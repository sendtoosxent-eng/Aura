import SwiftUI
import UIKit

// SCREEN 10, Account (redesigned)
// Full-bleed cover · profile card · Last visit + Health ID cards · grouped menu · logout · provider CTA
struct AccountView: View {
    @Environment(AppState.self) private var appState

    @State private var showEditProfile = false
    @State private var showComingSoon = false
    @State private var showRegistration = false
    @State private var showLogoutConfirm = false
    @State private var copiedHealthID = false

    private var initials: String {
        appState.userName.split(separator: " ").prefix(2).compactMap { $0.first }.map(String.init).joined()
    }

    var body: some View {
        ScrollView {
            VStack(spacing: 0) {
                // 1. Full-bleed cover (ignores the top safe area)
                coverHeaderSection

                // 2. Content
                VStack(spacing: Spacing.md) {
                    profileCard
                        .offset(y: -44)              // lifts the card over the cover
                        .padding(.bottom, -44)       // takes back the gap the offset leaves

                    // Only these two details, as cards
                    HStack(alignment: .top, spacing: Spacing.md) {
                        lastVisitCard
                        healthIDCard
                    }
                    .fixedSize(horizontal: false, vertical: true)

                    menuSection("Health") {
                        navRow(.medicalRecords, icon: "doc.text.fill", title: "Medical Records & Prescriptions")
                        rowDivider
                        navRow(.appointments, icon: "calendar.badge.clock", title: "My Appointments")
                        rowDivider
                        navRow(.reminders, icon: "pills.fill", title: "Medication & Reminders")
                        rowDivider
                        navRow(.emergencyHealthCard, icon: "staroflife.fill", title: "Emergency Health Card")
                    }

                    menuSection("Activity") {
                        navRow(.myReviews, icon: "square.and.pencil", title: "My Reviews")
                        rowDivider
                        navRow(.favorites, icon: "heart.fill", title: "Favorite Providers")
                        rowDivider
                        Button { showComingSoon = true } label: {
                            menuRow(icon: "creditcard.fill", title: "Payment & Insurance")
                        }
                        rowDivider
                        navRow(.notifications, icon: "bell.badge.fill", title: "Notifications")
                    }

                    menuSection("Account") {
                        navRow(.security, icon: "lock.fill", title: "Security & Privacy")
                        rowDivider
                        Button { showComingSoon = true } label: {
                            menuRow(icon: "questionmark.circle.fill", title: "Help & Support")
                        }
                    }

                    logoutCard

                    providerCTA

                    HStack(spacing: Spacing.md) {
                        Button("Privacy Policy") { showComingSoon = true }
                        Text("•")
                        Button("Terms of Service") { showComingSoon = true }
                    }
                    .font(AppFont.bodyMd)
                    .foregroundStyle(AppColor.outline)
                    .padding(.vertical, Spacing.md)
                }
                .padding(.horizontal, Spacing.md)
                .readableWidth()
            }
        }
        .background(AppColor.background.ignoresSafeArea())
        .ignoresSafeArea(edges: .top)
        .toolbar(.hidden, for: .navigationBar)
        .sheet(isPresented: $showEditProfile) {
            EditProfileSheet()
        }
        .alert("Coming soon", isPresented: $showComingSoon) {
            Button("OK", role: .cancel) {}
        } message: {
            Text("This feature is undergoing active development.")
        }
        .confirmationDialog(
            "Log out of \(AppConfig.brand)?",
            isPresented: $showLogoutConfirm,
            titleVisibility: .visible
        ) {
            Button("Log out", role: .destructive) { appState.logout() }
            Button("Cancel", role: .cancel) {}
        }
        .fullScreenCover(isPresented: $showRegistration) {
            ProviderRegistrationStep1View()
        }
    }

    // MARK: - Cover (fills the status-bar area)

    private var coverHeaderSection: some View {
        GeometryReader { geometry in
            ZStack {
                Image("WelcomeFacility")
                    .resizable()
                    .scaledToFill()
                    .frame(width: geometry.size.width, height: 210 + geometry.safeAreaInsets.top)
                    .clipped()
                    .overlay {
                        LinearGradient(
                            colors: [.black.opacity(0.55), .black.opacity(0.1), .black.opacity(0.5)],
                            startPoint: .top,
                            endPoint: .bottom
                        )
                    }
            }
            .offset(y: -geometry.safeAreaInsets.top)
        }
        .frame(height: 210)
        .ignoresSafeArea(edges: .top)
    }

