import SwiftUI

/// Custom top bar used on the tab root screens (Home, Map, Favorites, Account).
/// Shows a red title on the left and the notification bell on the right.
///
/// NOTE: screens that use this must hide the system navigation bar with
///     .toolbar(.hidden, for: .navigationBar)
/// otherwise there would be two bars.
struct AppHeader: View {
    let title: String
    var leadingSymbol: String? = nil

    var body: some View {
        HStack(spacing: Spacing.sm) {
            if let leadingSymbol {
                Image(systemName: leadingSymbol)
                    .font(.title3)
                    .foregroundStyle(AppColor.primary)
                    .frame(width: 34, height: 34)
            } else {
                BrandMark(size: 34)
            }
            Text(title)
                .font(AppFont.headlineMd)
                .foregroundStyle(AppColor.primary)
            Spacer()

            // The bell pushes the Notifications screen (Route.notifications).
            NavigationLink(value: Route.notifications) {
                ZStack(alignment: .topTrailing) {
                    Image(systemName: "bell")
                        .font(.title3)
                        .foregroundStyle(AppColor.onSurface)
                    Circle().fill(AppColor.primary).frame(width: 9, height: 9)
                        .offset(x: 2, y: -2)
                }
                .frame(width: 44, height: 44)
            }
            .accessibilityLabel("Notifications")
        }
        .padding(.horizontal, Spacing.md)
        .frame(minHeight: 56)
        .background(AppColor.background)
        .overlay(alignment: .bottom) { Divider() }
    }
}

/// Native rendering of the heart-pulse mark used by the AfyaLink website.
struct BrandMark: View {
    var size: CGFloat = 44

    var body: some View {
        RoundedRectangle(cornerRadius: size * 0.3, style: .continuous)
            .fill(
                LinearGradient(
                    colors: [AppColor.primary, AppColor.primaryContainer],
                    startPoint: .topLeading,
                    endPoint: .bottomTrailing
                )
            )
            .frame(width: size, height: size)
            .overlay {
                ZStack {
                    Image(systemName: "heart.fill")
                        .font(.system(size: size * 0.5, weight: .bold))
                        .foregroundStyle(.white)
                    Image(systemName: "waveform.path.ecg")
                        .font(.system(size: size * 0.34, weight: .bold))
                        .foregroundStyle(AppColor.primaryContainer)
                }
            }
            .shadow(color: AppColor.primary.opacity(0.2), radius: size * 0.18, y: size * 0.1)
            .accessibilityHidden(true)
    }
}
