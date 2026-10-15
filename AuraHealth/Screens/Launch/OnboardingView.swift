import SwiftUI

struct OnboardingView: View {
    @Environment(AppState.self) private var appState
    @Environment(\.accessibilityReduceMotion) private var reduceMotion
    @Environment(\.dynamicTypeSize) private var dynamicType
    @AppStorage("hasSeenOnboarding") private var hasSeenOnboarding = false
    @AppStorage("preferredLanguage") private var languageCode = WelcomeLanguage.deviceDefault
    @State private var index = 0
    @State private var showLanguages = false
    @State private var showPermissions = false

    private var language: WelcomeLanguage { WelcomeLanguage(rawValue: languageCode) ?? .english }

    var body: some View {
        GeometryReader { geometry in
            let photoHeight = dynamicType.isAccessibilitySize ? 210.0 : max(280, geometry.size.height * 0.52)
            VStack(spacing: 0) {
                TabView(selection: $index) {
                    ForEach(0..<3) { pageIndex in
                        page(language.pages[pageIndex], height: photoHeight)
                            .tag(pageIndex)
                    }
                }
                .tabViewStyle(.page(indexDisplayMode: .never))
                footer
            }
            .frame(maxWidth: .infinity)
            .background(alignment: .top) {
                // Keep the cover outside the pager and scroll view: their safe-area
                // insets must never leave an empty strip behind the status bar.
                Image(language.pages[index].image)
                    .resizable()
                    .scaledToFill()
                    .frame(width: geometry.size.width,
                           height: photoHeight + geometry.safeAreaInsets.top)
                    .clipped()
                    .overlay {
                        LinearGradient(colors: [.black.opacity(0.15), .clear, .black.opacity(0.48)],
                                       startPoint: .top, endPoint: .bottom)
                    }
                    .clipShape(UnevenRoundedRectangle(bottomLeadingRadius: 32, bottomTrailingRadius: 32))
                    .offset(y: -geometry.safeAreaInsets.top)
                    .accessibilityHidden(true)
            }
            .overlay(alignment: .top) {
                header
            }
        }
        .background(AppColor.background.ignoresSafeArea())
        .toolbar(.hidden, for: .navigationBar)
        .sheet(isPresented: $showLanguages) { languageSheet }
        .fullScreenCover(isPresented: $showPermissions) {
            WelcomePermissionsView {
                hasSeenOnboarding = true
                appState.stage = .auth
            }
        }
    }

    private var header: some View {
        HStack(spacing: 12) {
            Button { showLanguages = true } label: {
                HStack(spacing: 7) {
                    Image(systemName: "globe")
                    Text(language.name)
                    Image(systemName: "chevron.down").font(.caption2.weight(.bold))
                }
                .font(.subheadline.weight(.semibold))
                .foregroundStyle(Color(hex: 0xB71C1C))
                .padding(.horizontal, 14).frame(minHeight: 44)
                .background(.white, in: Capsule())
                .overlay(Capsule().stroke(AppColor.outlineVariant, lineWidth: 1))
            }
            .accessibilityLabel(language.choose)
            .accessibilityValue(language.name)
            Spacer(minLength: 8)
            Button(language.skip, action: finish)
                .font(.subheadline.weight(.semibold))
                .foregroundStyle(Color(hex: 0xB71C1C))
                .padding(.horizontal, 16)
                .frame(minWidth: 44, minHeight: 44)
                .background(.white.opacity(0.95), in: Capsule())
        }
        .padding(.horizontal, 24).padding(.vertical, 12)
    }

