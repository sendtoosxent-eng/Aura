import SwiftUI
import UniformTypeIdentifiers

struct ProviderRegistrationDetailsView: View {
    private enum Step: Hashable {
        case workplace, identity, contact, verification, regulatory, documents, review

        var title: String {
            switch self {
            case .workplace: return "Verified Workplace"
            case .identity: return "Identity"
            case .contact: return "Contact & Password"
            case .verification: return "Verify Account"
            case .regulatory: return "Registration Details"
            case .documents: return "Documents"
            case .review: return "Review & Submit"
            }
        }
    }

    private enum UploadTarget: Equatable {
        case profilePhoto
        case document(String)
    }

    let applicationType: ProviderApplicationType

    @Environment(AppState.self) private var appState
    @Environment(\.dismiss) private var dismiss
    private let api = AfyaLinkAPI.shared
    @State private var stepIndex = 0
    @State private var movingForward = true
    @State private var showValidationMessage = false
    @State private var backendError: String?
    @State private var isWorking = false
    @State private var identityCreated = false

    // Identity — mirrors createIdentity / UpdateRegistrationIdentityRequest.
    @State private var profilePhotoName: String?
    @State private var profilePhotoURL: URL?
    @State private var firstName = ""
    @State private var middleName = ""
    @State private var lastName = ""
    @State private var dateOfBirth = Calendar.current.date(byAdding: .year, value: -25, to: Date()) ?? Date()
    @State private var sex = ""
    @State private var nationality = "Ugandan"
    @State private var nin = ""

    // Account.
    @State private var phone = "+256 "
    @State private var email = ""
    @State private var password = ""
    @State private var passwordConfirmation = ""
    @State private var emailVerified = false
    @State private var phoneOTPSent = false
    @State private var phoneOTP = ""
    @State private var phoneVerified = false

    // Professional-only workplace and regulatory fields.
    @State private var workplaceName = ""
    @State private var workplaceID: Int?
    @State private var facilities: [RegistrationFacilityDTO] = []
    @State private var cadre = ""
    @State private var specialty = ""
    @State private var regulatoryBody = ""
    @State private var registrationNumber = ""
    @State private var licenceNumber = ""
    @State private var licenceExpiry = Date().addingTimeInterval(365 * 86_400)
    @State private var yearsExperience = ""

    // Facility-only regulatory/public fields.
    @State private var facilityName = ""
    @State private var facilityType = ""
    @State private var ownership = ""
    @State private var district = "Kampala"
    @State private var town = ""
    @State private var address = ""
    @State private var latitude: Double?
    @State private var longitude: Double?
    @State private var facilityInCharge = ""
    @State private var facilityPhone = "+256 "
    @State private var facilityEmail = ""
    @State private var open24Hours = false
    @State private var emergencyAvailable = false
    @State private var selectedServices: Set<String> = []
    @State private var otherServices = ""

    // Files and final declaration.
    @State private var documents: [String: URL] = [:]
    @State private var uploadTarget: UploadTarget?
    @State private var showFileImporter = false
    @State private var showLocationPicker = false
    @State private var acceptedDeclaration = false
    @State private var submitted = false

    private let cadres = ["Doctor", "Nurse", "Midwife", "Dentist", "Pharmacist",
                          "Clinical Officer", "Laboratory Professional", "Physiotherapist", "Other"]
    private let facilityTypes = ["Hospital", "Clinic", "Health Centre", "Pharmacy",
                                 "Dental Clinic", "Laboratory", "Maternity Centre", "Other"]
    private let ownershipTypes = ["Private", "Government", "NGO / Mission", "Community"]
    private let facilityServices = ["Outpatient care", "Inpatient care", "Maternity",
                                    "Child health and immunisation", "Family planning",
                                    "Laboratory tests", "Imaging", "Pharmacy", "Dental care", "Surgery"]

    private var steps: [Step] {
        applicationType == .professional
            ? [.workplace, .identity, .contact, .verification, .regulatory, .documents, .review]
            : [.identity, .contact, .verification, .regulatory, .documents, .review]
    }

