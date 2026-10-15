import SwiftUI
import UIKit

// =============================================================================
// THEME  —  the "design system" from DESIGN.md turned into Swift constants.
//
// Why bother? If every screen typed Color(hex: 0xAF101A) we'd have 200 copies of
// the red. Instead screens say AppColor.primary and we change it in ONE place.
// =============================================================================

// MARK: - Hex colour helper
extension Color {
    // LEARN: An `extension` adds new abilities to an existing type. SwiftUI's
    // Color can't be built from "#AF101A" out of the box, so we add that.
    // `>>` shifts bits right, `& 0xFF` keeps only the last 8 bits = one channel.
    init(hex: UInt32, opacity: Double = 1) {
        self.init(
            .sRGB,
            red: Double((hex >> 16) & 0xFF) / 255,
            green: Double((hex >> 8) & 0xFF) / 255,
            blue: Double(hex & 0xFF) / 255,
            opacity: opacity
        )
    }
}

// MARK: - Colours (names copied from DESIGN.md)
enum AppColor {
    private static func adaptive(_ light: UInt32, _ dark: UInt32) -> Color {
        Color(uiColor: UIColor { traits in
            let hex = traits.userInterfaceStyle == .dark ? dark : light
            return UIColor(red: CGFloat((hex >> 16) & 255) / 255,
                           green: CGFloat((hex >> 8) & 255) / 255,
                           blue: CGFloat(hex & 255) / 255, alpha: 1)
        })
    }

    static let background        = adaptive(0xF7F9FC, 0x10151E) // screen background
    static let surface           = adaptive(0xFFFFFF, 0x1B2330)           // cards, inputs
    static let surfaceLow        = adaptive(0xF7F9FC, 0x10151E)
    static let surfaceContainer  = adaptive(0xF2F5F9, 0x202A38)
    static let surfaceHigh       = adaptive(0xE8EDF3, 0x344152)

    static let onSurface         = adaptive(0x172033, 0xF1F5FA) // main text
    static let onSurfaceVariant  = adaptive(0x5F6B7A, 0xC0CBD9) // secondary text
    static let secondary         = adaptive(0x6B7686, 0xAAB8C9)

    static let outline           = Color(hex: 0x9AA5B3) // input borders
    static let outlineVariant    = adaptive(0xE8EDF3, 0x344152) // subtle card borders

    static let primary           = Color(hex: 0xD32F2F) // AfyaLink red
    static let primaryContainer  = Color(hex: 0xB71C1C) // deep red
    static let primaryFixed      = adaptive(0xFFDAD6, 0x482329) // light red tint
    static let primaryTint       = adaptive(0xFFF1F1, 0x352028) // very light red (selected bg)

    static let success           = adaptive(0x168A55, 0x5BD99B) // AfyaLink green
    static let successContainer  = adaptive(0xECF9F1, 0x16382A)
    static let star              = Color(hex: 0xF5B301) // rating stars
}

// MARK: - Spacing (4px grid from DESIGN.md)
enum Spacing {
    static let xs: CGFloat = 4
    static let sm: CGFloat = 8
    static let md: CGFloat = 16   // default screen-edge padding
    static let lg: CGFloat = 24
    static let xl: CGFloat = 32
}

// MARK: - Corner radius
enum Radius {
    static let sm: CGFloat = 8    // buttons, inputs, cards ("DEFAULT")
    static let md: CGFloat = 12   // thumbnails
    static let lg: CGFloat = 16   // sheets, emergency cards
    static let xl: CGFloat = 24
}

// MARK: - Typography
// LEARN: The design uses the Inter font. We use iOS's system font (SF Pro)
// because it needs no setup, and — importantly — we use TEXT STYLES
// (.title2, .body …) instead of fixed sizes. Text styles automatically grow
// when the user turns on "Larger Text" in iOS Accessibility. That's the
// "responsive text" of iOS.
//
// To use Inter later: add the .ttf files to the project, list them in Info.plist
// under "Fonts provided by application", then use Font.custom("Inter", size:…,
// relativeTo: .body).
enum AppFont {
    static let displayEmergency = Font.system(.largeTitle, weight: .bold)
    static let headlineLg = Font.system(.title2, weight: .semibold)
    static let headlineMd = Font.system(.title3, weight: .semibold)
    static let bodyLg     = Font.system(.body)
    static let bodyMd     = Font.system(.subheadline)
    static let labelLg    = Font.system(.subheadline, weight: .semibold)
    static let labelSm    = Font.system(.caption, weight: .medium)
}

// MARK: - Reusable view modifiers
extension View {

    /// White card with a soft pink border and the "Level 1" shadow from the design.
    // LEARN: A function that takes a View and returns a modified View lets us
    // write `.cardStyle()` on anything, like built-in modifiers (.padding()).
    func cardStyle(radius: CGFloat = Radius.sm) -> some View {
        self
            .background(AppColor.surface)
            .clipShape(RoundedRectangle(cornerRadius: radius))
            .overlay(
                RoundedRectangle(cornerRadius: radius)
                    .stroke(AppColor.outlineVariant.opacity(0.6), lineWidth: 1)
            )
            .shadow(color: .black.opacity(0.08), radius: 4, x: 0, y: 2)
    }

    /// RESPONSIVE: on iPhone this does nothing visible. On iPad (or a landscape
    /// phone) it stops content stretching edge-to-edge by capping the width and
    /// centring it, so the layout "resizes according to device width".
    func readableWidth(_ maxWidth: CGFloat = 640) -> some View {
        self
            .frame(maxWidth: maxWidth)
            .frame(maxWidth: .infinity)
    }

    /// Red inline navigation title (the design uses red titles in the top bar).
    func redInlineTitle(_ title: String) -> some View {
        self
            .navigationBarTitleDisplayMode(.inline)
            .toolbar {
                ToolbarItem(placement: .principal) {
                    Text(title)
                        .font(AppFont.headlineMd)
                        .foregroundStyle(AppColor.primary)
                }
            }
            .tint(AppColor.primary) // colours the back arrow red
    }

    /// Outlined input look: white fill, 1px outline, 8px corners, 52pt tall.
    ///
    /// Keep this original overload so incremental builds compiled against the
    /// earlier one-argument API continue to link correctly.
    func inputBox(isFocused: Bool = false) -> some View {
        inputBoxStyle(isFocused: isFocused, hasError: false)
    }

    /// Validation-aware variant used by registration fields.
    func inputBox(isFocused: Bool = false, hasError: Bool) -> some View {
        inputBoxStyle(isFocused: isFocused, hasError: hasError)
    }

    private func inputBoxStyle(isFocused: Bool, hasError: Bool) -> some View {
        self
            .padding(.horizontal, Spacing.md)
            .frame(minHeight: 52)
            .background(AppColor.surface)
            .clipShape(RoundedRectangle(cornerRadius: Radius.sm))
            .overlay(
                RoundedRectangle(cornerRadius: Radius.sm)
                    .stroke(
                        (isFocused || hasError) ? AppColor.primary : AppColor.outline,
                        lineWidth: (isFocused || hasError) ? 2 : 1
                    )
            )
    }
}
