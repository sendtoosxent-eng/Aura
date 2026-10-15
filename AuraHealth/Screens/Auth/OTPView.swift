import SwiftUI

enum OTPReason: Equatable {
    case phoneVerification
    case newLocation

    var title: String {
        switch self {
        case .phoneVerification: return "Verify your number"
        case .newLocation: return "Confirm this login"
        }
    }

    func message(phone: String) -> String {
        switch self {
        case .phoneVerification:
            return "We've sent a 6-digit code to \(phone) for secure access to medical services."
        case .newLocation:
            return "Your password was correct, but this login came from a new IP address or device. Enter the code sent to \(phone)."
        }
    }
}

// SCREEN 4 — OTP verification (design: otp_verification)
// The login security challenge uses the backend's six-digit code.
struct OTPView: View {
    let phone: String
    let role: UserRole
    var reason: OTPReason = .phoneVerification

    @Environment(AppState.self) private var appState
    @Environment(\.dismiss) private var dismiss   // lets us "go back" in code

    @State private var code = ""
    @State private var secondsLeft = 60
    @State private var isVerifying = false
    @State private var isResending = false
    @State private var errorMessage: String?
    @FocusState private var isFocused: Bool

    // LEARN: A Timer publisher fires every second; .onReceive below reacts.
    private let timer = Timer.publish(every: 1, on: .main, in: .common).autoconnect()

    private var timeText: String { String(format: "%d:%02d", secondsLeft / 60, secondsLeft % 60) }

    var body: some View {
        ScrollView {
            VStack(spacing: Spacing.lg) {
                VStack(spacing: Spacing.sm) {
                    Text(reason.title)
                        .font(AppFont.headlineLg).foregroundStyle(AppColor.onSurface)
                    Text(reason.message(phone: phone))
                        .font(AppFont.bodyLg).foregroundStyle(AppColor.onSurfaceVariant)
                        .multilineTextAlignment(.center)
                }
                .padding(.top, Spacing.lg)

                // Phone-vibrating icon in a white circle
                ZStack {
                    Circle().fill(AppColor.primaryTint).frame(width: 190, height: 190)
                    Circle().fill(.white).frame(width: 120, height: 120)
                        .shadow(color: .black.opacity(0.1), radius: 10, y: 4)
                    Image(systemName: "iphone.radiowaves.left.and.right")
                        .font(.system(size: 48)).foregroundStyle(AppColor.primary)
                }
                .padding(.vertical, Spacing.md)

                codeBoxes

#if DEBUG
                if reason == .newLocation, let debugCode = appState.loginDebugCode {
                    Text("Local development code: \(debugCode)")
                        .font(AppFont.labelSm)
                        .foregroundStyle(AppColor.onSurfaceVariant)
                }
                if reason == .phoneVerification, let debugCode = appState.registrationDebugCode {
                    Text("Local development code: \(debugCode)")
                        .font(AppFont.labelSm)
                        .foregroundStyle(AppColor.onSurfaceVariant)
                }
#endif

                if let errorMessage {
                    Label(errorMessage, systemImage: "exclamationmark.circle.fill")
                        .font(AppFont.labelSm)
                        .foregroundStyle(AppColor.primary)
                }

                VStack(spacing: Spacing.sm) {
                    HStack(spacing: 6) {
                        Image(systemName: "clock")
                        Text("Resend code in")
                        Text(timeText).fontWeight(.bold).foregroundStyle(AppColor.primary)
                    }
                    .font(AppFont.bodyLg).foregroundStyle(AppColor.onSurfaceVariant)

                    if reason == .phoneVerification {
                        Button(isResending ? "Sending…" : "Resend OTP") {
                            resendRegistrationCode()
                        }
                        .font(AppFont.bodyLg)
                        .foregroundStyle(secondsLeft == 0 ? AppColor.primary : AppColor.secondary)
                        .disabled(secondsLeft > 0)
                        .frame(minHeight: 44)
                    } else {
                        Button(isResending ? "Sending…" : "Resend OTP") {
                            resendLoginCode()
                        }
                        .font(AppFont.bodyLg)
                        .foregroundStyle(secondsLeft == 0 ? AppColor.primary : AppColor.secondary)
                        .disabled(secondsLeft > 0 || isResending)
                        .frame(minHeight: 44)
                    }
                }

                PrimaryButton(
                    title: isVerifying ? "VERIFYING…" : "VERIFY",
                    isEnabled: code.count == requiredCodeLength && !isVerifying
                ) {
                    verifyCode()
                }
            }
            .padding(Spacing.md)
            .readableWidth(480)
        }
        .background(AppColor.background.ignoresSafeArea())
        .onReceive(timer) { _ in if secondsLeft > 0 { secondsLeft -= 1 } }
        .onAppear { isFocused = true }
        .navigationBarBackButtonHidden(true)
        .toolbar {
            ToolbarItem(placement: .topBarLeading) {
                Button { dismiss() } label: {
                    Image(systemName: "arrow.left").foregroundStyle(AppColor.onSurface)
                }
            }
        }
    }