    private var step: Step { steps[stepIndex] }
    private var totalSteps: Int { steps.count + 1 }
    private var displayedStep: Int { stepIndex + 2 }
    private var requiredDocuments: [(key: String, title: String, description: String, optional: Bool)] {
        if applicationType == .facility {
            return [
                ("operating_licence", "Facility operating licence", "Current licence or operating permit", false),
                ("registration_certificate", "Certificate of registration", "Organisation registration document", false),
                ("responsible_officer_id", "Responsible officer's ID", "National ID of the responsible person", false),
                ("facility_photo", "Facility photo", "Public photo of the facility building", true),
                ("other_supporting", "Other supporting document", "Any additional supporting evidence", true)
            ]
        }
        return [
            ("national_id", "National ID (front and back)", "Clear scan or photo showing both sides", false),
            ("practising_licence", "Practising licence", "Current licence issued by your council", false),
            ("academic_certificate", "Academic certificate", "Your main professional qualification", false),
            ("other_supporting", "Other supporting document", "For example a recommendation letter", true)
        ]
    }

    var body: some View {
        VStack(spacing: 0) {
            topBar

            VStack(alignment: .leading, spacing: Spacing.md) {
                providerStepper
                stepHeader

                if showValidationMessage {
                    Label("Complete the required fields on this step before continuing.",
                          systemImage: "exclamationmark.circle.fill")
                        .font(AppFont.labelSm)
                        .foregroundStyle(AppColor.primary)
                        .transition(.opacity)
                }
                if let backendError {
                    Label(backendError, systemImage: "wifi.exclamationmark")
                        .font(AppFont.labelSm)
                        .foregroundStyle(AppColor.primary)
                }
            }
            .padding(.horizontal, Spacing.md)
            .padding(.top, Spacing.sm)
            .readableWidth(600)

            Form { stepContent }
                .id(step)
                .transition(.asymmetric(
                    insertion: .move(edge: movingForward ? .trailing : .leading).combined(with: .opacity),
                    removal: .opacity
                ))
                .scrollContentBackground(.hidden)
                .scrollDismissesKeyboard(.interactively)
        }
        .safeAreaInset(edge: .bottom, spacing: 0) { bottomBar }
        .background(AppColor.background.ignoresSafeArea())
        .toolbar(.hidden, for: .navigationBar)
        .fileImporter(isPresented: $showFileImporter,
                      allowedContentTypes: uploadTarget == .profilePhoto ? [.image] : [.pdf, .image],
                      allowsMultipleSelection: false,
                      onCompletion: handleImport)
        .sheet(isPresented: $showLocationPicker, onDismiss: useSelectedLocation) {
            NavigationStack { LocationPickerView() }
        }
        .alert("Application submitted", isPresented: $submitted) {
            Button("Open Provider Portal") {
                appState.completeLogin(as: .provider)
                dismiss()
            }
        } message: {
            Text("Your application is pending AfyaLink review. Keep your registration reference for support enquiries.")
        }
        .task { await loadFacilitiesIfNeeded() }
    }

    private var topBar: some View {
        HStack {
            Button { goBack() } label: {
                Image(systemName: "chevron.left")
                    .font(.title3)
                    .foregroundStyle(AppColor.primary)
                    .frame(width: 44, height: 44)
            }
            .accessibilityLabel("Back")

            Spacer()
            Text("Provider registration")
                .font(AppFont.headlineMd)
                .foregroundStyle(AppColor.primary)
            Spacer()
            Color.clear.frame(width: 44, height: 44)
        }
        .padding(.horizontal, Spacing.sm)
        .background(AppColor.background)
        .overlay(alignment: .bottom) { Divider() }
    }

