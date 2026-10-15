import SwiftUI

struct SplashView: View {
    @Environment(AppState.self) private var appState
    @Environment(\.accessibilityReduceMotion) private var reduceMotion
    @AppStorage("preferredLanguage") private var languageCode = WelcomeLanguage.deviceDefault
    @AppStorage("hasSeenOnboarding") private var hasSeenOnboarding = false
    @State private var appeared = false
    var advancesAutomatically = true

    private var language: WelcomeLanguage { WelcomeLanguage(rawValue: languageCode) ?? .english }

    var body: some View {
        ZStack {
            AppColor.background.ignoresSafeArea()
            GeometryReader { geometry in
                Circle().fill(AppColor.success.opacity(0.07))
                    .frame(width: geometry.size.width * 1.2)
                    .offset(x: geometry.size.width * 0.25, y: -180)
                Circle().fill(AppColor.primary.opacity(0.04))
                    .frame(width: geometry.size.width)
                    .offset(x: -geometry.size.width * 0.5, y: geometry.size.height * 0.65)
            }
            .accessibilityHidden(true)
            VStack(spacing: 0) {
                Spacer()
                VStack(spacing: 28) {
                    ZStack {
                        Circle().stroke(AppColor.success.opacity(0.1), lineWidth: 1).frame(width: 196, height: 196)
                        Circle().fill(AppColor.surface).frame(width: 158, height: 158)
                        BrandMark(size: 96)
                    }
                    VStack(spacing: 10) {
                        Text(AppConfig.brand)
                            .font(.system(.largeTitle, design: .rounded, weight: .bold))
                            .foregroundStyle(AppColor.onSurface)
                        Text("UGANDA")
                            .font(.caption.weight(.bold)).tracking(5)
                            .foregroundStyle(AppColor.success)
                    }
                    Text(language.tagline)
                        .font(.title3).foregroundStyle(AppColor.secondary)
                        .multilineTextAlignment(.center)
                }
                .opacity(appeared ? 1 : 0)
                .offset(y: appeared || reduceMotion ? 0 : 12)
                Spacer()
                VStack(spacing: 18) {
                    ProgressView().tint(AppColor.primary)
                    Text(language.footnote)
                        .font(.footnote).foregroundStyle(AppColor.secondary)
                        .multilineTextAlignment(.center)
                }
                .padding(.bottom, 30)
            }
            .padding(.horizontal, 24)
        }
        .task {
            withAnimation(reduceMotion ? nil : .easeOut(duration: 0.5)) { appeared = true }
            guard advancesAutomatically else { return }
            async let sessionRestored = appState.restoreSession()
            do { try await Task.sleep(for: .seconds(1.8)) } catch { return }
            guard !Task.isCancelled else { return }
            let restored = await sessionRestored
            guard !restored else { return }
            appState.stage = hasSeenOnboarding ? .auth : .onboarding
        }
    }
}

#Preview("Splash") {
    SplashView(advancesAutomatically: false).environment(AppState())
}