    private func page(_ page: WelcomePage, height: CGFloat) -> some View {
        ScrollView {
            VStack(alignment: .leading, spacing: 24) {
                ZStack(alignment: .bottomLeading) {
                    Color.clear
                    HStack(spacing: 10) {
                        Image(systemName: page.symbol).font(.body.weight(.semibold))
                            .foregroundStyle(page.accent)
                            .frame(width: 38, height: 38)
                            .background(.white, in: Circle())
                        Text(page.badge).font(.subheadline.weight(.semibold))
                            .foregroundStyle(.white)
                            .fixedSize(horizontal: false, vertical: true)
                    }
                    .padding(20)
                }
                .frame(height: height)
                .clipShape(UnevenRoundedRectangle(bottomLeadingRadius: 32, bottomTrailingRadius: 32))
                .accessibilityElement(children: .ignore)
                .accessibilityLabel(page.badge)

                VStack(alignment: .leading, spacing: 12) {
                    Text(page.eyebrow).font(.caption.weight(.bold)).tracking(1.8)
                        .foregroundStyle(page.accent)
                    Text(page.title)
                        .font(.system(.largeTitle, design: .rounded, weight: .bold)).tracking(-0.8)
                        .foregroundStyle(AppColor.onSurface)
                        .fixedSize(horizontal: false, vertical: true)
                        .accessibilityAddTraits(.isHeader)
                    Text(page.detail).font(.body).lineSpacing(4)
                        .foregroundStyle(AppColor.onSurfaceVariant)
                        .fixedSize(horizontal: false, vertical: true)
                }
                .padding(.horizontal, 24)
                .frame(maxWidth: 620)
                .frame(maxWidth: .infinity)
                .background(AppColor.background)
            }
            .padding(.bottom, 16)
        }
        .scrollIndicators(.hidden)
        .scrollClipDisabled()
    }

    private var footer: some View {
        VStack(spacing: 18) {
            HStack(spacing: 8) {
                ForEach(0..<3) { pageIndex in
                    Capsule()
                        .fill(pageIndex == index ? AppColor.primary : AppColor.outlineVariant)
                        .frame(width: pageIndex == index ? 28 : 7, height: 7)
                }
            }
            .accessibilityElement(children: .ignore)
            .accessibilityLabel(language.progress(index + 1))
            HStack(spacing: 12) {
                if index > 0 {
                    Button { changePage(index - 1) } label: {
                        Image(systemName: "arrow.left").font(.body.weight(.semibold))
                            .frame(width: 56, height: 56)
                            .background(AppColor.surface, in: RoundedRectangle(cornerRadius: 18))
                            .overlay(RoundedRectangle(cornerRadius: 18).stroke(AppColor.outlineVariant))
                    }
                    .foregroundStyle(AppColor.onSurface)
                    .accessibilityLabel(language.back)
                }
                Button {
                    if index == 2 { finish() } else { changePage(index + 1) }
                } label: {
                    HStack {
                        Spacer(minLength: 0)
                        Text(index == 2 ? language.start : language.next)
                            .fixedSize(horizontal: false, vertical: true)
                        Spacer(minLength: 0)
                        Image(systemName: "arrow.right")
                    }
                    .font(.body.weight(.semibold)).foregroundStyle(.white)
                    .padding(.horizontal, 20).padding(.vertical, 18)
                    .frame(minHeight: 56)
                    .background(
                        LinearGradient(colors: [AppColor.primary, AppColor.primaryContainer], startPoint: .leading, endPoint: .trailing),
                        in: RoundedRectangle(cornerRadius: 18)
                    )
                }
            }
        }
        .padding(.horizontal, 24).padding(.top, 12).padding(.bottom, 20)
        .frame(maxWidth: 620)
        .frame(maxWidth: .infinity)
    }

    private var languageSheet: some View {
        NavigationStack {
            List {
                ForEach(WelcomeLanguage.allCases) { option in
                    Button {
                        languageCode = option.rawValue
                        showLanguages = false
                    } label: {
                        HStack {
                            Text(option.name).foregroundStyle(AppColor.onSurface)
                            Spacer()
                            if option == language {
                                Image(systemName: "checkmark.circle.fill").foregroundStyle(AppColor.success)
                            }
                        }
                        .padding(.vertical, 10)
                    }
                    .accessibilityAddTraits(option == language ? .isSelected : [])
                }
            }
            .navigationTitle(language.choose)
            .navigationBarTitleDisplayMode(.inline)
            .toolbar {
                ToolbarItem(placement: .confirmationAction) {
                    Button(language.done) { showLanguages = false }.tint(AppColor.primary)
                }
            }
        }
        .presentationDetents([.medium, .large])
        .presentationDragIndicator(.visible)
    }

    private func changePage(_ next: Int) {
        withAnimation(reduceMotion ? nil : .easeInOut(duration: 0.25)) { index = next }
    }

    private func finish() {
        showPermissions = true
    }
}

#Preview("Onboarding") {
    OnboardingView().environment(AppState())
}

#Preview("Larger text") {
    OnboardingView().environment(AppState()).environment(\.dynamicTypeSize, .accessibility3)
}