    private var providerStepper: some View {
        ScrollView(.horizontal, showsIndicators: false) {
            HStack(alignment: .top, spacing: 0) {
                ForEach(Array(flowLabels.enumerated()), id: \.offset) { index, label in
                    VStack(spacing: 5) {
                        ZStack {
                            Circle()
                                .fill(index <= displayedStep - 1 ? AppColor.primary : AppColor.surfaceHigh)
                            if index < displayedStep - 1 {
                                Image(systemName: "checkmark")
                                    .font(.caption2.bold())
                                    .foregroundStyle(.white)
                            } else {
                                Text("\(index + 1)")
                                    .font(AppFont.labelSm)
                                    .foregroundStyle(index == displayedStep - 1 ? .white : AppColor.onSurfaceVariant)
                            }
                        }
                        .frame(width: 28, height: 28)
                        .overlay(Circle().stroke(AppColor.primaryFixed,
                                                 lineWidth: index == displayedStep - 1 ? 4 : 0))
                        Text(label)
                            .font(.system(size: 9, weight: .semibold))
                            .foregroundStyle(index == displayedStep - 1 ? AppColor.primary : AppColor.onSurfaceVariant)
                            .lineLimit(1)
                    }
                    .frame(width: 54)

                    if index < flowLabels.count - 1 {
                        Rectangle()
                            .fill(index < displayedStep - 1 ? AppColor.primary : AppColor.surfaceHigh)
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
        .animation(.easeInOut(duration: 0.3), value: stepIndex)
    }

    private var flowLabels: [String] {
        ["Type"] + steps.map {
            switch $0 {
            case .workplace: return "Workplace"
            case .identity: return "Identity"
            case .contact: return "Account"
            case .verification: return "Verify"
            case .regulatory: return applicationType == .facility ? "Facility" : "Licence"
            case .documents: return "Documents"
            case .review: return "Review"
            }
        }
    }

    private var stepHeader: some View {
        HStack(alignment: .top, spacing: Spacing.md) {
            Image(systemName: stepIcon)
                .font(.title2)
                .foregroundStyle(AppColor.primary)
                .frame(width: 48, height: 48)
                .background(AppColor.primaryTint)
                .clipShape(RoundedRectangle(cornerRadius: Radius.md))

            VStack(alignment: .leading, spacing: 4) {
                Text("REQUIRED")
                    .font(AppFont.labelSm)
                    .tracking(1)
                    .foregroundStyle(AppColor.primary)
                Text(step.title)
                    .font(AppFont.headlineLg)
                    .foregroundStyle(AppColor.onSurface)
                Text(stepSubtitle)
                    .font(AppFont.bodyMd)
                    .foregroundStyle(AppColor.onSurfaceVariant)
            }
        }
    }

    private var stepIcon: String {
        switch step {
        case .workplace: return "building.2"
        case .identity: return "person.text.rectangle"
        case .contact: return "lock.shield"
        case .verification: return "checkmark.seal"
        case .regulatory: return applicationType.icon
        case .documents: return "doc.text.fill"
        case .review: return "checklist"
        }
    }

    private var stepSubtitle: String {
        switch step {
        case .workplace: return "Choose the AfyaLink-verified facility where you practise."
        case .identity: return "Enter the responsible person's details exactly as shown on their National ID."
        case .contact: return "Create secure sign-in details for this provider account."
        case .verification: return "Confirm the email address and phone number attached to the account."
        case .regulatory:
            return applicationType == .facility
                ? "Tell us about the facility, its location, licence and services."
                : "Provide your professional registration and current practising licence."
        case .documents: return "Upload the documents AfyaLink needs to verify this application."
        case .review: return "Check every detail and declare that the application is accurate."
        }
    }

    private var bottomBar: some View {
        VStack(spacing: Spacing.sm) {
            HStack {
                SecondaryButton(title: "Back", icon: "chevron.left", action: goBack)
                    .frame(maxWidth: 140)
                PrimaryButton(title: isWorking ? "PLEASE WAIT…" : (step == .review ? "SUBMIT APPLICATION" : "NEXT"),
                              icon: step == .review ? "paperplane.fill" : "arrow.right",
                              isEnabled: !isWorking,
                              action: goNext)
            }
            Text("Step \(displayedStep) of \(totalSteps) · \(applicationType.rawValue)")
                .font(AppFont.labelSm)
                .foregroundStyle(AppColor.onSurfaceVariant)
        }
        .padding(.horizontal, Spacing.md)
        .padding(.top, Spacing.md)
        .padding(.bottom, Spacing.sm)
        .readableWidth(600)
        .background(
            AppColor.surface
                .shadow(color: .black.opacity(0.08), radius: 8, y: -2)
                .ignoresSafeArea(edges: .bottom)
        )
    }

    @ViewBuilder
    private var stepContent: some View {
        switch step {
        case .workplace: workplaceStep
        case .identity: identityStep
        case .contact: contactStep
        case .verification: verificationStep
        case .regulatory:
            if applicationType == .facility { facilityStep } else { professionalStep }
        case .documents: documentsStep
        case .review: reviewStep
        }
    }

    private var workplaceStep: some View {
        Group {
            Section("Verified workplace") {
                Picker("Facility", selection: $workplaceID) {
                    Text("Select your workplace").tag(Int?.none)
                    ForEach(facilities) { facility in
                        Text("\(facility.name) — \([facility.town, facility.district].compactMap { $0 }.joined(separator: ", "))")
                            .tag(Optional(facility.id))
                    }
                }
                .onChange(of: workplaceID) { _, selectedID in
                    workplaceName = facilities.first { $0.id == selectedID }?.name ?? ""
                }
                if facilities.isEmpty {
                    ProgressView("Loading verified facilities…")
                }
                Label("Only facilities already verified by AfyaLink can be selected.", systemImage: "checkmark.seal.fill")
                    .font(AppFont.bodyMd)
                    .foregroundStyle(AppColor.onSurfaceVariant)
            }
            Section {
                Text("Cannot find your workplace? It must register and be approved first. Go back and choose Healthcare Facility if you manage it.")
                    .font(AppFont.bodyMd)
                    .foregroundStyle(AppColor.onSurfaceVariant)
            }
        }
    }

    private var identityStep: some View {
        Group {
            Section("Profile photo") {
                uploadButton(title: profilePhotoName ?? "Upload profile photo", key: nil)
                Text("Required: JPG, PNG or WebP, maximum 5 MB. This is shown publicly only after approval.")
                    .font(AppFont.labelSm).foregroundStyle(AppColor.onSurfaceVariant)
            }
            Section("Personal information — as on National ID") {
                TextField("First name", text: $firstName)
                TextField("Middle name (optional)", text: $middleName)
                TextField("Last name", text: $lastName)
                DatePicker("Date of birth", selection: $dateOfBirth,
                           in: ...adultCutoff, displayedComponents: .date)
                Picker("Sex", selection: $sex) {
                    Text("Select").tag("")
                    Text("Male").tag("male")
                    Text("Female").tag("female")
                }
                TextField("Nationality", text: $nationality)
                TextField("National Identification Number (NIN)", text: $nin)
                    .textInputAutocapitalization(.characters)
                    .autocorrectionDisabled()
                    .onChange(of: nin) { _, value in
                        let clean = String(value.uppercased().filter { $0.isLetter || $0.isNumber }.prefix(14))
                        if clean != nin { nin = clean }
                    }
                Text("NIN must contain 14 characters and begin with CM or CF. It is never shown publicly.")
                    .font(AppFont.labelSm).foregroundStyle(ninIsValid || nin.isEmpty ? AppColor.onSurfaceVariant : AppColor.primary)
            }
        }
    }

    private var contactStep: some View {
        Group {
            Section("Contact details") {
                TextField("Phone number", text: $phone).keyboardType(.phonePad)
                TextField("Email address", text: $email)
                    .keyboardType(.emailAddress).textInputAutocapitalization(.never).autocorrectionDisabled()
            }
            Section("Create a password") {
                SecureField("Password", text: $password)
                SecureField("Confirm password", text: $passwordConfirmation)
                Label("At least 8 characters with uppercase, lowercase, a number and a symbol.",
                      systemImage: passwordIsValid ? "checkmark.circle.fill" : "info.circle")
                    .font(AppFont.labelSm)
                    .foregroundStyle(passwordIsValid ? AppColor.success : AppColor.onSurfaceVariant)
            }
            Section {
                Label("Your identity and contact details are used for registration, security and verification.",
                      systemImage: "lock.shield")
                    .font(AppFont.bodyMd).foregroundStyle(AppColor.onSurfaceVariant)
            }
        }
    }

    private var verificationStep: some View {
        Group {
            Section("Email verification") {
                LabeledContent("Email", value: email)
                Button(emailVerified ? "Email verified" : "Check verification status") {
                    refreshVerificationStatus()
                }
                .disabled(emailVerified || isWorking)
                .foregroundStyle(emailVerified ? AppColor.success : AppColor.primary)
                if !emailVerified {
                    Button("Resend verification email") { resendVerificationEmail() }
                        .disabled(isWorking)
                }
            }
            Section("Phone verification") {
                LabeledContent("Phone", value: phone)
                if !phoneOTPSent {
                    Button("Send phone OTP") { sendPhoneOTP() }
                        .disabled(isWorking)
                } else if !phoneVerified {
                    TextField("Enter verification code", text: $phoneOTP)
                        .keyboardType(.numberPad)
                        .onChange(of: phoneOTP) { _, value in
                            let clean = String(value.filter(\.isNumber).prefix(6))
                            if clean != phoneOTP { phoneOTP = clean }
                        }
                    Button("Verify phone") { verifyPhoneOTP() }
                        .disabled(phoneOTP.count != 6 || isWorking)
                } else {
                    Label("Phone verified", systemImage: "checkmark.seal.fill")
                        .foregroundStyle(AppColor.success)
                }
                Text("Enter the six-digit code sent by AfyaLink.")
                    .font(AppFont.labelSm).foregroundStyle(AppColor.onSurfaceVariant)
            }
        }
    }

    private var professionalStep: some View {
        Group {
            Section("Professional profile") {
                Picker("Profession / cadre", selection: $cadre) {
                    Text("Select profession").tag("")
                    ForEach(cadres, id: \.self) { Text($0).tag($0) }
                }
                TextField("Specialty (optional)", text: $specialty)
            }
            Section("Registration and licence") {
                TextField("Regulatory body", text: $regulatoryBody)
                TextField("Registration number", text: $registrationNumber)
                    .textInputAutocapitalization(.characters)
                TextField("Practising licence number", text: $licenceNumber)
                    .textInputAutocapitalization(.characters)
                DatePicker("Licence expiry date", selection: $licenceExpiry,
                           in: Date()..., displayedComponents: .date)
                TextField("Years of experience (optional)", text: $yearsExperience)
                    .keyboardType(.numberPad)
                    .onChange(of: yearsExperience) { _, value in
                        let clean = String(value.filter(\.isNumber).prefix(2))
                        if clean != yearsExperience { yearsExperience = clean }
                    }
            }
        }
    }

    private var facilityStep: some View {
        Group {
            Section("Facility identity") {
                TextField("Facility name — as on licence", text: $facilityName)
                Picker("Facility type", selection: $facilityType) {
                    Text("Select type").tag("")
                    ForEach(facilityTypes, id: \.self) { Text($0).tag($0) }
                }
                Picker("Ownership", selection: $ownership) {
                    Text("Select ownership").tag("")
                    ForEach(ownershipTypes, id: \.self) { Text($0).tag($0) }
                }
            }
            Section("Location") {
                TextField("District", text: $district)
                TextField("Town / area", text: $town)
                TextField("Physical address — plot, street and landmark", text: $address, axis: .vertical)
                Button { showLocationPicker = true } label: {
                    Label(latitude == nil ? "Pin facility on map (optional)" : "Map location selected",
                          systemImage: latitude == nil ? "map" : "checkmark.circle.fill")
                }
            }
            Section("Licensing and management") {
                TextField("Operating licence number", text: $licenceNumber)
                    .textInputAutocapitalization(.characters)
                DatePicker("Licence expiry date", selection: $licenceExpiry,
                           in: Date()..., displayedComponents: .date)
                TextField("Facility in-charge (optional)", text: $facilityInCharge)
            }
            Section("Public contact and availability") {
                TextField("Facility phone", text: $facilityPhone).keyboardType(.phonePad)
                TextField("Facility email (optional)", text: $facilityEmail)
                    .keyboardType(.emailAddress).textInputAutocapitalization(.never)
                Toggle("Open 24 hours, every day", isOn: $open24Hours)
                Toggle("Emergency services available", isOn: $emergencyAvailable)
            }
            Section("Key services offered") {
                ForEach(facilityServices, id: \.self) { service in
                    Toggle(service, isOn: Binding(
                        get: { selectedServices.contains(service) },
                        set: { enabled in
                            if enabled { selectedServices.insert(service) }
                            else { selectedServices.remove(service) }
                        }
                    ))
                }
                TextField("Other services (optional)", text: $otherServices, axis: .vertical)
            }
        }
    }

    private var documentsStep: some View {
        Group {
            Section {
                Text("Upload PDF, JPG, PNG or WebP files. Each file must be 5 MB or smaller.")
                    .font(AppFont.bodyMd).foregroundStyle(AppColor.onSurfaceVariant)
            }
            ForEach(requiredDocuments, id: \.key) { document in
                Section(document.title + (document.optional ? " (optional)" : "")) {
                    Text(document.description)
                        .font(AppFont.bodyMd).foregroundStyle(AppColor.onSurfaceVariant)
                    uploadButton(title: documents[document.key]?.lastPathComponent ?? "Choose file", key: document.key)
                }
            }
        }
    }

    private var reviewStep: some View {
        Group {
            Section("Account holder") {
                reviewRow("Name", fullName)
                reviewRow("Account", applicationType.rawValue)
                reviewRow("Phone", phone)
                reviewRow("Email", email)
                reviewRow("NIN", maskedNIN)
            }
            if applicationType == .facility {
                Section("Facility") {
                    reviewRow("Name", facilityName)
                    reviewRow("Type", facilityType)
                    reviewRow("Ownership", ownership)
                    reviewRow("Location", "\(town), \(district)")
                    reviewRow("Licence", licenceNumber)
                    reviewRow("Services", "\(selectedServices.count) selected")
                }
            } else {
                Section("Professional registration") {
                    reviewRow("Workplace", workplaceName)
                    reviewRow("Cadre", cadre)
                    reviewRow("Specialty", specialty.isEmpty ? "Not provided" : specialty)
                    reviewRow("Council", regulatoryBody)
                    reviewRow("Registration", registrationNumber)
                    reviewRow("Licence", licenceNumber)
                }
            }
            Section("Documents") {
                LabeledContent("Uploaded", value: "\(documents.count)")
            }
            Section {
                Toggle("I declare that the information and documents are true and complete, and I understand that false information may lead to rejection or removal.",
                       isOn: $acceptedDeclaration)
            }
        }
    }

    private func uploadButton(title: String, key: String?) -> some View {
        Button {
            uploadTarget = key.map(UploadTarget.document) ?? .profilePhoto
            showFileImporter = true
        } label: {
            Label(title, systemImage: title == "Choose file" || title.contains("Upload") ? "doc.badge.plus" : "checkmark.circle.fill")
        }
    }

    private func reviewRow(_ label: String, _ value: String) -> some View {
        LabeledContent(label, value: value)
    }

    private var fullName: String {
        [firstName, middleName, lastName]
            .map { $0.trimmingCharacters(in: .whitespaces) }
            .filter { !$0.isEmpty }
            .joined(separator: " ")
    }

    private var maskedNIN: String {
        guard nin.count >= 4 else { return nin }
        return String(repeating: "•", count: max(0, nin.count - 4)) + nin.suffix(4)
    }

    private var adultCutoff: Date {
        Calendar.current.date(byAdding: .year, value: -18, to: Date()) ?? Date()
    }

    private var ninIsValid: Bool {
        guard nin.count == 14, nin.hasPrefix("CM") || nin.hasPrefix("CF") else { return false }
        return nin.allSatisfy { $0.isLetter || $0.isNumber }
    }

    private var emailIsValid: Bool {
        email.contains("@") && email.contains(".")
    }

    private var phoneIsValid: Bool {
        phone.filter(\.isNumber).count >= 10
    }

    private var passwordIsValid: Bool {
        password.count >= 8
            && password.contains(where: \.isUppercase)
            && password.contains(where: \.isLowercase)
            && password.contains(where: \.isNumber)
            && password.contains { !$0.isLetter && !$0.isNumber }
            && password == passwordConfirmation
    }

    private var requiredFilesUploaded: Bool {
        requiredDocuments.filter { !$0.optional }.allSatisfy { documents[$0.key] != nil }
    }

    private var currentStepIsValid: Bool {
        switch step {
        case .workplace:
            return workplaceID != nil
        case .identity:
            return profilePhotoURL != nil && !firstName.isEmpty && !lastName.isEmpty
                && !sex.isEmpty && !nationality.isEmpty && ninIsValid
        case .contact:
            return phoneIsValid && emailIsValid && passwordIsValid
        case .verification:
            return emailVerified && phoneVerified
        case .regulatory:
            if applicationType == .professional {
                return !cadre.isEmpty && !regulatoryBody.isEmpty && !registrationNumber.isEmpty
                    && !licenceNumber.isEmpty && licenceExpiry >= Calendar.current.startOfDay(for: Date())
            }
            return !facilityName.isEmpty && !facilityType.isEmpty && !ownership.isEmpty
                && !district.isEmpty && !town.isEmpty && !address.isEmpty
                && !licenceNumber.isEmpty && facilityPhone.filter(\.isNumber).count >= 10
        case .documents:
            return requiredFilesUploaded
        case .review:
            return acceptedDeclaration
        }
    }

    private func goNext() {
        guard currentStepIsValid else {
            withAnimation { showValidationMessage = true }
            return
        }
        showValidationMessage = false
        backendError = nil
        if step == .review {
            submitApplication()
            return
        }
        if step == .contact && !identityCreated {
            createIdentity()
            return
        }
        advanceStep()
    }

    private func advanceStep() {
        guard stepIndex < steps.count - 1 else { return }
        movingForward = true
        withAnimation(.easeInOut(duration: 0.3)) { stepIndex += 1 }
    }

    private func goBack() {
        showValidationMessage = false
        movingForward = false
        if stepIndex > 0 {
            withAnimation(.easeInOut(duration: 0.3)) { stepIndex -= 1 }
        } else {
            dismiss()
        }
    }

    private func handleImport(_ result: Result<[URL], Error>) {
        guard case .success(let urls) = result, let url = urls.first,
              let uploadTarget else { return }
        switch uploadTarget {
        case .profilePhoto:
            profilePhotoURL = url
            profilePhotoName = url.lastPathComponent
        case .document(let key): documents[key] = url
        }
        self.uploadTarget = nil
    }

    private func useSelectedLocation() {
        let location = appState.selectedCareLocation
        latitude = location.latitude
        longitude = location.longitude
        if town.isEmpty { town = location.name }
    }

    private func loadFacilitiesIfNeeded() async {
        guard applicationType == .professional, facilities.isEmpty else { return }
        do {
            facilities = try await api.registrationFacilities()
        } catch {
            backendError = error.localizedDescription
        }
    }

    private func createIdentity() {
        guard let profilePhotoURL else { return }
        isWorking = true
        Task {
            do {
                let response = try await api.createProviderIdentity(ProviderIdentityRequest(
                    applicationType: applicationType.backendValue,
                    healthcareProviderId: workplaceID,
                    firstName: firstName.trimmingCharacters(in: .whitespaces),
                    middleName: middleName.trimmingCharacters(in: .whitespaces).nilIfEmpty,
                    lastName: lastName.trimmingCharacters(in: .whitespaces),
                    dateOfBirth: Self.dateFormatter.string(from: dateOfBirth),
                    sex: sex,
                    nationality: nationality,
                    profilePhoto: profilePhotoURL,
                    phone: normalizedPhone(phone),
                    email: email.trimmingCharacters(in: .whitespacesAndNewlines).lowercased(),
                    nin: nin,
                    password: password,
                    passwordConfirmation: passwordConfirmation
                ))
                identityCreated = true
                emailVerified = response.data.emailVerified ?? false
                phoneVerified = response.data.phoneVerified ?? false
                isWorking = false
                advanceStep()
            } catch {
                isWorking = false
                backendError = error.localizedDescription
            }
        }
    }

    private func refreshVerificationStatus() {
        runVerificationRequest {
            let status = try await api.providerRegistrationStatus()
            emailVerified = status.emailVerified ?? false
            phoneVerified = status.phoneVerified ?? false
            if !emailVerified {
                backendError = "Your email is not verified yet. Open the AfyaLink email link, then check again."
            }
        }
    }

    private func resendVerificationEmail() {
        runVerificationRequest {
            let response = try await api.resendProviderEmail()
            backendError = response.message
        }
    }

    private func sendPhoneOTP() {
        runVerificationRequest {
            let response = try await api.sendProviderPhoneCode()
            phoneOTPSent = true
            backendError = [response.message, response.debugCode.map { "Local code: \($0)" }]
                .compactMap { $0 }
                .joined(separator: " ")
        }
    }

    private func verifyPhoneOTP() {
        runVerificationRequest {
            let response = try await api.verifyProviderPhone(code: phoneOTP)
            phoneVerified = response.verified ?? true
            backendError = response.message
        }
    }

    private func runVerificationRequest(_ operation: @escaping () async throws -> Void) {
        isWorking = true
        backendError = nil
        Task {
            do {
                try await operation()
            } catch {
                backendError = error.localizedDescription
            }
            isWorking = false
        }
    }

    private func submitApplication() {
        isWorking = true
        backendError = nil
        let services = selectedServices.sorted()
        Task {
            do {
                let response = try await api.submitProviderApplication(ProviderApplicationRequest(
                    applicationType: applicationType.backendValue,
                    healthcareProviderId: workplaceID,
                    regulatoryFields: regulatoryFields,
                    services: services,
                    documents: documents
                ))
                updateProviderProfile(reference: response.reference, services: services)
                isWorking = false
                submitted = true
            } catch {
                isWorking = false
                backendError = error.localizedDescription
            }
        }
    }

    private var regulatoryFields: [String: String] {
        var fields = [
            "licence_number": licenceNumber,
            "licence_expiry": Self.dateFormatter.string(from: licenceExpiry)
        ]
        if applicationType == .professional {
            fields["cadre"] = cadre
            fields["specialty"] = specialty
            fields["regulatory_body"] = regulatoryBody
            fields["registration_number"] = registrationNumber
            fields["years_experience"] = yearsExperience
        } else {
            fields["name"] = facilityName
            fields["provider_type"] = facilityType
            fields["ownership"] = ownership
            fields["district"] = district
            fields["town"] = town
            fields["address"] = address
            fields["latitude"] = latitude.map { String($0) }
            fields["longitude"] = longitude.map { String($0) }
            fields["in_charge"] = facilityInCharge
            fields["phone"] = normalizedPhone(facilityPhone)
            fields["email"] = facilityEmail
            fields["open_24_hours"] = open24Hours ? "1" : "0"
            fields["emergency_available"] = emergencyAvailable ? "1" : "0"
            fields["services_other"] = otherServices
        }
        return fields.filter { !$0.value.isEmpty }
    }

    private func updateProviderProfile(reference: String, services: [String]) {
        let displayName = applicationType == .facility ? facilityName : fullName
        appState.providerAccountProfile = ProviderAccountProfile(
            applicationType: applicationType,
            displayName: displayName,
            roleTitle: applicationType == .facility ? facilityType : (specialty.isEmpty ? cadre : specialty),
            registrationStatus: "Submitted",
            registrationReference: reference,
            workplace: applicationType == .professional ? workplaceName : facilityName,
            facilityType: facilityType,
            ownership: ownership,
            district: applicationType == .facility ? district : "Kampala",
            town: town,
            address: address,
            phone: applicationType == .facility ? normalizedPhone(facilityPhone) : normalizedPhone(phone),
            email: applicationType == .facility && !facilityEmail.isEmpty ? facilityEmail : email,
            licenceNumber: licenceNumber,
            licenceExpiry: licenceExpiry,
            specialty: specialty,
            open24Hours: open24Hours,
            emergencyAvailable: emergencyAvailable,
            services: services + (otherServices.isEmpty ? [] : [otherServices])
        )
        appState.userName = displayName
        appState.userPhone = normalizedPhone(phone)
        appState.healthID = reference
        appState.providerHours = open24Hours ? "Open 24 hours, every day" : "Set operating hours in Practice"
    }

    private func normalizedPhone(_ value: String) -> String {
        let digits = value.filter(\.isNumber)
        if digits.hasPrefix("256") { return "+\(digits)" }
        if digits.hasPrefix("0") { return "+256\(digits.dropFirst())" }
        return "+256\(digits)"
    }

    private static let dateFormatter: DateFormatter = {
        let formatter = DateFormatter()
        formatter.calendar = Calendar(identifier: .gregorian)
        formatter.locale = Locale(identifier: "en_US_POSIX")
        formatter.dateFormat = "yyyy-MM-dd"
        return formatter
    }()
}

private extension String {
    var nilIfEmpty: String? { isEmpty ? nil : self }
}
