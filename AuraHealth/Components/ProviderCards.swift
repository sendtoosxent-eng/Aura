import SwiftUI

// =============================================================================
// PROVIDER CARDS — the hospital/clinic cards used on Home, Map, Favorites…
// =============================================================================

struct ProviderThumbnail: View {
    let provider: Provider
    var cornerRadius: CGFloat = Radius.md
    var iconSize: CGFloat = 30

    var body: some View {
        GeometryReader { proxy in
            Group {
                if let imageURL = provider.imageURL {
                    AsyncImage(url: imageURL) { phase in
                        switch phase {
                        case .success(let image):
                            image
                                .resizable()
                                .scaledToFill()
                        default:
                            placeholder
                        }
                    }
                } else {
                    placeholder
                }
            }
            .frame(width: proxy.size.width, height: proxy.size.height)
            .clipped()
        }
        .clipShape(RoundedRectangle(cornerRadius: cornerRadius))
        .accessibilityHidden(true)
    }

    private var placeholder: some View {
        ZStack {
            LinearGradient(
                colors: [AppColor.primaryFixed, AppColor.surfaceHigh],
                startPoint: .topLeading,
                endPoint: .bottomTrailing
            )
            Image(systemName: provider.type.icon)
                .font(.system(size: iconSize))
                .foregroundStyle(AppColor.primary.opacity(0.8))
        }
    }
}

/// Big card with picture on top (first result on Home).
struct FeaturedProviderCard: View {
    let provider: Provider

    var body: some View {
        VStack(alignment: .leading, spacing: 0) {
            ProviderThumbnail(provider: provider, cornerRadius: 0, iconSize: 64)
                .frame(height: 170)
                .overlay(alignment: .topTrailing) {
                    HStack(spacing: 4) {
                        Image(systemName: "star").foregroundStyle(AppColor.star)
                        Text(String(format: "%.1f", provider.rating))
                    }
                    .font(AppFont.labelLg)
                    .padding(.horizontal, 10).padding(.vertical, 6)
                    .background(.white)
                    .clipShape(Capsule())
                    .padding(Spacing.sm)
                }

            VStack(alignment: .leading, spacing: Spacing.sm) {
                providerName(provider, font: AppFont.headlineMd)
                    .frame(maxWidth: .infinity, alignment: .leading)

                HStack(alignment: .firstTextBaseline, spacing: Spacing.sm) {
                    Text(provider.address)
                        .font(AppFont.bodyMd)
                        .foregroundStyle(AppColor.onSurfaceVariant)
                        .lineLimit(2)
                        .fixedSize(horizontal: false, vertical: true)

                    Spacer(minLength: Spacing.xs)

                    StatusBadge(isOpen: provider.isOpenNow)
                        .fixedSize()
                }

                HStack {
                    Label(provider.distanceText, systemImage: "location")
                        .font(AppFont.labelLg)
                        .foregroundStyle(AppColor.onSurface)
                    Spacer()
                    DirectionsCircleButton(provider: provider)
                    Button {
                        PhoneCaller.call(provider.phone)
                    } label: {
                        Label("Call Now", systemImage: "phone.fill")
                            .font(AppFont.labelLg)
                            .foregroundStyle(.white)
                            .padding(.horizontal, Spacing.lg)
                            .frame(minHeight: 44)
                            .background(AppColor.primary)
                            .clipShape(Capsule())
                    }
                }
                .padding(.top, Spacing.xs)
            }
            .padding(Spacing.md)
        }
        .cardStyle(radius: Radius.lg)
    }

    @ViewBuilder
    private func providerName(_ provider: Provider, font: Font) -> some View {
        HStack(alignment: .firstTextBaseline, spacing: 5) {
            Text(provider.name)
                .font(font)
                .foregroundStyle(AppColor.onSurface)
                .multilineTextAlignment(.leading)
                .lineLimit(2)
                .fixedSize(horizontal: false, vertical: true)
                .layoutPriority(1)
            if provider.type == .hospital && provider.isVerified {
                Image(systemName: "checkmark.seal.fill")
                    .font(.caption.weight(.bold))
                    .foregroundStyle(AppColor.primary)
                    .fixedSize()
                    .accessibilityLabel("Verified hospital")
            }
        }
    }
}

/// Compact horizontal card: photo left, details middle, phone button right.
struct ProviderRow: View {
    let provider: Provider

    var body: some View {
        HStack(alignment: .top, spacing: Spacing.md) {
            ProviderThumbnail(provider: provider)
                .frame(width: 84, height: 84)

            VStack(alignment: .leading, spacing: 4) {
                providerName(provider, font: AppFont.labelLg)
                    .frame(maxWidth: .infinity, alignment: .leading)

                HStack(alignment: .firstTextBaseline, spacing: Spacing.xs) {
                    Text(provider.address)
                        .font(AppFont.bodyMd)
                        .foregroundStyle(AppColor.onSurfaceVariant)
                        .lineLimit(1)

                    Spacer(minLength: Spacing.xs)

                    RatingLabel(rating: provider.rating)
                        .fixedSize()
                }

                HStack(spacing: 6) {
                    Circle()
                        .fill(provider.isOpenNow ? AppColor.success : AppColor.secondary)
                        .frame(width: 8, height: 8)
                    Text(provider.statusText)
                        .font(AppFont.labelSm)
                        .foregroundStyle(provider.isOpenNow ? AppColor.success : AppColor.secondary)
                }

                HStack {
                    Text("\(provider.distanceText) away")
                        .font(AppFont.bodyMd)
                        .foregroundStyle(AppColor.secondary)
                    Spacer()
                    DirectionsCircleButton(provider: provider)
                    CallCircleButton(phone: provider.phone)
                }
            }
        }
        .padding(Spacing.md)
        .cardStyle(radius: Radius.lg)
    }

    @ViewBuilder
    private func providerName(_ provider: Provider, font: Font) -> some View {
        HStack(alignment: .firstTextBaseline, spacing: 5) {
            Text(provider.name)
                .font(font)
                .foregroundStyle(AppColor.onSurface)
                .multilineTextAlignment(.leading)
                .lineLimit(2)
                .fixedSize(horizontal: false, vertical: true)
                .layoutPriority(1)
            if provider.type == .hospital && provider.isVerified {
                Image(systemName: "checkmark.seal.fill")
                    .font(.caption.weight(.bold))
                    .foregroundStyle(AppColor.primary)
                    .fixedSize()
                    .accessibilityLabel("Verified hospital")
            }
        }
    }
}

/// Grey pulsing placeholder shown while data "loads" (Design: Skeleton Loaders).
struct ProviderRowSkeleton: View {
    @State private var dim = false

    var body: some View {
        HStack(alignment: .top, spacing: Spacing.md) {
            RoundedRectangle(cornerRadius: Radius.md).frame(width: 84, height: 84)
            VStack(alignment: .leading, spacing: Spacing.sm) {
                RoundedRectangle(cornerRadius: 4).frame(height: 16)
                RoundedRectangle(cornerRadius: 4).frame(width: 140, height: 12)
                RoundedRectangle(cornerRadius: 4).frame(width: 90, height: 12)
                RoundedRectangle(cornerRadius: 4).frame(width: 110, height: 12)
            }
        }
        .foregroundStyle(AppColor.surfaceHigh)
        .padding(Spacing.md)
        .cardStyle(radius: Radius.lg)
        .opacity(dim ? 0.5 : 1)
        .animation(.easeInOut(duration: 0.8).repeatForever(autoreverses: true), value: dim)
        .onAppear { dim = true }
    }
}
