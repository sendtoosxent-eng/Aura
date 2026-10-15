import SwiftUI
import UIKit

// =============================================================================
// BUTTONS — built once, reused on every screen.
//
// LEARN: Instead of copy-pasting a styled Button 20 times, we make our own View.
// Parameters (title, icon, action) are its "inputs". `action` is a CLOSURE: a
// little block of code the caller hands us to run when the button is tapped.
// =============================================================================

/// Solid red button, white text. (Design: "Primary")
struct PrimaryButton: View {
    let title: String
    var icon: String? = nil          // optional SF Symbol on the right
    var isEnabled: Bool = true
    let action: () -> Void

    var body: some View {
        Button(action: action) {
            HStack(spacing: Spacing.sm) {
                Text(title).font(AppFont.labelLg)
                if let icon { Image(systemName: icon).font(.subheadline.weight(.bold)) }
            }
            // RESPONSIVE: maxWidth .infinity = "take all the width you're offered".
            // minHeight 52 keeps the touch target above Apple's 44pt minimum.
            .frame(maxWidth: .infinity, minHeight: 52)
            .foregroundStyle(.white)
            .background(isEnabled ? AppColor.primary : AppColor.primary.opacity(0.4))
            .clipShape(RoundedRectangle(cornerRadius: Radius.sm))
            .shadow(color: AppColor.primary.opacity(isEnabled ? 0.25 : 0), radius: 6, y: 3)
        }
        .disabled(!isEnabled)
    }
}

/// White button with a red border and red text. (Design: "Secondary")
struct SecondaryButton: View {
    let title: String
    var icon: String? = nil
    let action: () -> Void

    var body: some View {
        Button(action: action) {
            HStack(spacing: Spacing.sm) {
                if let icon { Image(systemName: icon) }
                Text(title).font(AppFont.labelLg)
            }
            .frame(maxWidth: .infinity, minHeight: 52)
            .foregroundStyle(AppColor.primary)
            .background(AppColor.surface)
            .clipShape(RoundedRectangle(cornerRadius: Radius.sm))
            .overlay(RoundedRectangle(cornerRadius: Radius.sm).stroke(AppColor.primary, lineWidth: 1))
        }
    }
}

/// Round phone button used on provider cards. `filled` = red circle, else outlined.
struct CallCircleButton: View {
    let phone: String
    var filled: Bool = false

    var body: some View {
        Button {
            PhoneCaller.call(phone)
        } label: {
            Image(systemName: "phone.fill")
                .font(.headline)
                .foregroundStyle(filled ? Color.white : AppColor.primary)
                .frame(width: 48, height: 48)   // ≥ 44pt touch target
                .background(filled ? AppColor.primary : Color.clear)
                .clipShape(Circle())
                .overlay(Circle().stroke(AppColor.primary, lineWidth: filled ? 0 : 1))
        }
        .accessibilityLabel("Call")
    }
}

/// Opens Apple Maps with a route to a provider.
struct DirectionsCircleButton: View {
    let provider: Provider

    var body: some View {
        Button {
            let query = provider.name.addingPercentEncoding(withAllowedCharacters: .urlQueryAllowed) ?? provider.name
            let url = URL(string: "http://maps.apple.com/?daddr=\(provider.latitude),\(provider.longitude)&q=\(query)")
            if let url { UIApplication.shared.open(url) }
        } label: {
            Image(systemName: "location")
                .font(.headline)
                .foregroundStyle(AppColor.primary)
                .frame(width: 48, height: 48)
                .background(AppColor.primaryTint)
                .clipShape(Circle())
                .overlay(Circle().stroke(AppColor.primary.opacity(0.25), lineWidth: 1))
        }
        .accessibilityLabel("Get directions to \(provider.name)")
    }
}

/// Small red "CALL" pill used in the emergency list.
struct CallPillButton: View {
    let phone: String

    var body: some View {
        Button {
            PhoneCaller.call(phone)
        } label: {
            Label("CALL", systemImage: "phone.fill")
                .font(AppFont.labelLg)
                .foregroundStyle(.white)
                .padding(.horizontal, Spacing.md)
                .frame(minHeight: 44)
                .background(AppColor.primary)
                .clipShape(RoundedRectangle(cornerRadius: Radius.sm))
        }
    }
}

// LEARN: Opening the Phone app is done by opening a "tel:" URL. iOS hands it to
// the Phone app, which asks the user to confirm. (The Simulator can't place
// calls — test on a real iPhone.)
enum PhoneCaller {
    static func call(_ number: String) {
        let cleaned = number.filter { $0.isNumber || $0 == "+" }
        if let url = URL(string: "tel://\(cleaned)") {
            UIApplication.shared.open(url)
        }
    }
}