    /// LEARN (a classic trick): ONE invisible TextField receives the typing.
    /// Four pretty boxes just DISPLAY its characters. Tapping a box focuses
    /// the hidden field, which opens the number keyboard.
    private var codeBoxes: some View {
        HStack(spacing: Spacing.md) {
            ForEach(0..<requiredCodeLength, id: \.self) { i in
                let digit = i < code.count ? String(Array(code)[i]) : ""
                let isCurrent = i == code.count && isFocused

                Text(digit)
                    .font(.system(.title, weight: .bold))
                    .foregroundStyle(AppColor.onSurface)
                    .frame(maxWidth: .infinity, minHeight: 64)   // 4 equal boxes, any width
                    .background(AppColor.surface)
                    .clipShape(RoundedRectangle(cornerRadius: Radius.md))
                    .overlay(
                        RoundedRectangle(cornerRadius: Radius.md)
                            .stroke(isCurrent ? AppColor.primary : AppColor.outlineVariant, lineWidth: 2)
                    )
            }
        }
        .contentShape(Rectangle())
        .onTapGesture { isFocused = true }
        .background {
            TextField("", text: $code)
                .keyboardType(.numberPad)
                .textContentType(.oneTimeCode)     // lets iOS suggest the SMS code
                .focused($isFocused)
                .opacity(0.01)
                .onChange(of: code) { _, newValue in
                    let digits = String(newValue.filter(\.isNumber).prefix(requiredCodeLength))
                    if digits != code { code = digits }
                }
        }
    }

    private var requiredCodeLength: Int { 6 }

    private func verifyCode() {
        errorMessage = nil
        if reason == .phoneVerification {
            isVerifying = true
            Task {
                do {
                    try await appState.verifyPatientRegistration(code: code)
                    await MainActor.run { isVerifying = false }
                } catch {
                    await MainActor.run {
                        isVerifying = false
                        errorMessage = error.localizedDescription
                    }
                }
            }
            return
        }
        isVerifying = true
        Task {
            do {
                try await appState.verifyPendingLogin(code: code)
                await MainActor.run { isVerifying = false }
            } catch {
                await MainActor.run {
                    isVerifying = false
                    errorMessage = error.localizedDescription
                }
            }
        }
    }

    private func resendRegistrationCode() {
        errorMessage = nil
        isResending = true
        Task {
            do {
                try await appState.sendPatientRegistrationCode()
                await MainActor.run {
                    isResending = false
                    secondsLeft = 60
                    code = ""
                }
            } catch {
                await MainActor.run {
                    isResending = false
                    errorMessage = error.localizedDescription
                }
            }
        }
    }

    private func resendLoginCode() {
        errorMessage = nil
        isResending = true
        Task {
            do {
                try await appState.resendPendingLoginCode()
                await MainActor.run {
                    isResending = false
                    secondsLeft = 60
                    code = ""
                }
            } catch {
                await MainActor.run {
                    isResending = false
                    errorMessage = error.localizedDescription
                }
            }
        }
    }
}

#Preview {
    NavigationStack { OTPView(phone: "+256 770 000 000", role: .patient) }
        .environment(AppState())
}
