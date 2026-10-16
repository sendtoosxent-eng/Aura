import SwiftUI
import PhotosUI
import QuickLook
import UniformTypeIdentifiers
import UIKit

// MARK: - Conversation list

struct MessagesView: View {
    @Environment(AppState.self) private var appState
    @State private var searchText = ""

    private var visibleThreads: [MessageThread] {
        guard !searchText.trimmingCharacters(in: .whitespaces).isEmpty else {
            return appState.messageThreads
        }
        return appState.messageThreads.filter {
            $0.participantName.localizedCaseInsensitiveContains(searchText)
            || $0.subtitle.localizedCaseInsensitiveContains(searchText)
            || ($0.lastMessage?.text.localizedCaseInsensitiveContains(searchText) ?? false)
        }
    }

    var body: some View {
        VStack(spacing: 0) {
            AppHeader(title: "Messages", leadingSymbol: "message.fill")

            HStack(spacing: Spacing.sm) {
                Image(systemName: "magnifyingglass")
                    .foregroundStyle(AppColor.onSurfaceVariant)
                TextField("Search conversations", text: $searchText)
                    .textInputAutocapitalization(.never)
            }
            .inputBox()
            .padding(Spacing.md)

            if visibleThreads.isEmpty {
                ContentUnavailableView(
                    "No conversations",
                    systemImage: "message",
                    description: Text("Start a conversation from a provider's profile.")
                )
            } else {
                List {
                    let reminders = visibleThreads.filter(\.isAutomated)
                    let providers = visibleThreads.filter { !$0.isAutomated }

                    if !reminders.isEmpty {
                        Section("Care reminders") {
                            ForEach(reminders) { threadRow($0) }
                        }
                    }

                    if !providers.isEmpty {
                        Section("Healthcare providers") {
                            ForEach(providers) { threadRow($0) }
                        }
                    }
                }
                .listStyle(.plain)
                .scrollContentBackground(.hidden)
            }
        }
        .background(AppColor.background.ignoresSafeArea())
        .toolbar(.hidden, for: .navigationBar)
        .task { await appState.loadMobileData() }
    }

    private func threadRow(_ thread: MessageThread) -> some View {
        let person = ChatParticipant.from(thread, name: thread.participantName)

        return NavigationLink(value: Route.chat(thread.participantName)) {
            HStack(spacing: Spacing.md) {
                AvatarView(name: person.name, assetName: person.assetName, symbol: person.symbol,
                           size: 52, showsActiveDot: person.isActive)

                VStack(alignment: .leading, spacing: 4) {
                    HStack(spacing: 4) {
                        Text(thread.participantName)
                            .font(AppFont.labelLg)
                            .foregroundStyle(AppColor.onSurface)
                            .lineLimit(1)

                        if person.isVerified && !person.isAutomated {
                            Image(systemName: "checkmark.seal.fill")
                                .font(.caption)
                                .foregroundStyle(AppColor.primary)
                                .accessibilityLabel("Verified")
                        }
                        if thread.isAutomated {
                            Text("AUTOMATED")
                                .font(.system(size: 9, weight: .bold))
                                .foregroundStyle(AppColor.primary)
                                .padding(.horizontal, 6)
                                .padding(.vertical, 3)
                                .background(AppColor.primaryTint, in: Capsule())
                        }
                    }

                    HStack(spacing: 4) {
                        Text(thread.subtitle)
                            .lineLimit(1)
                        if !person.isAutomated {
                            Text("• " + person.statusText)
                                .lineLimit(1)
                                .foregroundStyle(person.isActive ? AppColor.success : AppColor.onSurfaceVariant)
                        }
                    }
                    .font(AppFont.labelSm)
                    .foregroundStyle(AppColor.onSurfaceVariant)

                    Text(thread.lastMessage?.text ?? "Start a conversation")
                        .font(AppFont.bodyMd)
                        .foregroundStyle(AppColor.onSurfaceVariant)
                        .lineLimit(2)
                }

                Spacer(minLength: Spacing.xs)

                if thread.unreadCount > 0 {
                    Text("\(thread.unreadCount)")
                        .font(.caption.bold())
                        .foregroundStyle(.white)
                        .frame(minWidth: 24, minHeight: 24)
                        .background(AppColor.primary, in: Circle())
                }
            }
            .padding(.vertical, Spacing.xs)
        }
    }
}

// MARK: - Conversation

struct ConversationView: View {
    let participantName: String

    @Environment(AppState.self) private var appState

