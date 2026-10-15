import SwiftUI

struct RegistrationFlowView: View {
    @Environment(\.dismiss) private var dismiss
    @Environment(AppState.self) private var appState

    // This view OWNS the view model. The steps only borrow it.
    @State private var vm = RegistrationViewModel()
    @State private var goToOTP = false
    @State private var movingForward = true      // decides which way steps slide
    @State private var isSubmitting = false
    @State private var registrationCreated = false
    @State private var backendError: String?

    var body: some View {
        VStack(spacing: 0) {
            topBar

            ScrollViewReader { proxy in
                ScrollView {
                    VStack(alignment: .leading, spacing: Spacing.lg) {
                        RegistrationStepper(current: vm.step)
                            .id("top")                         // scroll target

                        stepHeader

                        stepContent
                            .id(vm.step)                       // new identity → transition runs
                            .transition(.asymmetric(
                                insertion: .move(edge: movingForward ? .trailing : .leading)
                                    .combined(with: .opacity),
                                removal: .opacity
                            ))

                        if let backendError {
                            Label(backendError, systemImage: "exclamationmark.circle.fill")
                                .font(AppFont.labelSm)
                                .foregroundStyle(AppColor.primary)
                        }
                    }
                    .padding(Spacing.md)
                    .readableWidth(560)
                }
                .scrollDismissesKeyboard(.interactively)
                // Every new step starts at the top.
                .onChange(of: vm.step) { _, _ in
                    withAnimation { proxy.scrollTo("top", anchor: .top) }
                }
            }
            // Pinned above the keyboard and home bar, content scrolls underneath.
            .safeAreaInset(edge: .bottom, spacing: 0) { bottomBar }
        }
        .background(AppColor.background.ignoresSafeArea())
        .toolbar(.hidden, for: .navigationBar)
        .navigationDestination(isPresented: $goToOTP) {
            OTPView(phone: vm.submittedPhone, role: .patient)
        }
    }

    // MARK: Actions

    private func goNext() {
        guard !isSubmitting else { return }
        movingForward = true
        var finished = false
        withAnimation(.easeInOut(duration: 0.3)) { finished = vm.advance() }
        if finished { submitRegistration() }
    }

    private func submitRegistration() {
        guard let profilePhoto = vm.profileImage?.jpegData(compressionQuality: 0.82),
              let coverPhoto = vm.coverImage?.jpegData(compressionQuality: 0.82),
              let relationship = vm.kinRelationship?.rawValue else {
            backendError = "Choose both profile photos and complete the emergency contact details."
            return
        }

        isSubmitting = true
        backendError = nil
        Task {
            do {
                if !registrationCreated {
                    try await appState.registerPatient(PatientRegistrationRequest(
                        name: vm.fullName.trimmingCharacters(in: .whitespacesAndNewlines),
                        phone: vm.submittedPhone,
                        email: vm.email.trimmingCharacters(in: .whitespacesAndNewlines),
                        nin: Validators.normalizeNIN(vm.nin),
                        password: vm.password,
                        profilePhoto: profilePhoto,
                        coverPhoto: coverPhoto,
                        bloodGroup: vm.bloodGroup?.rawValue,
                        systolic: vm.systolic,
                        diastolic: vm.diastolic,
                        heartRate: vm.heartRate,
                        heightCm: vm.heightCm,
                        weightKg: vm.weightKg,
                        allergies: vm.allergies,
                        chronicConditions: vm.conditions,
                        emergencyContactName: vm.kinName,
                        emergencyContactRelationship: relationship,
                        emergencyContactPhone: "+256\(vm.kinPhoneDigits)"
                    ))
                    registrationCreated = true
                }
                try await appState.sendPatientRegistrationCode()
                isSubmitting = false
                goToOTP = true
            } catch {
                isSubmitting = false
                backendError = error.localizedDescription
            }
        }
    }

    private func goBack() {
        movingForward = false
        var leave = false
        withAnimation(.easeInOut(duration: 0.3)) { leave = vm.goBack() }
        if leave { dismiss() }                         // on step 1, leave the flow
    }

    private func skipVitals() {
        movingForward = true
        withAnimation(.easeInOut(duration: 0.3)) { vm.skipVitals() }
    }

    // MARK: Pieces

    @ViewBuilder
    private var stepContent: some View {
        switch vm.step {
        case .bio:       BioStepView(vm: vm)
        case .vitals:    VitalsStepView(vm: vm)
        case .nextOfKin: NextOfKinStepView(vm: vm)
        }
    }

    private var topBar: some View {
        HStack {
            Button { goBack() } label: {
                Image(systemName: vm.isFirstStep ? "xmark" : "chevron.left")
                    .font(.title3)
                    .foregroundStyle(AppColor.primary)
                    .frame(width: 44, height: 44)
            }
            .accessibilityLabel(vm.isFirstStep ? "Close" : "Back")

            Spacer()
            Text("Create account")
                .font(AppFont.headlineMd)
                .foregroundStyle(AppColor.primary)
            Spacer()

            // Invisible twin of the left button, keeps the title truly centred.
            Color.clear.frame(width: 44, height: 44)
        }
        .padding(.horizontal, Spacing.sm)
        .background(AppColor.background)
        .overlay(alignment: .bottom) { Divider() }
    }