    // MARK: - Profile card (name, verification, contact)

    private var profileCard: some View {
        VStack(alignment: .leading, spacing: Spacing.md) {
            HStack(alignment: .center, spacing: Spacing.md) {
                avatar

                VStack(alignment: .leading, spacing: 4) {
                    HStack(spacing: 6) {
                        Text(appState.userName)
                            .font(AppFont.headlineLg)
                            .foregroundStyle(AppColor.onSurface)
                            .lineLimit(2)
                            .fixedSize(horizontal: false, vertical: true)

                        Image(systemName: "checkmark.seal.fill")
                            .font(.subheadline.weight(.semibold))
                            .foregroundStyle(AppColor.primary)
                            .accessibilityLabel("Verified user")
                    }

                    Text("@\(appState.userHandle)")
                        .font(AppFont.labelSm)
                        .foregroundStyle(AppColor.onSurfaceVariant)

                    Label("Identity verified", systemImage: "checkmark.shield.fill")
                        .font(AppFont.labelSm)
                        .foregroundStyle(AppColor.primary)
                        .padding(.horizontal, 10)
                        .padding(.vertical, 4)
                        .background(AppColor.primaryTint, in: Capsule())
                        .padding(.top, 2)
                }

                Spacer(minLength: 0)
            }

            Divider()

            // Contact details, each on its own row with an icon tile
            VStack(alignment: .leading, spacing: Spacing.sm) {
                contactRow(icon: "phone.fill", text: appState.userPhone)
                if !appState.userEmail.isEmpty {
                    contactRow(icon: "envelope.fill", text: appState.userEmail)
                }
            }

            Button { showEditProfile = true } label: {
                Label("Edit profile", systemImage: "pencil")
                    .font(AppFont.labelLg)
                    .foregroundStyle(AppColor.primary)
                    .frame(maxWidth: .infinity, minHeight: 44)
                    .background(AppColor.primaryTint, in: RoundedRectangle(cornerRadius: Radius.sm))
            }
        }
        .padding(Spacing.md)
        .background(AppColor.surface, in: RoundedRectangle(cornerRadius: Radius.lg))
        .shadow(color: .black.opacity(0.08), radius: 10, x: 0, y: 4)
    }

    /// Photo from Assets if it exists, otherwise initials.
    private var avatar: some View {
        Group {
            if UIImage(named: "WelcomeCare") != nil {
                Image("WelcomeCare")
                    .resizable()
                    .scaledToFill()
            } else {
                Text(initials)
                    .font(.system(size: 28, weight: .bold))
                    .foregroundStyle(AppColor.primary)
                    .frame(maxWidth: .infinity, maxHeight: .infinity)
                    .background(AppColor.primaryFixed)
            }
        }
        .frame(width: 84, height: 84)
        .clipShape(Circle())
        .overlay(Circle().stroke(AppColor.surface, lineWidth: 4))
        .shadow(color: .black.opacity(0.12), radius: 8, x: 0, y: 4)
    }

    private func contactRow(icon: String, text: String) -> some View {
        HStack(spacing: Spacing.sm) {
            Image(systemName: icon)
                .font(.caption)
                .foregroundStyle(AppColor.primary)
                .frame(width: 28, height: 28)
                .background(AppColor.primaryTint, in: RoundedRectangle(cornerRadius: 6))
            Text(text)
                .font(AppFont.bodyMd)
                .foregroundStyle(AppColor.onSurface)
                .lineLimit(1)
                .minimumScaleFactor(0.8)
        }
    }

    // MARK: - Last visit + Health ID cards

    private var lastVisitCard: some View {
        NavigationLink(value: Route.medicalRecords) {
            detailCard(icon: "clock.arrow.circlepath", label: "Last visit",
                       value: "3 days ago", caption: "Mulago National Hospital") {
                Label("View record", systemImage: "chevron.right")
                    .labelStyle(TrailingIconLabelStyle())
                    .font(AppFont.labelSm)
                    .foregroundStyle(AppColor.primary)
            }
        }
        .buttonStyle(.plain)
    }