    @State private var draft = ""
    @State private var recorder = VoiceRecorder()
    @State private var player = VoicePlayer()

    @State private var showPhotoPicker = false
    @State private var photoItem: PhotosPickerItem?
    @State private var showFileImporter = false
    @State private var previewURL: URL?
    @State private var alertMessage: String?
    @State private var showMicDenied = false

    private var thread: MessageThread? { appState.thread(named: participantName) }
    private var messages: [ChatMessage] { thread?.messages ?? [] }
    private var participant: ChatParticipant { .from(thread, name: participantName) }
    private var canSendText: Bool { !draft.trimmingCharacters(in: .whitespacesAndNewlines).isEmpty }

    var body: some View {
        VStack(spacing: 0) {
            if let thread, thread.isAutomated {
                Label("Automated reminders are not emergency medical advice.", systemImage: "info.circle")
                    .font(AppFont.labelSm)
                    .foregroundStyle(AppColor.onSurfaceVariant)
                    .padding(Spacing.sm)
                    .frame(maxWidth: .infinity)
                    .background(AppColor.primaryTint)
            }

            ScrollViewReader { proxy in
                ScrollView {
                    LazyVStack(spacing: Spacing.md) {
                        if messages.isEmpty { welcomeMessage }

                        ForEach(messages) { message in
                            messageRow(message)
                                .id(message.id)
                        }
                    }
                    .padding(Spacing.md)
                }
                .scrollDismissesKeyboard(.interactively)
                .onChange(of: messages.count) { _, _ in
                    if let lastID = messages.last?.id {
                        withAnimation { proxy.scrollTo(lastID, anchor: .bottom) }
                    }
                }
                .onAppear {
                    if let lastID = messages.last?.id {
                        proxy.scrollTo(lastID, anchor: .bottom)
                    }
                }
            }
        }
        .background(AppColor.background.ignoresSafeArea())
        .navigationBarTitleDisplayMode(.inline)
        .toolbar { ToolbarItem(placement: .principal) { chatHeader } }
        .tint(AppColor.primary)
        .safeAreaInset(edge: .bottom, spacing: 0) { composer }
        .onAppear { appState.markThreadRead(participantName) }
        .onDisappear { player.stop(); recorder.cancel() }

        // Photo picker (opened from the + menu or the photo icon in the field)
        .photosPicker(isPresented: $showPhotoPicker, selection: $photoItem, matching: .images)
        .onChange(of: photoItem) { _, item in
            guard let item else { return }
            Task {
                if let data = try? await item.loadTransferable(type: Data.self),
                   let image = UIImage(data: data) {
                    send(.image(image), caption: "Photo")
                } else {
                    alertMessage = "Could not load that photo."
                }
                photoItem = nil
            }
        }
        // Document picker
        .fileImporter(isPresented: $showFileImporter, allowedContentTypes: [.data],
                      allowsMultipleSelection: false) { result in
            if case .success(let urls) = result, let url = urls.first { importDocument(url) }
        }
        .quickLookPreview($previewURL)                          // tap a document → preview
        // Alerts
        .onChange(of: recorder.permissionDenied) { _, denied in
            if denied { showMicDenied = true; recorder.permissionDenied = false }
        }
        .alert("Microphone is off", isPresented: $showMicDenied) {
            Button("Open Settings") {
                if let url = URL(string: UIApplication.openSettingsURLString) { UIApplication.shared.open(url) }
            }
            Button("Cancel", role: .cancel) {}
        } message: {
            Text("Allow microphone access in Settings to send voice messages.")
        }
        .alert("Attachment",
               isPresented: Binding(get: { alertMessage != nil },
                                    set: { if !$0 { alertMessage = nil } })) {
            Button("OK", role: .cancel) {}
        } message: {
            Text(alertMessage ?? "")
        }
    }

    // MARK: Header: avatar, name, red verified tick, status

    private var chatHeader: some View {
        HStack(spacing: Spacing.sm) {
            AvatarView(name: participant.name, assetName: participant.assetName,
                       symbol: participant.symbol, size: 36, showsActiveDot: participant.isActive)

            VStack(alignment: .leading, spacing: 1) {
                HStack(spacing: 4) {
                    Text(participant.name)
                        .font(AppFont.labelLg)
                        .foregroundStyle(AppColor.onSurface)
                        .lineLimit(1)
                    if participant.isVerified {
                        Image(systemName: "checkmark.seal.fill")
                            .font(.caption)
                            .foregroundStyle(AppColor.primary)
                            .accessibilityLabel("Verified")
                    }
                }
                Text(participant.statusText)
                    .font(AppFont.labelSm)
                    .foregroundStyle(participant.isActive ? AppColor.success : AppColor.onSurfaceVariant)
                    .lineLimit(1)
            }
        }
        .accessibilityElement(children: .combine)
    }

