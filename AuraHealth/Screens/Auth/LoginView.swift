import SwiftUI

// The auth flow lives in its own NavigationStack so Login can PUSH the OTP screen.
struct AuthFlowView: View {
    var body: some View {
        NavigationStack {
            LoginView()
        }
    }
}

// SCREEN 3 — Login / Sign up (Full-Bleed Image Design)
struct LoginView: View {
    @Environment(AppState.self) private var appState

    private enum LoginStep { case phone, profile, password }

    @State private var role: UserRole = .patient
    @State private var phoneDigits = ""        // only the 9 digits after +256
    @State private var goToOTP = false
    @State private var showGoogleAlert = false
    @FocusState private var phoneFocused: Bool
    @State private var goToRegister = false
    @State private var goToForgotPassword = false
    @State private var step: LoginStep = .phone
    @State private var profile: AuthProfile?
    @State private var password = ""
    @State private var passwordVisible = false
    @State private var isLookingUpProfile = false
    @State private var isSigningIn = false
    @State private var errorMessage: String?

    /// Ugandan mobile numbers are 9 digits after +256.
    private var isPhoneValid: Bool { phoneDigits.count == 9 }

    var body: some View {
        ZStack {
            // MARK: - Full Screen Background Image
            backgroundImageView

            // MARK: - Foreground Content
            ScrollView {
                VStack(spacing: Spacing.lg) {
                    // Top spacing to let hero image shine through
                    Spacer(minLength: 120)

                    // Header Text
                    headerTitleSection

                    // Floating Glass Form Card
                    formCard

                    // Bottom Sign Up Action Link
                    HStack(spacing: 4) {
                        Text("New to \(AppConfig.brand)?")
                            .foregroundStyle(.white.opacity(0.85))
                        Button("Create Account") { goToRegister = true }
                            .fontWeight(.bold)
                            .foregroundStyle(.white)
                    }
                    .font(AppFont.bodyLg)
                    .padding(.bottom, 24)
                }
                .padding(.horizontal, Spacing.md)
                .readableWidth(480)
            }
            .scrollDismissesKeyboard(.interactively)
        }
        .navigationDestination(isPresented: $goToOTP) {
            OTPView(phone: "+256 \(phoneDigits)", role: role, reason: .newLocation)
        }
        .navigationDestination(isPresented: $goToRegister) {
            RegistrationFlowView()
        }
        .navigationDestination(isPresented: $goToForgotPassword) {
            ForgotPasswordView(phone: "+256 \(phoneDigits)")
        }
        .alert("Google Sign-In", isPresented: $showGoogleAlert) {
            Button("OK", role: .cancel) {}
        } message: {
            Text("Needs a backend + Google Sign-In SDK. A good later exercise!")
        }
        .toolbar(.hidden, for: .navigationBar)
    }

    // MARK: - Full Screen Image & Vignette
    private var backgroundImageView: some View {
        GeometryReader { geometry in
            // Reuse the AfyaLink welcome photography from onboarding so the
            // authentication flow keeps the same visual story.
            Image("WelcomeCare")
                .resizable()
                .scaledToFill()
                .frame(width: geometry.size.width, height: geometry.size.height)
                .clipped()
                .overlay {
                    // Dark gradient vignettes to maintain high readability for status bar & text
                    LinearGradient(
                        colors: [
                            .black.opacity(0.65),
                            .black.opacity(0.3),
                            .black.opacity(0.75)
                        ],
                        startPoint: .top,
                        endPoint: .bottom
                    )
                }
        }
        .ignoresSafeArea() // Extends image behind status bar & bottom safe area
    }

    // MARK: - Header Text
    private var headerTitleSection: some View {
        VStack(spacing: Spacing.xs) {
            BrandMark(size: 72)
                .padding(.bottom, 6)
            Text(AppConfig.fullName)
                .font(AppFont.headlineLg)
                .foregroundStyle(.white)
                .shadow(color: .black.opacity(0.3), radius: 6, x: 0, y: 2)

            Text("Precision Care, Instant Access.")
                .font(AppFont.bodyLg)
                .foregroundStyle(.white.opacity(0.9))
                .shadow(color: .black.opacity(0.2), radius: 4, x: 0, y: 1)
        }
        .padding(.top, Spacing.lg)
    }