    private var healthIDCard: some View {
        detailCard(icon: "cross.case.fill", label: "Health ID",
                   value: appState.healthID, caption: "Share with your providers") {
            Button {
                UIPasteboard.general.string = appState.healthID
                withAnimation { copiedHealthID = true }
                Task {
                    try? await Task.sleep(for: .seconds(1.5))
                    withAnimation { copiedHealthID = false }
                }
            } label: {
                Label(copiedHealthID ? "Copied" : "Copy",
                      systemImage: copiedHealthID ? "checkmark" : "doc.on.doc")
                    .font(AppFont.labelSm)
                    .foregroundStyle(copiedHealthID ? AppColor.success : AppColor.primary)
                    .frame(minHeight: 28)
            }
            .accessibilityLabel("Copy Health ID")
        }
    }

    // LEARN: `@ViewBuilder action: () -> Action` lets each card plug in its own
    // bottom row (a link on one, a Copy button on the other) while sharing one layout.
    private func detailCard<Action: View>(icon: String, label: String, value: String, caption: String,
                                          @ViewBuilder action: () -> Action) -> some View {
        VStack(alignment: .leading, spacing: Spacing.sm) {
            Image(systemName: icon)
                .font(.title3)
                .foregroundStyle(AppColor.primary)
                .frame(width: 40, height: 40)
                .background(AppColor.primaryTint, in: RoundedRectangle(cornerRadius: Radius.sm))

            VStack(alignment: .leading, spacing: 2) {
                Text(label)
                    .font(AppFont.labelSm)
                    .foregroundStyle(AppColor.onSurfaceVariant)
                Text(value)
                    .font(AppFont.headlineMd)
                    .foregroundStyle(AppColor.onSurface)
                    .lineLimit(1)
                    .minimumScaleFactor(0.7)
                Text(caption)
                    .font(AppFont.labelSm)
                    .foregroundStyle(AppColor.secondary)
                    .lineLimit(1)
                    .minimumScaleFactor(0.8)
            }

            Spacer(minLength: 0)
            action()
        }
        .padding(Spacing.md)
        .frame(maxWidth: .infinity, maxHeight: .infinity, alignment: .topLeading)
        .cardStyle(radius: Radius.lg)
    }

    // MARK: - Grouped menu

    // LEARN: a generic helper takes the rows as a @ViewBuilder closure, so every
    // section gets the same title + card styling without repeating it.
    private func menuSection<Content: View>(_ title: String,
                                            @ViewBuilder content: () -> Content) -> some View {
        VStack(alignment: .leading, spacing: Spacing.sm) {
            Text(title.uppercased())
                .font(AppFont.labelSm)
                .tracking(1)
                .foregroundStyle(AppColor.onSurfaceVariant)
                .padding(.leading, Spacing.xs)

            VStack(spacing: 0) { content() }
                .buttonStyle(.plain)
                .cardStyle(radius: Radius.lg)
        }
    }

    private var rowDivider: some View {
        Divider().padding(.leading, 64)
    }

    private func navRow(_ route: Route, icon: String, title: String) -> some View {
        NavigationLink(value: route) {
            menuRow(icon: icon, title: title)
        }
    }

    private func menuRow(icon: String, title: String, isDestructive: Bool = false) -> some View {
        HStack(spacing: Spacing.md) {
            Image(systemName: icon)
                .font(.body)
                .foregroundStyle(AppColor.primary)
                .frame(width: 36, height: 36)
                .background(isDestructive ? Color.white : AppColor.primaryTint,
                            in: RoundedRectangle(cornerRadius: Radius.sm))
            Text(title)
                .font(AppFont.labelLg)
                .foregroundStyle(isDestructive ? AppColor.primary : AppColor.onSurface)
                .multilineTextAlignment(.leading)
            Spacer()
            Image(systemName: "chevron.right")
                .font(.caption.weight(.bold))
                .foregroundStyle(AppColor.outline)
        }
        .padding(.horizontal, Spacing.md)
        .frame(minHeight: 60)
        .contentShape(Rectangle())
    }