    private var welcomeMessage: some View {
        VStack(spacing: Spacing.sm) {
            Image(systemName: "message.badge.waveform.fill")
                .font(.largeTitle)
                .foregroundStyle(AppColor.primary)
            Text("Start a conversation with \(participantName)")
                .font(AppFont.headlineMd)
                .multilineTextAlignment(.center)
            Text("Do not use chat for an emergency. Use the SOS button or call emergency services.")
                .font(AppFont.bodyMd)
                .foregroundStyle(AppColor.onSurfaceVariant)
                .multilineTextAlignment(.center)
        }
        .padding(.vertical, Spacing.xl)
    }

    // MARK: Message row: [avatar] bubble ... / ... bubble [avatar]

    private func messageRow(_ message: ChatMessage) -> some View {
        let fromUser = appState.currentRole == .provider
            ? message.author == .provider
            : message.author == .user

        return HStack(alignment: .bottom, spacing: Spacing.sm) {
            if fromUser { Spacer(minLength: 48) } else { avatar(fromUser: false) }

            bubble(message, fromUser: fromUser)

            if fromUser { avatar(fromUser: true) } else { Spacer(minLength: 48) }
        }
        .frame(maxWidth: .infinity, alignment: fromUser ? .trailing : .leading)
    }

    @ViewBuilder
    private func avatar(fromUser: Bool) -> some View {
        if fromUser {
            // Add an image named "me" to Assets to show your own photo.
            AvatarView(name: appState.userName, assetName: "me", size: 32)
        } else {
            AvatarView(name: participant.name, assetName: participant.assetName,
                       symbol: participant.symbol, size: 32)
        }
    }

    @ViewBuilder
    private func bubble(_ message: ChatMessage, fromUser: Bool) -> some View {
        if let attachment = appState.attachments[message.id] {
            switch attachment {
            case .image(let image):
                imageBubble(image, time: message.timeText, fromUser: fromUser)
            case .document(let name, let sizeText, let url):
                documentBubble(name: name, sizeText: sizeText, url: url,
                               time: message.timeText, fromUser: fromUser)
            case .voice(let url, let duration):
                voiceBubble(id: message.id, url: url, duration: duration,
                            time: message.timeText, fromUser: fromUser)
            }
        } else {
            textBubble(message, fromUser: fromUser)
        }
    }

    // MARK: Bubble pieces

    /// Rounded bubble with a small "tail" corner on the avatar side.
    private func bubbleShape(_ fromUser: Bool) -> UnevenRoundedRectangle {
        UnevenRoundedRectangle(
            topLeadingRadius: Radius.lg,
            bottomLeadingRadius: fromUser ? Radius.lg : 4,
            bottomTrailingRadius: fromUser ? 4 : Radius.lg,
            topTrailingRadius: Radius.lg
        )
    }

    private func shell<Content: View>(fromUser: Bool, @ViewBuilder _ content: () -> Content) -> some View {
        content()
            .padding(.horizontal, 14)
            .padding(.vertical, 10)
            .background(fromUser ? AppColor.primary : AppColor.surface)
            .clipShape(bubbleShape(fromUser))
            .overlay(
                bubbleShape(fromUser)
                    .stroke(fromUser ? Color.clear : AppColor.outlineVariant.opacity(0.6), lineWidth: 1)
            )
    }

    private func subColor(_ fromUser: Bool) -> Color {
        fromUser ? Color.white.opacity(0.75) : AppColor.onSurfaceVariant
    }

    private func textBubble(_ message: ChatMessage, fromUser: Bool) -> some View {
        shell(fromUser: fromUser) {
            VStack(alignment: fromUser ? .trailing : .leading, spacing: 2) {
                if message.author == .automated {
                    Label("Automated reminder", systemImage: "gearshape.2")
                        .font(.caption2)
                        .foregroundStyle(subColor(fromUser))
                }
                Text(message.text)
                    .font(AppFont.bodyLg)
                    .foregroundStyle(fromUser ? Color.white : AppColor.onSurface)
                    .multilineTextAlignment(.leading)
                Text(message.timeText)
                    .font(.caption2)
                    .foregroundStyle(subColor(fromUser))
            }
        }
    }

