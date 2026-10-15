import SwiftUI

/// Label above, input in the middle, red error (or grey hint) below.
struct LabeledField<Content: View>: View {
    let label: String
    let isOptional: Bool
    let error: String?
    let hint: String?
    let content: Content

    init(_ label: String,
         isOptional: Bool = false,
         error: String? = nil,
         hint: String? = nil,
         @ViewBuilder content: () -> Content) {
        self.label = label
        self.isOptional = isOptional
        self.error = error
        self.hint = hint
        self.content = content()
    }

    var body: some View {
        VStack(alignment: .leading, spacing: 6) {
            HStack(spacing: 4) {
                Text(label).font(AppFont.labelLg).foregroundStyle(AppColor.onSurface)
                if isOptional {
                    Text("(optional)").font(AppFont.labelSm).foregroundStyle(AppColor.secondary)
                }
            }
            content
            if let error {
                Label(error, systemImage: "exclamationmark.circle.fill")
                    .font(AppFont.labelSm)
                    .foregroundStyle(AppColor.primary)
            } else if let hint {
                Text(hint).font(AppFont.labelSm).italic().foregroundStyle(AppColor.onSurfaceVariant)
            }
        }
    }
}

/// "Step 1 of 3" + segmented progress bar.
struct StepProgress: View {
    let current: Int
    let total: Int

    var body: some View {
        VStack(spacing: Spacing.sm) {
            HStack {
                Text("Step \(current) of \(total)")
                    .font(AppFont.bodyMd).foregroundStyle(AppColor.onSurfaceVariant)
                Spacer()
                Text("Registration").font(AppFont.labelLg).foregroundStyle(AppColor.primary)
            }
            HStack(spacing: 6) {
                ForEach(1...total, id: \.self) { i in
                    Capsule()
                        .fill(i <= current ? AppColor.primary : AppColor.surfaceHigh)
                        .frame(height: 6)
                }
            }
            .animation(.easeInOut, value: current)
        }
        .accessibilityElement(children: .ignore)
        .accessibilityLabel("Step \(current) of \(total)")
    }
}

/// The soft red information box from the provider-registration design.
struct InfoNote: View {
    let text: String
    var icon: String = "info.circle"

    var body: some View {
        Label(text, systemImage: icon)
            .font(AppFont.bodyMd)
            .foregroundStyle(AppColor.primary)
            .padding(Spacing.md)
            .frame(maxWidth: .infinity, alignment: .leading)
            .background(AppColor.primaryTint)
            .clipShape(RoundedRectangle(cornerRadius: Radius.sm))
            .overlay(RoundedRectangle(cornerRadius: Radius.sm).stroke(AppColor.outlineVariant, lineWidth: 1))
    }
}