    private var logoutCard: some View {
        Button { showLogoutConfirm = true } label: {
            menuRow(icon: "rectangle.portrait.and.arrow.right", title: "Logout", isDestructive: true)
        }
        .buttonStyle(.plain)
        .background(AppColor.primaryTint, in: RoundedRectangle(cornerRadius: Radius.lg))
        .overlay(RoundedRectangle(cornerRadius: Radius.lg).stroke(AppColor.outlineVariant, lineWidth: 1))
    }

    // MARK: - Provider CTA

    private var providerCTA: some View {
        VStack(spacing: Spacing.sm) {
            Image(systemName: "cross.case.fill")
                .font(.title2)
                .foregroundStyle(.white)
                .frame(width: 56, height: 56)
                .background(AppColor.primary)
                .clipShape(Circle())

            Text("Are you a healthcare professional?")
                .font(AppFont.headlineLg)
                .foregroundStyle(AppColor.primary)
                .multilineTextAlignment(.center)

            Text("Join our network to reach more patients and manage your practice efficiently.")
                .font(AppFont.bodyLg)
                .foregroundStyle(AppColor.onSurfaceVariant)
                .multilineTextAlignment(.center)

            PrimaryButton(title: "Become a Health Provider") {
                showRegistration = true
            }
            .padding(.top, Spacing.sm)
        }
        .padding(Spacing.lg)
        .frame(maxWidth: .infinity)
        .background(AppColor.primaryTint)
        .clipShape(RoundedRectangle(cornerRadius: Radius.lg))
        .overlay(RoundedRectangle(cornerRadius: Radius.lg).stroke(AppColor.outlineVariant, lineWidth: 1))
    }
}

/// Puts the icon AFTER the text, e.g. "View record ›".
private struct TrailingIconLabelStyle: LabelStyle {
    func makeBody(configuration: Configuration) -> some View {
        HStack(spacing: 4) {
            configuration.title
            configuration.icon.font(.caption2.weight(.bold))
        }
    }
}

// MARK: - Edit Profile Sheet

struct EditProfileSheet: View {
    @Environment(AppState.self) private var appState
    @Environment(\.dismiss) private var dismiss

    @State private var name: String = ""
    @State private var handle: String = ""
    @State private var phone: String = ""
    @State private var healthID: String = ""

    var body: some View {
        NavigationStack {
            Form {
                Section("Personal Details") {
                    HStack {
                        Text("Full Name")
                            .font(AppFont.labelLg)
                        Spacer()
                        TextField("Enter name", text: $name)
                            .multilineTextAlignment(.trailing)
                    }

                    HStack {
                        Text("Phone Number")
                            .font(AppFont.labelLg)
                        Spacer()
                        TextField("Enter phone", text: $phone)
                            .keyboardType(.phonePad)
                            .multilineTextAlignment(.trailing)
                    }

                    HStack {
                        Text("Username")
                            .font(AppFont.labelLg)
                        Spacer()
                        TextField("Username", text: $handle)
                            .textInputAutocapitalization(.never)
                            .autocorrectionDisabled()
                            .multilineTextAlignment(.trailing)
                    }

                    HStack {
                        Text("Health ID")
                            .font(AppFont.labelLg)
                        Spacer()
                        TextField("Enter health ID", text: $healthID)
                            .multilineTextAlignment(.trailing)
                    }
                }
            }
            .navigationTitle("Edit Profile")
            .navigationBarTitleDisplayMode(.inline)
            .toolbar {
                ToolbarItem(placement: .cancellationAction) {
                    Button("Cancel") { dismiss() }
                }
                ToolbarItem(placement: .confirmationAction) {
                    Button("Save") {
                        appState.userName = name
                        appState.userHandle = handle
                        appState.userPhone = phone
                        appState.healthID = healthID
                        dismiss()
                    }
                    .font(.body.weight(.bold))
                    .foregroundStyle(AppColor.primary)
                }
            }
            .onAppear {
                name = appState.userName
                handle = appState.userHandle
                phone = appState.userPhone
                healthID = appState.healthID
            }
        }
    }
}

#Preview {
    NavigationStack { AccountView().appDestinations() }
        .environment(AppState())
}