    // MARK: - Floating Glass Form Card
    private var formCard: some View {
        VStack(spacing: Spacing.md) {
            VStack(spacing: 4) {
                Text(loginTitle)
                    .font(AppFont.headlineLg)
                    .foregroundStyle(AppColor.onSurface)
                Text(loginSubtitle)
                    .font(AppFont.bodyLg)
                    .foregroundStyle(AppColor.onSurfaceVariant)
                    .multilineTextAlignment(.center)
            }

            if step == .phone {
                roleSwitcher
                phoneLookupForm
            } else if step == .profile, let profile {
                profileConfirmation(profile)
            } else if let profile {
                passwordForm(profile)
            }

            if let errorMessage {
                Label(errorMessage, systemImage: "exclamationmark.circle.fill")
                    .font(AppFont.labelSm)
                    .foregroundStyle(AppColor.primary)
                    .frame(maxWidth: .infinity, alignment: .leading)
            }

            if step == .phone {
                HStack {
                    Rectangle().fill(AppColor.outlineVariant).frame(height: 1)
                    Text("OR")
                        .font(AppFont.labelSm)
                        .foregroundStyle(AppColor.onSurfaceVariant)
                    Rectangle().fill(AppColor.outlineVariant).frame(height: 1)
                }

                Button { showGoogleAlert = true } label: {
                    HStack(spacing: Spacing.sm) {
                        Text("G")
                            .font(.system(.headline, weight: .bold))
                            .foregroundStyle(.blue)
                        Text("Continue with Google")
                            .font(AppFont.labelLg)
                    }
                    .foregroundStyle(AppColor.onSurface)
                    .frame(maxWidth: .infinity, minHeight: 52)
                    .background(AppColor.surface)
                    .clipShape(RoundedRectangle(cornerRadius: Radius.sm))
                    .overlay(RoundedRectangle(cornerRadius: Radius.sm).stroke(AppColor.outline, lineWidth: 1))
                }
            }
        }
        .padding(Spacing.lg)
        // Keep the card opaque enough for readable text over either the image
        // or the system dark appearance.
        .background(AppColor.surface.opacity(0.97), in: RoundedRectangle(cornerRadius: Radius.lg))
        .overlay {
            RoundedRectangle(cornerRadius: Radius.lg)
                .stroke(AppColor.outlineVariant, lineWidth: 1)
        }
        .shadow(color: .black.opacity(0.2), radius: 20, x: 0, y: 10)
    }

    private var loginTitle: String {
        switch step {
        case .phone: return "Welcome back"
        case .profile: return "Is this your profile?"
        case .password: return "Enter your password"
        }
    }

    private var loginSubtitle: String {
        switch step {
        case .phone: return "Find your AfyaLink profile using your phone number"
        case .profile: return "Check the name and profile ID before continuing"
        case .password: return "Use your password to continue securely"
        }
    }

    private var phoneLookupForm: some View {
        VStack(alignment: .leading, spacing: 6) {
            Text("Phone Number")
                .font(AppFont.labelLg)
                .foregroundStyle(AppColor.onSurfaceVariant)

            phoneField

            Text("We’ll securely look up the profile linked to this number.")
                .font(AppFont.labelSm)
                .foregroundStyle(AppColor.outline)
                .frame(maxWidth: .infinity, alignment: .center)

            PrimaryButton(
                title: isLookingUpProfile ? "Finding profile…" : "Find My Profile",
                icon: "person.crop.circle.badge.checkmark",
                isEnabled: isPhoneValid && !isLookingUpProfile,
                action: findProfile
            )
            .padding(.top, Spacing.sm)
        }
    }

    private func passwordForm(_ profile: AuthProfile) -> some View {
        VStack(spacing: Spacing.md) {
            HStack(spacing: Spacing.md) {
                Text(profile.initials)
                    .font(.title3.bold())
                    .foregroundStyle(AppColor.primary)
                    .frame(width: 56, height: 56)
                    .background(AppColor.primaryTint, in: Circle())

                VStack(alignment: .leading, spacing: 3) {
                    Text(profile.name)
                        .font(AppFont.headlineMd)
                        .foregroundStyle(AppColor.onSurface)
                    Text("Profile ID: \(profile.profileID)")
                        .font(AppFont.labelSm)
                        .foregroundStyle(AppColor.onSurfaceVariant)
                    Text(profile.phone)
                        .font(AppFont.bodyMd)
                        .foregroundStyle(AppColor.onSurfaceVariant)
                }
                Spacer()
            }
            .padding(Spacing.md)
            .background(AppColor.primaryTint, in: RoundedRectangle(cornerRadius: Radius.md))

            Button("This isn’t my profile") { resetLookup() }
                .font(AppFont.labelLg)
                .foregroundStyle(AppColor.primary)
                .frame(maxWidth: .infinity, alignment: .leading)

            VStack(alignment: .leading, spacing: 6) {
                Text("Password")
                    .font(AppFont.labelLg)
                    .foregroundStyle(AppColor.onSurfaceVariant)

                HStack {
                    Group {
                        if passwordVisible {
                            TextField("Enter password", text: $password)
                        } else {
                            SecureField("Enter password", text: $password)
                        }
                    }
                    .textContentType(.password)

                    Button { passwordVisible.toggle() } label: {
                        Image(systemName: passwordVisible ? "eye.slash" : "eye")
                            .foregroundStyle(AppColor.onSurfaceVariant)
                            .frame(width: 44, height: 44)
                    }
                    .accessibilityLabel(passwordVisible ? "Hide password" : "Show password")
                }
                .inputBox()

                HStack {
                    Text("Use the password for this AfyaLink account")
                        .font(AppFont.labelSm)
                        .foregroundStyle(AppColor.onSurfaceVariant)
                    Spacer()
                    Button("Forgot password?") { goToForgotPassword = true }
                        .font(AppFont.labelLg)
                        .foregroundStyle(AppColor.primary)
                }
            }

            PrimaryButton(
                title: isSigningIn ? "Signing in…" : "Sign In",
                icon: "lock.open.fill",
                isEnabled: !password.isEmpty && !isSigningIn
            ) {
                signIn(profile)
            }
        }
    }

