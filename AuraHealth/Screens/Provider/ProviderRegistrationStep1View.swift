import SwiftUI

/// First step of the backend-aligned provider onboarding flow.
struct ProviderRegistrationStep1View: View {
    @Environment(\.dismiss) private var dismiss
    @State private var applicationType: ProviderApplicationType?
    @State private var continueRegistration = false

    var body: some View {
        NavigationStack {
            VStack(spacing: 0) {
                topBar

                ScrollView {
                    VStack(alignment: .leading, spacing: Spacing.lg) {
                        progress

                        HStack(alignment: .top, spacing: Spacing.md) {
                            Image(systemName: "person.2.badge.gearshape.fill")
                                .font(.title2)
                                .foregroundStyle(AppColor.primary)
                                .frame(width: 48, height: 48)
                                .background(AppColor.primaryTint)
                                .clipShape(RoundedRectangle(cornerRadius: Radius.md))
                            VStack(alignment: .leading, spacing: 4) {
                                Text("REQUIRED")
                                    .font(AppFont.labelSm).tracking(1).foregroundStyle(AppColor.primary)
                                Text("Choose registration path")
                                    .font(AppFont.headlineLg)
                                Text("This determines the details and verification documents AfyaLink will request.")
                                    .font(AppFont.bodyMd)
                                    .foregroundStyle(AppColor.onSurfaceVariant)
                            }
                        }

                        ForEach(ProviderApplicationType.allCases) { type in
                            typeCard(type)
                        }

                        Label("Facilities need licence verification. Professionals must be linked to a verified AfyaLink workplace.",
                              systemImage: "checkmark.seal.fill")
                            .font(AppFont.bodyMd)
                            .foregroundStyle(AppColor.primary)
                            .padding(Spacing.md)
                            .frame(maxWidth: .infinity, alignment: .leading)
                            .background(AppColor.primaryTint, in: RoundedRectangle(cornerRadius: Radius.md))
                    }
                    .padding(Spacing.md)
                    .readableWidth(600)
                }

                VStack(spacing: Spacing.sm) {
                    PrimaryButton(title: "CONTINUE", icon: "arrow.right",
                                  isEnabled: applicationType != nil) {
                        continueRegistration = true
                    }
                    HStack(spacing: 4) {
                        Text("Already registered?")
                        Button("Sign In") { dismiss() }
                            .fontWeight(.bold)
                            .foregroundStyle(AppColor.primary)
                    }
                    .font(AppFont.bodyMd)
                    .foregroundStyle(AppColor.onSurfaceVariant)
                }
                .padding(Spacing.md)
                .readableWidth(600)
                .background(AppColor.surface)
            }
            .background(AppColor.background.ignoresSafeArea())
            .toolbar(.hidden, for: .navigationBar)
            .navigationDestination(isPresented: $continueRegistration) {
                if let applicationType {
                    ProviderRegistrationDetailsView(applicationType: applicationType)
                }
            }
        }
    }

    private func typeCard(_ type: ProviderApplicationType) -> some View {
        Button { applicationType = type } label: {
            HStack(alignment: .top, spacing: Spacing.md) {
                Image(systemName: type.icon)
                    .font(.title2)
                    .foregroundStyle(applicationType == type ? .white : AppColor.primary)
                    .frame(width: 52, height: 52)
                    .background(applicationType == type ? AppColor.primary : AppColor.primaryTint,
                                in: RoundedRectangle(cornerRadius: Radius.md))

                VStack(alignment: .leading, spacing: 5) {
                    Text(type.rawValue)
                        .font(AppFont.headlineMd)
                        .foregroundStyle(AppColor.onSurface)
                    Text(type == .facility
                         ? "Hospital, clinic, health centre, pharmacy, laboratory, maternity or dental facility."
                         : "Doctor, nurse, midwife, dentist, pharmacist, clinical officer or allied professional.")
                        .font(AppFont.bodyMd)
                        .foregroundStyle(AppColor.onSurfaceVariant)
                        .multilineTextAlignment(.leading)
                }
                Spacer(minLength: 0)
                Image(systemName: applicationType == type ? "checkmark.circle.fill" : "circle")
                    .font(.title3)
                    .foregroundStyle(applicationType == type ? AppColor.primary : AppColor.outline)
            }
            .padding(Spacing.md)
            .frame(maxWidth: .infinity, alignment: .leading)
            .background(AppColor.surface, in: RoundedRectangle(cornerRadius: Radius.lg))
            .overlay(RoundedRectangle(cornerRadius: Radius.lg)
                .stroke(applicationType == type ? AppColor.primary : AppColor.outlineVariant,
                        lineWidth: applicationType == type ? 2 : 1))
        }
        .buttonStyle(.plain)
    }

    private var topBar: some View {
        HStack {
            Button { dismiss() } label: {
                Image(systemName: "xmark").frame(width: 44, height: 44)
            }
            .accessibilityLabel("Close")
            Spacer()
            Text("Provider registration")
                .font(AppFont.headlineMd)
                .foregroundStyle(AppColor.primary)
            Spacer()
            Color.clear.frame(width: 44, height: 44)
        }
        .foregroundStyle(AppColor.primary)
        .padding(.horizontal, Spacing.sm)
        .overlay(alignment: .bottom) { Divider() }
    }

    private var progress: some View {
        ScrollView(.horizontal, showsIndicators: false) {
            HStack(alignment: .top, spacing: 0) {
                ForEach(Array(stepLabels.enumerated()), id: \.offset) { index, label in
                    VStack(spacing: 5) {
                        ZStack {
                            Circle().fill(index == 0 ? AppColor.primary : AppColor.surfaceHigh)
                            Text("\(index + 1)")
                                .font(AppFont.labelSm)
                                .foregroundStyle(index == 0 ? .white : AppColor.onSurfaceVariant)
                        }
                        .frame(width: 28, height: 28)
                        .overlay(Circle().stroke(AppColor.primaryFixed, lineWidth: index == 0 ? 4 : 0))
                        Text(label)
                            .font(.system(size: 9, weight: .semibold))
                            .foregroundStyle(index == 0 ? AppColor.primary : AppColor.onSurfaceVariant)
                            .lineLimit(1)
                    }
                    .frame(width: 54)
                    if index < stepLabels.count - 1 {
                        Rectangle().fill(AppColor.surfaceHigh)
                            .frame(width: 14, height: 3)
                            .padding(.top, 13)
                    }
                }
            }
            .padding(.vertical, Spacing.md)
            .padding(.horizontal, Spacing.sm)
        }
        .frame(maxWidth: .infinity)
        .cardStyle(radius: Radius.lg)
    }

    private var stepLabels: [String] {
        if applicationType == .facility {
            return ["Type", "Identity", "Account", "Verify", "Facility", "Documents", "Review"]
        }
        return ["Type", "Workplace", "Identity", "Account", "Verify", "Licence", "Documents", "Review"]
    }
}

struct ProviderRegistrationStep1View_Previews: PreviewProvider {
    static var previews: some View { ProviderRegistrationStep1View() }
}