    private func imageBubble(_ image: UIImage, time: String, fromUser: Bool) -> some View {
        Image(uiImage: image)
            .resizable()
            .scaledToFit()
            .frame(maxWidth: 240, maxHeight: 300)
            .clipShape(bubbleShape(fromUser))
            .overlay(alignment: .bottomTrailing) {
                Text(time)
                    .font(.caption2)
                    .foregroundStyle(.white)
                    .padding(.horizontal, 6)
                    .padding(.vertical, 3)
                    .background(Color.black.opacity(0.45), in: Capsule())
                    .padding(6)
            }
    }

    private func documentBubble(name: String, sizeText: String, url: URL,
                                time: String, fromUser: Bool) -> some View {
        Button { previewURL = url } label: {
            shell(fromUser: fromUser) {
                VStack(alignment: fromUser ? .trailing : .leading, spacing: 4) {
                    HStack(spacing: Spacing.sm) {
                        Image(systemName: "doc.fill")
                            .font(.title2)
                            .foregroundStyle(fromUser ? Color.white : AppColor.primary)
                            .frame(width: 40, height: 40)
                            .background(fromUser ? Color.white.opacity(0.2) : AppColor.primaryTint)
                            .clipShape(RoundedRectangle(cornerRadius: Radius.sm))

                        VStack(alignment: .leading, spacing: 2) {
                            Text(name)
                                .font(AppFont.labelLg)
                                .foregroundStyle(fromUser ? Color.white : AppColor.onSurface)
                                .lineLimit(1)
                                .truncationMode(.middle)
                            Text(sizeText)
                                .font(AppFont.labelSm)
                                .foregroundStyle(subColor(fromUser))
                        }
                    }
                    Text(time).font(.caption2).foregroundStyle(subColor(fromUser))
                }
            }
        }
        .buttonStyle(.plain)
    }

    private func voiceBubble(id: AnyHashable, url: URL, duration: TimeInterval,
                             time: String, fromUser: Bool) -> some View {
        let isPlaying = player.playingID == id
        let bars = waveform(for: id)

        return shell(fromUser: fromUser) {
            VStack(alignment: fromUser ? .trailing : .leading, spacing: 4) {
                HStack(spacing: Spacing.sm) {
                    Button { player.toggle(id: id, url: url) } label: {
                        Image(systemName: isPlaying ? "pause.fill" : "play.fill")
                            .foregroundStyle(fromUser ? AppColor.primary : Color.white)
                            .frame(width: 36, height: 36)
                            .background(fromUser ? Color.white : AppColor.primary)
                            .clipShape(Circle())
                    }
                    .accessibilityLabel(isPlaying ? "Pause voice message" : "Play voice message")

                    // Bars fill in as the audio plays. Width flexes with the screen.
                    HStack(alignment: .center, spacing: 2) {
                        ForEach(bars.indices, id: \.self) { i in
                            let played = isPlaying && Double(i) / Double(bars.count) < player.progress
                            Capsule()
                                .fill((fromUser ? Color.white : AppColor.primary).opacity(played ? 1 : 0.4))
                                .frame(height: bars[i])
                        }
                    }
                    .frame(minWidth: 80, maxWidth: 160)
                    .frame(height: 28)

                    Text(clock(duration))
                        .font(AppFont.labelSm)
                        .foregroundStyle(subColor(fromUser))
                }
                Text(time).font(.caption2).foregroundStyle(subColor(fromUser))
            }
        }
    }

    /// Fake-but-stable waveform: the same message always draws the same bars.
    private func waveform(for id: AnyHashable) -> [CGFloat] {
        let bytes = Array("\(id)".utf8)
        guard !bytes.isEmpty else { return Array(repeating: 10, count: 24) }
        return (0..<24).map { i in CGFloat(6 + Int(bytes[i % bytes.count]) % 20) }
    }

    private func clock(_ seconds: TimeInterval) -> String {
        String(format: "%d:%02d", Int(seconds) / 60, Int(seconds) % 60)
    }

    // MARK: Composer

    private var composer: some View {
        Group {
            if recorder.isRecording { recordingBar } else { inputBar }
        }
        .padding(.horizontal, Spacing.md)
        .padding(.vertical, Spacing.sm)
        .background(
            AppColor.surface
                .shadow(color: .black.opacity(0.08), radius: 8, y: -2)
                .ignoresSafeArea(edges: .bottom)
        )
    }

