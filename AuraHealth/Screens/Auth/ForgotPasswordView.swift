import SwiftUI

struct ForgotPasswordView: View {
    private enum ResetStep: Int {
        case request, verify, newPassword
    }

    let phone: String

    @Environment(AppState.self) private var appState
    @Environment(\.dismiss) private var dismiss
    @State private var step: ResetStep = .request
    @State private var code = ""
    @State private var password = ""
    @State private var confirmation = ""
    @State private var showPasswords = false
    @State private var showSuccess = false
    @State private var errorMessage: String?
    @FocusState private var codeFocused: Bool

    private var passwordIsValid: Bool {
        password.count >= 8
        && password.contains(where: \.isUppercase)
        && password.contains(where: \.isLowercase)
        && password.contains(where: \.isNumber)
    }

    var body: some View {
        ScrollView {
            VStack(alignment: .leading, spacing: Spacing.lg) {
                Image(systemName: stepIcon)
                    .font(.system(size: 38))
                    .foregroundStyle(AppColor.primary)
                    .frame(width: 76, height: 76)
                    .background(AppColor.primaryTint, in: Circle())
                    .frame(maxWidth: .infinity)

                VStack(alignment: .leading, spacing: Spacing.sm) {
                    Text(stepTitle)
                        .font(AppFont.headlineLg)
                        .foregroundStyle(AppColor.onSurface)
                    Text(stepMessage)
                        .font(AppFont.bodyLg)
                        .foregroundStyle(AppColor.onSurfaceVariant)
                }

                switch step {
                case .request:
                    requestContent
                case .verify:
                    verificationContent
                case .newPassword:
                    newPasswordContent
                }

                if let errorMessage {
                    Label(errorMessage, systemImage: "exclamationmark.circle.fill")
                        .font(AppFont.labelSm)
                        .foregroundStyle(AppColor.primary)
                }
            }
            .padding(Spacing.lg)
            .readableWidth(480)
        }
        .background(AppColor.background.ignoresSafeArea())
        .navigationTitle("Forgot Password")
        .navigationBarTitleDisplayMode(.inline)
        .tint(AppColor.primary)
        .alert("Password updated", isPresented: $showSuccess) {
            Button("Return to sign in") { dismiss() }
        } message: {
            Text("You can now sign in using your new password.")
        }
    }

    private var requestContent: some View {
        VStack(spacing: Spacing.md) {
            HStack(spacing: Spacing.md) {
                Image(systemName: "iphone")
                    .foregroundStyle(AppColor.primary)
                VStack(alignment: .leading, spacing: 2) {
                    Text("Reset code will be sent to")
                        .font(AppFont.labelSm)
                        .foregroundStyle(AppColor.onSurfaceVariant)
                    Text(phone)
                        .font(AppFont.headlineMd)
                        .foregroundStyle(AppColor.onSurface)
                }
                Spacer()
            }
            .padding(Spacing.md)
            .cardStyle(radius: Radius.md)

            PrimaryButton(title: "Send Reset Code", icon: "paperplane.fill") {
                withAnimation { step = .verify }
                codeFocused = true
            }
        }
    }

    private var verificationContent: some View {
        VStack(spacing: Spacing.md) {
            codeBoxes

            Text("Prototype: enter any four-digit code.")
                .font(AppFont.labelSm)
                .foregroundStyle(AppColor.onSurfaceVariant)
                .frame(maxWidth: .infinity, alignment: .center)

            PrimaryButton(title: "Verify Code", icon: "checkmark.shield.fill", isEnabled: code.count == 4) {
                errorMessage = nil
                withAnimation { step = .newPassword }
            }

            Button("Send another code") { code = "" }
                .font(AppFont.labelLg)
                .foregroundStyle(AppColor.primary)
                .frame(maxWidth: .infinity, minHeight: 44)
        }
    }

    private var newPasswordContent: some View {
        VStack(alignment: .leading, spacing: Spacing.md) {
            passwordField("New password", text: $password)
            passwordField("Confirm password", text: $confirmation)

            Label("Use at least 8 characters with uppercase, lowercase and a number.", systemImage: "lock.shield")
                .font(AppFont.labelSm)
                .foregroundStyle(passwordIsValid ? AppColor.success : AppColor.onSurfaceVariant)

            PrimaryButton(
                title: "Update Password",
                icon: "checkmark",
                isEnabled: passwordIsValid && !confirmation.isEmpty,
                action: updatePassword
            )
        }
    }

    private var codeBoxes: some View {
        HStack(spacing: Spacing.sm) {
            ForEach(0..<4, id: \.self) { index in
                let digit = index < code.count ? String(Array(code)[index]) : ""
                Text(digit)
                    .font(.title2.bold())
                    .frame(maxWidth: .infinity, minHeight: 62)
                    .background(AppColor.surface)
                    .clipShape(RoundedRectangle(cornerRadius: Radius.md))
                    .overlay(RoundedRectangle(cornerRadius: Radius.md).stroke(AppColor.outlineVariant, lineWidth: 1))
            }
        }
        .contentShape(Rectangle())
        .onTapGesture { codeFocused = true }
        .background {
            TextField("", text: $code)
                .keyboardType(.numberPad)
                .textContentType(.oneTimeCode)
                .focused($codeFocused)
                .opacity(0.01)
                .onChange(of: code) { _, value in
                    code = String(value.filter(\.isNumber).prefix(4))
                }
        }
    }

    private func passwordField(_ title: String, text: Binding<String>) -> some View {
        VStack(alignment: .leading, spacing: 6) {
            Text(title)
                .font(AppFont.labelLg)
                .foregroundStyle(AppColor.onSurface)
            HStack {
                Group {
                    if showPasswords {
                        TextField(title, text: text)
                    } else {
                        SecureField(title, text: text)
                    }
                }
                .textContentType(.newPassword)

                Button { showPasswords.toggle() } label: {
                    Image(systemName: showPasswords ? "eye.slash" : "eye")
                        .foregroundStyle(AppColor.onSurfaceVariant)
                        .frame(width: 44, height: 44)
                }
            }
            .inputBox()
        }
    }

    private func updatePassword() {
        guard password == confirmation else {
            errorMessage = "The passwords do not match."
            return
        }
        errorMessage = nil
        appState.updatePassword(password, for: phone)
        showSuccess = true
    }

    private var stepIcon: String {
        switch step {
        case .request: return "lock.rotation"
        case .verify: return "message.badge.filled.fill"
        case .newPassword: return "key.fill"
        }
    }

    private var stepTitle: String {
        switch step {
        case .request: return "Reset your password"
        case .verify: return "Enter the reset code"
        case .newPassword: return "Create a new password"
        }
    }

    private var stepMessage: String {
        switch step {
        case .request: return "We’ll verify that you have access to the phone number linked to your profile."
        case .verify: return "Enter the four-digit code sent to \(phone)."
        case .newPassword: return "Choose a strong password you have not used before."
        }
    }
}

#Preview {
    NavigationStack { ForgotPasswordView(phone: "+256 770 000 000") }
        .environment(AppState())
}
