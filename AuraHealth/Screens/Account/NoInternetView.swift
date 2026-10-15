import SwiftUI

// SCREEN 14 — No internet (design: no_internet_connection)
// Shown by MainTabView when NetworkMonitor reports we're offline.
// To SEE it while building: use the preview below, or turn on Airplane Mode.
struct NoInternetView: View {
    let onRetry: () -> Void
    let onViewOffline: () -> Void

    var body: some View {
        VStack(spacing: Spacing.lg) {
            Spacer()

            ZStack {
                Circle().fill(AppColor.surfaceLow).frame(width: 190, height: 190)
                Image(systemName: "wifi.slash")
                    .font(.system(size: 70, weight: .semibold))
                    .foregroundStyle(AppColor.primary)
            }

            VStack(spacing: Spacing.sm) {
                Text("No Connection").font(AppFont.headlineLg).foregroundStyle(AppColor.onSurface)

                // LEARN: Text can be built by joining styled pieces with `+`
                // so a few words get a different colour/weight.
                (Text("We're having trouble reaching our servers. You can still access your ")
                 + Text("cached list of providers").bold().foregroundColor(AppColor.primary)
                 + Text(" for immediate help."))
                    .font(AppFont.bodyLg)
                    .foregroundStyle(AppColor.onSurfaceVariant)
                    .multilineTextAlignment(.center)
            }

            VStack(spacing: Spacing.md) {
                PrimaryButton(title: "Retry", icon: "arrow.clockwise", action: onRetry)
                SecondaryButton(title: "View Offline Data", action: onViewOffline)
            }
            .padding(.top, Spacing.sm)

            Spacer()
        }
        .padding(Spacing.lg)
        .readableWidth(480)
        .frame(maxWidth: .infinity, maxHeight: .infinity)
        .background(AppColor.background.ignoresSafeArea())
    }
}

#Preview {
    NoInternetView(onRetry: {}, onViewOffline: {})
}