    /// Icon tile + REQUIRED/OPTIONAL label + title + subtitle.
    private var stepHeader: some View {
        HStack(alignment: .top, spacing: Spacing.md) {
            Image(systemName: vm.step.icon)
                .font(.title2)
                .foregroundStyle(AppColor.primary)
                .frame(width: 48, height: 48)
                .background(AppColor.primaryTint)
                .clipShape(RoundedRectangle(cornerRadius: Radius.md))

            VStack(alignment: .leading, spacing: 4) {
                Text(vm.step.isOptional ? "OPTIONAL STEP" : "REQUIRED")
                    .font(AppFont.labelSm)
                    .tracking(1)
                    .foregroundStyle(vm.step.isOptional ? AppColor.secondary : AppColor.primary)

                Text(vm.step.title)
                    .font(AppFont.headlineLg)
                    .foregroundStyle(AppColor.onSurface)

                Text(vm.step.subtitle)
                    .font(AppFont.bodyMd)
                    .foregroundStyle(AppColor.onSurfaceVariant)
            }
        }
    }

    private var bottomBar: some View {
        VStack(spacing: Spacing.sm) {
            HStack(spacing: Spacing.md) {
                if !vm.isFirstStep {
                    SecondaryButton(title: "Back", icon: "chevron.left") { goBack() }
                        .frame(maxWidth: 140)
                }
                PrimaryButton(title: isSubmitting ? "PLEASE WAIT…" :
                              (registrationCreated ? "SEND VERIFICATION CODE" :
                               (vm.isLastStep ? "CREATE ACCOUNT" : "NEXT")),
                              icon: vm.isLastStep ? "checkmark" : "arrow.right") {
                    goNext()
                }
            }

            if vm.step == .vitals {
                Button("Skip this step") { skipVitals() }
                    .font(AppFont.labelLg)
                    .foregroundStyle(AppColor.primary)
                    .frame(minHeight: 44)
            } else if vm.isFirstStep {
                HStack(spacing: 4) {
                    Text("Already registered?").foregroundStyle(AppColor.onSurfaceVariant)
                    Button("Sign In") { dismiss() }
                        .fontWeight(.bold)
                        .foregroundStyle(AppColor.primary)
                }
                .font(AppFont.bodyMd)
                .frame(minHeight: 44)
            }
        }
        .padding(.horizontal, Spacing.md)
        .padding(.top, Spacing.md)
        .padding(.bottom, Spacing.sm)
        .readableWidth(560)
        .background(
            AppColor.surface
                .shadow(color: .black.opacity(0.08), radius: 8, y: -2)
                .ignoresSafeArea(edges: .bottom)
        )
    }
}

// MARK: - Stepper

/// ① ──── ② ──── ③   Done = red with a tick · Current = red with a halo · Upcoming = grey
private struct RegistrationStepper: View {
    let current: RegistrationStep

    var body: some View {
        HStack(alignment: .top, spacing: 0) {
            ForEach(RegistrationStep.allCases, id: \.rawValue) { step in
                node(for: step)
                if step != .nextOfKin {
                    connector(after: step)
                }
            }
        }
        .padding(.vertical, Spacing.md)
        .padding(.horizontal, Spacing.sm)
        .frame(maxWidth: .infinity)
        .cardStyle(radius: Radius.lg)
        .animation(.easeInOut(duration: 0.3), value: current)
        .accessibilityElement(children: .ignore)
        .accessibilityLabel("Step \(current.rawValue + 1) of \(RegistrationStep.allCases.count): \(current.title)")
    }

    private func node(for step: RegistrationStep) -> some View {
        let isDone = step.rawValue < current.rawValue
        let isCurrent = step == current

        return VStack(spacing: 6) {
            ZStack {
                Circle()
                    .fill(isDone || isCurrent ? AppColor.primary : AppColor.surfaceHigh)

                if isDone {
                    Image(systemName: "checkmark")
                        .font(.caption.weight(.bold))
                        .foregroundStyle(Color.white)
                } else {
                    Text("\(step.rawValue + 1)")
                        .font(AppFont.labelLg)
                        .foregroundStyle(isCurrent ? Color.white : AppColor.onSurfaceVariant)
                }
            }
            .frame(width: 32, height: 32)
            .overlay(Circle().stroke(AppColor.primaryFixed, lineWidth: isCurrent ? 5 : 0)) // halo

            Text(step.shortTitle)
                .font(AppFont.labelSm)
                .foregroundStyle(isCurrent ? AppColor.primary : AppColor.onSurfaceVariant)
                .lineLimit(1)
                .minimumScaleFactor(0.8)
        }
        .frame(width: 76)
    }

    /// The line between two circles, red once the step before it is done.
    private func connector(after step: RegistrationStep) -> some View {
        Rectangle()
            .fill(step.rawValue < current.rawValue ? AppColor.primary : AppColor.surfaceHigh)
            .frame(height: 3)
            .frame(maxWidth: .infinity)
            .padding(.top, 14)          // lines up with the circles' centre (32 / 2 − 3 / 2)
    }
}

#Preview {
    NavigationStack { RegistrationFlowView() }
        .environment(AppState())
}