    private func profileConfirmation(_ profile: AuthProfile) -> some View {
        VStack(spacing: Spacing.md) {
            VStack(spacing: Spacing.sm) {
                Text(profile.initials)
                    .font(.title.bold())
                    .foregroundStyle(AppColor.primary)
                    .frame(width: 76, height: 76)
                    .background(AppColor.primaryTint, in: Circle())
                Text(profile.name)
                    .font(AppFont.headlineLg)
                    .foregroundStyle(AppColor.onSurface)
                Text("Profile ID: \(profile.profileID)")
                    .font(AppFont.labelLg)
                    .foregroundStyle(AppColor.primary)
                Text(profile.phone)
                    .font(AppFont.bodyMd)
                    .foregroundStyle(AppColor.onSurfaceVariant)
            }
            .padding(Spacing.lg)
            .frame(maxWidth: .infinity)
            .background(AppColor.primaryTint, in: RoundedRectangle(cornerRadius: Radius.lg))

            PrimaryButton(title: "Yes, This Is My Profile", icon: "checkmark.circle.fill") {
                errorMessage = nil
                withAnimation(.easeInOut) { step = .password }
            }

            SecondaryButton(title: "No, Use Another Number", icon: "arrow.left") {
                resetLookup()
            }
        }
    }

    private func findProfile() {
        phoneFocused = false
        errorMessage = nil
        isLookingUpProfile = true

        Task {
            do {
                let found = try await appState.lookupProfile(phoneDigits: phoneDigits, role: role)
                await MainActor.run {
                    isLookingUpProfile = false
                    profile = found
                    withAnimation(.easeInOut) { step = .profile }
                }
            } catch {
                await MainActor.run {
                    isLookingUpProfile = false
                    errorMessage = error.localizedDescription
                }
            }
        }
    }

    private func signIn(_ profile: AuthProfile) {
        errorMessage = nil
        isSigningIn = true
        Task {
            do {
                let needsOTP = try await appState.signIn(password: password, profile: profile)
                await MainActor.run {
                    isSigningIn = false
                    if needsOTP { goToOTP = true }
                }
            } catch {
                await MainActor.run {
                    isSigningIn = false
                    errorMessage = error.localizedDescription
                }
            }
        }
    }

    private func resetLookup() {
        withAnimation(.easeInOut) { step = .phone }
        profile = nil
        password = ""
        errorMessage = nil
    }

    /// Two-option switch: "I'm a User" / "I'm a Health Provider".
    private var roleSwitcher: some View {
        HStack(spacing: 0) {
            ForEach(UserRole.allCases) { r in
                Button {
                    withAnimation(.spring(response: 0.3, dampingFraction: 0.7)) {
                        role = r
                        resetLookup()
                    }
                } label: {
                    Text(r.rawValue)
                        .font(AppFont.labelLg)
                        .lineLimit(1)
                        .minimumScaleFactor(0.75)   // shrink on narrow phones
                        .foregroundStyle(role == r ? AppColor.primary : AppColor.onSurface)
                        .frame(maxWidth: .infinity, minHeight: 44)
                        .background(role == r ? AppColor.primaryTint : AppColor.surface)
                        .clipShape(RoundedRectangle(cornerRadius: Radius.sm))
                        .overlay {
                            RoundedRectangle(cornerRadius: Radius.sm)
                                .stroke(role == r ? AppColor.primary.opacity(0.28) : AppColor.outlineVariant,
                                        lineWidth: 1)
                        }
                        .shadow(color: .black.opacity(role == r ? 0.08 : 0), radius: 3, y: 1)
                }
                .buttonStyle(.plain)
            }
        }
        .padding(4)
        .background(AppColor.surfaceContainer)
        .clipShape(RoundedRectangle(cornerRadius: Radius.md))
        .overlay {
            RoundedRectangle(cornerRadius: Radius.md)
                .stroke(AppColor.outlineVariant, lineWidth: 1)
        }
    }

    private var phoneField: some View {
        HStack(spacing: Spacing.sm) {
            Text("🇺🇬")
            Text("+256")
                .font(AppFont.bodyLg)
                .fontWeight(.medium)
            Divider()
                .frame(height: 24)
            TextField("770 000 000", text: $phoneDigits)
                .keyboardType(.numberPad)
                .textContentType(.telephoneNumber)
                .focused($phoneFocused)
                .font(AppFont.bodyLg)
                .onChange(of: phoneDigits) { _, newValue in
                    let digits = newValue.filter(\.isNumber)
                    let trimmed = String(digits.prefix(9))
                    if trimmed != phoneDigits { phoneDigits = trimmed }
                }
        }
        .inputBox(isFocused: phoneFocused)
    }
}

#Preview {
    AuthFlowView().environment(AppState())
}
