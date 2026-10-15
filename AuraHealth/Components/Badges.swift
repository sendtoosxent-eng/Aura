import SwiftUI

// Small visual pieces: filter chips, status badges, star ratings, pulse ring.

// MARK: - Filter chip (Design: "Chips")
struct FilterChip: View {
    let title: String
    var icon: String? = nil
    let isSelected: Bool
    let action: () -> Void

    var body: some View {
        Button(action: action) {
            HStack(spacing: 6) {
                if let icon { Image(systemName: icon) }
                Text(title)
            }
            .font(AppFont.labelLg)
            .padding(.horizontal, Spacing.md)
            .frame(minHeight: 44)
            // Selected = solid red, unselected = soft grey (like the Home design).
            .foregroundStyle(isSelected ? Color.white : AppColor.onSurfaceVariant)
            .background(isSelected ? AppColor.primary : AppColor.surfaceHigh)
            .clipShape(Capsule())
        }
        .buttonStyle(.plain)
    }
}

// MARK: - Status badge ("Open Now" / "Closed")
struct StatusBadge: View {
    let isOpen: Bool
    var text: String? = nil

    var body: some View {
        Text(text ?? (isOpen ? "Open Now" : "Closed"))
            .font(AppFont.labelSm)
            .foregroundStyle(isOpen ? AppColor.success : AppColor.secondary)
            .padding(.horizontal, 10)
            .padding(.vertical, 4)
            .background(isOpen ? AppColor.successContainer : AppColor.surfaceHigh)
            .clipShape(Capsule())
    }
}

// MARK: - Rating label: ☆ 4.8
struct RatingLabel: View {
    let rating: Double
    var body: some View {
        HStack(spacing: 4) {
            Image(systemName: "star").foregroundStyle(AppColor.star)
            Text(String(format: "%.1f", rating)).foregroundStyle(AppColor.onSurface)
        }
        .font(AppFont.labelLg)
    }
}

// MARK: - Read-only stars (supports half stars)
struct StarRow: View {
    let rating: Double
    var size: CGFloat = 14
    var body: some View {
        HStack(spacing: 2) {
            ForEach(1...5, id: \.self) { i in
                Image(systemName: symbol(for: i))
                    .font(.system(size: size))
                    .foregroundStyle(AppColor.primary)
            }
        }
        .accessibilityElement(children: .ignore)
        .accessibilityLabel("Rated \(rating) out of 5")
    }

    private func symbol(for index: Int) -> String {
        if rating >= Double(index) { return "star.fill" }
        if rating >= Double(index) - 0.5 { return "star.leadinghalf.filled" }
        return "star"
    }
}

// MARK: - Tappable stars for "Write a Review"
struct InteractiveStars: View {
    // LEARN: @Binding = "a two-way connection to a value owned by my parent".
    // When we change `rating` here, the parent's @State changes too.
    @Binding var rating: Int

    var body: some View {
        HStack(spacing: Spacing.md) {
            ForEach(1...5, id: \.self) { i in
                Button {
                    rating = i
                } label: {
                    Image(systemName: i <= rating ? "star.fill" : "star")
                        .font(.system(size: 36))
                        .foregroundStyle(i <= rating ? AppColor.star : AppColor.outline)
                }
                .buttonStyle(.plain)
                .accessibilityLabel("\(i) star\(i == 1 ? "" : "s")")
            }
        }
    }
}

// MARK: - Pulsing ring (SOS + emergency call button)
struct PulseRing: View {
    var color: Color = AppColor.primaryContainer
    var cornerRadius: CGFloat? = nil       // nil = circle
    @State private var animate = false

    var body: some View {
        Group {
            if let cornerRadius {
                RoundedRectangle(cornerRadius: cornerRadius).stroke(color.opacity(0.45), lineWidth: 4)
            } else {
                Circle().stroke(color.opacity(0.45), lineWidth: 4)
            }
        }
        .scaleEffect(animate ? 1.35 : 1)
        .opacity(animate ? 0 : 1)
        // LEARN: .animation(_, value:) animates changes of `animate`.
        // repeatForever makes it loop; we flip `animate` once in onAppear.
        .animation(.easeOut(duration: 1.4).repeatForever(autoreverses: false), value: animate)
        .onAppear { animate = true }
        .allowsHitTesting(false)
    }
}