    private var inputBar: some View {
        HStack(alignment: .bottom, spacing: Spacing.sm) {
            // + opens the attachment menu
            Menu {
                Button { showFileImporter = true } label: {
                    Label("Document", systemImage: "doc.fill")
                }
                Button { showPhotoPicker = true } label: {
                    Label("Photo", systemImage: "photo")
                }
            } label: {
                Image(systemName: "plus")
                    .font(.title3.weight(.semibold))
                    .foregroundStyle(AppColor.primary)
                    .frame(width: 48, height: 48)
                    .background(AppColor.primaryTint)
                    .clipShape(Circle())
            }
            .accessibilityLabel("Attach")

            // Text field with a photo shortcut inside it
            HStack(alignment: .bottom, spacing: Spacing.xs) {
                TextField("Write a message", text: $draft, axis: .vertical)
                    .lineLimit(1...4)
                    .padding(.vertical, 6)

                Button { showPhotoPicker = true } label: {
                    Image(systemName: "photo")
                        .foregroundStyle(AppColor.outline)
                        .frame(width: 36, height: 36)
                }
                .accessibilityLabel("Send a photo")
            }
            .inputBox()

            // Paper plane when there's text, microphone when empty
            if canSendText {
                roundButton("paperplane.fill", label: "Send message", action: sendText)
            } else {
                roundButton("mic.fill", label: "Record a voice message") {
                    player.stop()
                    recorder.start()
                }
            }
        }
    }

    private var recordingBar: some View {
        HStack(spacing: Spacing.sm) {
            Button { recorder.cancel() } label: {
                Image(systemName: "trash")
                    .foregroundStyle(AppColor.primary)
                    .frame(width: 48, height: 48)
            }
            .accessibilityLabel("Discard recording")

            HStack(spacing: Spacing.sm) {
                Image(systemName: "circle.fill")
                    .font(.caption)
                    .foregroundStyle(AppColor.primaryContainer)
                    .symbolEffect(.pulse, isActive: true)
                Text(clock(recorder.elapsed))
                    .font(.system(.body, design: .monospaced))
                    .foregroundStyle(AppColor.onSurface)
                Text("Recording…")
                    .font(AppFont.bodyMd)
                    .foregroundStyle(AppColor.onSurfaceVariant)
                Spacer()
            }
            .inputBox()

            roundButton("paperplane.fill", label: "Send voice message", action: finishRecording)
        }
    }

    private func roundButton(_ symbol: String, label: String, action: @escaping () -> Void) -> some View {
        Button(action: action) {
            Image(systemName: symbol)
                .foregroundStyle(.white)
                .frame(width: 48, height: 48)
                .background(AppColor.primary)
                .clipShape(Circle())
        }
        .accessibilityLabel(label)
    }

    // MARK: Sending

    private func send(_ attachment: ChatAttachment, caption: String) {
        appState.sendAttachment(attachment, caption: caption, to: participantName)
    }

    private func sendText() {
        let message = draft
        Task {
            do {
                try await appState.sendMessage(message, to: participantName)
                draft = ""
            } catch {
                alertMessage = error.localizedDescription
            }
        }
    }

    private func finishRecording() {
        guard let result = recorder.stop() else { return }
        send(.voice(url: result.url, duration: result.duration),
             caption: "Voice message · \(clock(result.duration))")
    }

    private func importDocument(_ source: URL) {
        // Files from the picker are "security scoped": ask for access, copy, release.
        let granted = source.startAccessingSecurityScopedResource()
        defer { if granted { source.stopAccessingSecurityScopedResource() } }

        do {
            let size = try source.resourceValues(forKeys: [.fileSizeKey]).fileSize ?? 0
            guard size <= 10_000_000 else {
                alertMessage = "Files must be smaller than 10 MB."
                return
            }
            let folder = FileManager.default.temporaryDirectory
                .appendingPathComponent(UUID().uuidString, isDirectory: true)
            try FileManager.default.createDirectory(at: folder, withIntermediateDirectories: true)
            let copy = folder.appendingPathComponent(source.lastPathComponent)
            try FileManager.default.copyItem(at: source, to: copy)

            let sizeText = ByteCountFormatter.string(fromByteCount: Int64(size), countStyle: .file)
            send(.document(name: source.lastPathComponent, sizeText: sizeText, url: copy),
                 caption: "Document: \(source.lastPathComponent)")
        } catch {
            alertMessage = "Could not attach that file."
        }
    }
}

#Preview {
    NavigationStack { MessagesView().appDestinations() }
        .environment(AppState())
}
