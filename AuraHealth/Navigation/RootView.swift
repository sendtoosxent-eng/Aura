import SwiftUI

// RootView decides WHICH big screen to show, based on appState.stage:
//   splash → onboarding → auth (login + OTP) → patient tabs
//                                            └→ provider portal
struct RootView: View {
    @Environment(AppState.self) private var appState

    var body: some View {
        // LEARN: `switch` inside body works because body is a @ViewBuilder.
        Group {
            switch appState.stage {
            case .splash:         SplashView()
            case .onboarding:     OnboardingView()
            case .auth:           AuthFlowView()
            case .patient:        MainTabView()
            case .providerPortal: ProviderPortalView()
            }
        }
        .transition(.opacity)
        .animation(.easeInOut(duration: 0.3), value: appState.stage)
        .task { await appState.loadDiscoveryIfNeeded() }
    }
}

#Preview {
    RootView().environment(AppState())
}
