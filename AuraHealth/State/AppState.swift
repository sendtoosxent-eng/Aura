import SwiftUI
import Observation

// =============================================================================
// APP STATE — the single "brain" the screens share.
//
// LEARN: @Observable (iOS 17) turns a class into something SwiftUI watches.
// When a property changes, every view that READ that property re-draws itself
// automatically. No manual refresh code. This is the heart of SwiftUI:
//     state changes  →  UI re-renders.
// =============================================================================

/// Which "big phase" of the app we're in. RootView switches on this.
enum AppStage: Equatable {
    case splash, onboarding, auth, patient, providerPortal
}

/// The five bottom tabs.
enum MainTab: Hashable {
    case home, map, emergency, messages, account
}

enum UserRole: String, CaseIterable, Identifiable {
    case patient = "I'm a User"
    case provider = "I'm a Health Provider"
    var id: String { rawValue }
}

/// What the Filter sheet edits.
struct ProviderFilters: Equatable {
    var types: Set<ProviderType> = []   // empty = "show every type"
    var careServices: Set<CareService> = []
    var openNow = false
    var open24h = false
    var minRating = 1.0
    var radiusKm = 50.0

    /// How many filters are switched on (shown as a badge on the filter button).
    var activeCount: Int {
        var n = types.count + careServices.count
        if openNow { n += 1 }
        if open24h { n += 1 }
        if minRating > 1 { n += 1 }
        if radiusKm < 50 { n += 1 }
        return n
    }

    // LEARN: `mutating` is required when a struct method changes the struct itself.
    mutating func toggle(_ type: ProviderType) {
        if types.contains(type) { types.remove(type) } else { types.insert(type) }
    }

    mutating func toggle(_ service: CareService) {
        if careServices.contains(service) {
            careServices.remove(service)
        } else {
            careServices.insert(service)
        }
    }
}

@Observable
final class AppState {

    @ObservationIgnored private let api = AfyaLinkAPI.shared
    @ObservationIgnored private var backendProviderIDs: [UUID: Int] = [:]
    @ObservationIgnored private var backendDoctorIDs: [UUID: Int] = [:]
    @ObservationIgnored private var pendingLoginChallengeID: Int?
    @ObservationIgnored private var pendingLoginProfile: AuthProfile?
    @ObservationIgnored private var hasLoadedDiscovery = false
    @ObservationIgnored private var demoPasswordsByPhone: [String: String] = [:]

    // MARK: Navigation-level state
    var stage: AppStage = .splash
    var selectedTab: MainTab = .home
    var showEmergency = false           // emergency screen slides over everything
    var currentRole: UserRole = .patient

    // MARK: Data
    var allProviders: [Provider] = MockData.providers
    var allDoctors: [CareDoctor] = MockData.doctors
    var filters = ProviderFilters()
    var favoriteIDs: Set<UUID>
    var messageThreads: [MessageThread] = []
    var notifications: [NotificationItem] = []
    var appointments: [CareAppointment] = [
        CareAppointment(providerName: "Mulago National Hospital", doctorName: "Dr. Sarah Namubiru",
                        service: "Child health & pediatrics", date: Date().addingTimeInterval(86_400),
                        time: "2:30 PM", reason: "Follow-up consultation", status: .confirmed)
    ]
    var providerUnavailableDates: [ProviderUnavailableDate] = []
    var medicationReminders: [MedicationReminder] = [
        MedicationReminder(medicine: "Amoxicillin", instructions: "1 tablet after food",
                           times: ["8:00 AM", "8:00 PM"], nextRefill: Date().addingTimeInterval(6 * 86_400),
                           isEnabled: true, takenToday: false)
    ]
    var medicalRecords: [MedicalRecord] = [
        MedicalRecord(title: "Malaria blood test", provider: "Lancet Laboratories Uganda",
                      date: Date().addingTimeInterval(-12 * 86_400), type: .laboratory,
                      detail: "Result: Negative. Reviewed by the attending clinician."),
        MedicalRecord(title: "Respiratory prescription", provider: "Mulago National Hospital",
                      date: Date().addingTimeInterval(-30 * 86_400), type: .prescription,
                      detail: "Salbutamol inhaler — use as directed by your clinician.")
    ]
    var selectedCareLocation = SavedCareLocation(name: "Current location", detail: "Kampala, Central Region",
                                                  latitude: AppConfig.kampala.latitude,
                                                  longitude: AppConfig.kampala.longitude)
    var emergencyHealthProfile = EmergencyHealthProfile()
    var trustedSessions: [TrustedSession] = [
        TrustedSession(device: "This iPhone", location: "Kampala, Uganda", lastActive: "Active now", isCurrent: true),
        TrustedSession(device: "Chrome on Mac", location: "Kampala, Uganda", lastActive: "2 days ago", isCurrent: false)
    ]
    var providerServices: Set<CareService> = [.malaria, .antenatal, .postnatal, .familyPlanning, .laboratory]
    var providerDoctors = ["Dr. Mugisha", "Dr. Sarah Namubiru"]
    var providerHours = "Monday–Saturday · 8:00 AM–8:00 PM"
    var providerAccountProfile = ProviderAccountProfile()

    // MARK: Signed-in user (fake for now)
    var userName = "Musa Balondemu"
    var userHandle = "musa_balondemu"
    var userPhone = "+256 700 000 000"
    var userEmail = ""
    var userNIN = ""
    var healthID = "UG-9421"
    var twoFactorEnabled = false
    var patientProfile: PatientAuthProfile?
    var profilePhotoData: Data?
    var coverPhotoData: Data?

    // MARK: Network
    let network = NetworkMonitor()

    init() {
        // Start with the 5 favourites shown in the design.
        favoriteIDs = Set(
            MockData.providers
                .filter { MockData.initialFavoriteNames.contains($0.name) }
                .map(\.id)
        )
        Task {
            for await _ in NotificationCenter.default.notifications(named: .afyaLinkRemoteNotificationReceived) {
                await loadMobileData()
            }
        }
    }

    // MARK: Provider queries

    /// Providers after applying the Filter sheet + the search box, nearest first.
    func filteredProviders(search: String = "") -> [Provider] {
        let q = search.trimmingCharacters(in: .whitespaces)
        return allProviders
            .filter { p in
                (filters.types.isEmpty || filters.types.contains(p.type))
                && (filters.careServices.isEmpty
                    || filters.careServices.contains { selected in
                        p.services.contains { $0.localizedCaseInsensitiveContains(selected.rawValue) }
                    })
                && (!filters.openNow || p.isOpenNow)
                && (!filters.open24h || p.is24Hours)
                && p.rating >= filters.minRating
                && p.distanceKm <= filters.radiusKm
                && (q.isEmpty
                    || p.name.localizedCaseInsensitiveContains(q)
                    || p.address.localizedCaseInsensitiveContains(q)
                    || p.services.contains { $0.localizedCaseInsensitiveContains(q) })
            }
            .sorted { $0.distanceKm < $1.distanceKm }
    }

    func doctors(at provider: Provider) -> [CareDoctor] {
        allDoctors.filter { $0.provider.id == provider.id }
    }

    /// Open hospitals/clinics nearest first — used by Emergency Mode.
    var emergencyProviders: [Provider] {
        allProviders
            .filter { $0.isOpenNow && ($0.type == .hospital || $0.type == .clinic) }
            .sorted { $0.distanceKm < $1.distanceKm }
    }

    // MARK: Favourites

    var favorites: [Provider] { allProviders.filter { favoriteIDs.contains($0.id) } }

    func isFavorite(_ p: Provider) -> Bool { favoriteIDs.contains(p.id) }

    func toggleFavorite(_ p: Provider) {
        if favoriteIDs.contains(p.id) { favoriteIDs.remove(p.id) } else { favoriteIDs.insert(p.id) }
    }

    // MARK: Ongoing care

    func addAppointment(_ appointment: CareAppointment) {
        appointments.insert(appointment, at: 0)
    }

    func requestAppointment(
        provider: Provider,
        doctor: CareDoctor?,
        service: String,
        date: Date,
        time: String,
        reason: String
    ) async throws {
        guard let providerID = backendProviderIDs[provider.id] else {
            throw APIError.server(status: 422, message: "This demo facility is not linked to the backend yet.")
        }

        let request = CreateAppointmentRequest(
            healthcareProviderId: providerID,
            doctorId: doctor.flatMap { backendDoctorIDs[$0.id] },
            appointmentDate: Self.apiDateFormatter.string(from: date),
            appointmentTime: Self.twentyFourHourTime(time),
            service: service,
            reason: reason.trimmingCharacters(in: .whitespacesAndNewlines).nilIfEmpty
        )
        let appointment = try await api.createAppointment(request)
        addAppointment(appointment.appointment)
    }

    func cancelAppointment(_ appointment: CareAppointment) {
        guard let index = appointments.firstIndex(where: { $0.id == appointment.id }) else { return }
        appointments[index].status = .cancelled
        guard let backendID = appointment.backendID else { return }
        Task { try? await api.cancelAppointment(backendID) }
    }

    func updateAppointment(_ appointment: CareAppointment, status: AppointmentStatus) async throws {
        guard let backendID = appointment.backendID else {
            throw APIError.server(status: 422, message: "This appointment is not linked to the backend.")
        }
        let updated = try await api.updateAppointment(backendID, status: status.apiValue)
        guard let index = appointments.firstIndex(where: { $0.id == appointment.id }) else { return }
        appointments[index].status = updated.appointment.status
    }

    func unavailableDates(for provider: Provider, month: Date) async throws -> [ProviderUnavailableDate] {
        guard let providerID = backendProviderIDs[provider.id] else {
            throw APIError.server(status: 422, message: "This facility is not linked to the backend yet.")
        }
        let values = try await api.providerAvailability(
            providerID: providerID,
            month: Self.apiMonthFormatter.string(from: month)
        )
        return values.map(\.unavailableDate)
    }

    func loadManagedAvailability() async {
        guard let values = try? await api.managedProviderAvailability() else { return }
        providerUnavailableDates = values.map(\.unavailableDate)
    }

    func closeProviderDate(_ date: Date, reason: String) async throws {
        let value = try await api.closeProviderDate(
            date: Self.apiDateFormatter.string(from: date),
            reason: reason.trimmingCharacters(in: .whitespacesAndNewlines).nilIfEmpty
        )
        providerUnavailableDates.removeAll { $0.id == value.id || $0.date == value.date }
        providerUnavailableDates.append(value.unavailableDate)
    }

    func reopenProviderDate(_ unavailableDate: ProviderUnavailableDate) async throws {
        try await api.reopenProviderDate(unavailableDate.id)
        providerUnavailableDates.removeAll { $0.id == unavailableDate.id }
    }

    func toggleReminder(_ reminder: MedicationReminder) {
        guard let index = medicationReminders.firstIndex(where: { $0.id == reminder.id }) else { return }
        medicationReminders[index].isEnabled.toggle()
    }

    func markMedicineTaken(_ reminder: MedicationReminder) {
        guard let index = medicationReminders.firstIndex(where: { $0.id == reminder.id }) else { return }
        medicationReminders[index].takenToday.toggle()
    }

    func removeTrustedSession(_ session: TrustedSession) {
        guard !session.isCurrent else { return }
        trustedSessions.removeAll { $0.id == session.id }
    }

    // MARK: Messages

    func thread(named participantName: String) -> MessageThread? {
        messageThreads.first { $0.participantName == participantName }
    }

    func markThreadRead(_ participantName: String) {
        guard let index = messageThreads.firstIndex(where: { $0.participantName == participantName }) else { return }
        messageThreads[index].unreadCount = 0
        guard let backendID = messageThreads[index].backendID else { return }
        Task { try? await api.markThreadRead(backendID) }
    }

    func sendMessage(_ text: String, to participantName: String) {
        let cleanText = text.trimmingCharacters(in: .whitespacesAndNewlines)
        guard !cleanText.isEmpty else { return }

        Task {
            do {
                if let index = messageThreads.firstIndex(where: { $0.participantName == participantName }),
                   let threadID = messageThreads[index].backendID {
                    let dto = try await api.sendMessage(threadID: threadID, text: cleanText)
                    await MainActor.run { messageThreads[index].messages.append(dto.message) }
                    return
                }

                let providerID = allProviders.first(where: { $0.name == participantName })
                    .flatMap { backendProviderIDs[$0.id] }
                let doctorID = allDoctors.first(where: { $0.name == participantName })
                    .flatMap { backendDoctorIDs[$0.id] }
                let dto = try await api.startConversation(
                    providerID: providerID,
                    doctorID: doctorID,
                    text: cleanText
                )
                await MainActor.run { messageThreads.insert(dto.thread, at: 0) }
            } catch {
                // The conversation remains unchanged when the server rejects a send.
            }
        }
    }

    func markNotificationRead(_ notification: NotificationItem) {
        guard let index = notifications.firstIndex(where: { $0.id == notification.id }) else { return }
        notifications[index].isUnread = false
        guard let backendID = notifications[index].backendID else { return }
        Task { try? await api.markNotificationRead(backendID) }
    }

    func markAllNotificationsRead() {
        for index in notifications.indices { notifications[index].isUnread = false }
        Task { try? await api.markAllNotificationsRead() }
    }

    // MARK: Authentication

    func lookupProfile(phoneDigits: String, role: UserRole) async throws -> AuthProfile {
        guard Validators.isValidUgandaPhone(phoneDigits) else {
            throw APIError.server(status: 422, message: "Enter a valid Ugandan mobile number.")
        }
        return try await api.lookup(phone: "+256\(phoneDigits)", role: role)
    }

    func signIn(password: String, profile: AuthProfile) async throws -> Bool {
        let result = try await api.login(phone: profile.phone, password: password, role: profile.role)
        switch result {
        case let .authenticated(authenticatedProfile):
            prepareLogin(for: authenticatedProfile)
            completeLogin(as: authenticatedProfile.role)
            return false
        case let .securityCodeRequired(challengeID, debugCode):
            pendingLoginChallengeID = challengeID
            pendingLoginProfile = profile
#if DEBUG
            loginDebugCode = debugCode
#endif
            return true
        }
    }

    func verifyPendingLogin(code: String) async throws {
        guard let challengeID = pendingLoginChallengeID, let profile = pendingLoginProfile else {
            throw APIError.server(status: 410, message: "This security-code request has expired. Please sign in again.")
        }
        let authenticated = try await api.verifyLogin(challengeID: challengeID, code: code, role: profile.role)
        pendingLoginChallengeID = nil
        pendingLoginProfile = nil
        loginDebugCode = nil
        prepareLogin(for: authenticated)
        completeLogin(as: authenticated.role)
    }

    func resendPendingLoginCode() async throws {
        guard let challengeID = pendingLoginChallengeID else {
            throw APIError.server(status: 410, message: "This security-code request has expired. Please sign in again.")
        }
        let response = try await api.resendLoginCode(challengeID: challengeID)
        pendingLoginChallengeID = response.challengeId
#if DEBUG
        loginDebugCode = response.debugCode
#endif
    }

    func registerPatient(_ request: PatientRegistrationRequest) async throws {
        let profile = try await api.registerPatient(request)
        prepareLogin(for: profile)
    }

    func updatePatientProfile(_ request: PatientProfileUpdateRequest) async throws {
        let profile = try await api.updatePatientProfile(request)
        prepareLogin(for: profile)
        if let photo = request.profilePhoto {
            profilePhotoData = photo
        }
        if let photo = request.coverPhoto {
            coverPhotoData = photo
        }
    }

    func sendPatientRegistrationCode() async throws {
        let response = try await api.sendPatientRegistrationCode()
#if DEBUG
        registrationDebugCode = response.debugCode
#endif
    }

    func verifyPatientRegistration(code: String) async throws {
        let profile = try await api.verifyPatientRegistration(code: code)
        registrationDebugCode = nil
        prepareLogin(for: profile)
        completeLogin(as: .patient)
    }

    var loginDebugCode: String?
    var registrationDebugCode: String?

    func updatePassword(_ password: String, for phone: String) {
        demoPasswordsByPhone[phone] = password
    }

    func updateTwoFactor(enabled: Bool) async throws {
        let profile = try await api.updateTwoFactor(enabled: enabled)
        twoFactorEnabled = profile.twoFactorEnabled
    }

    func prepareLogin(for profile: AuthProfile) {
        currentRole = profile.role
        userName = profile.name
        userHandle = profile.handle
        userPhone = profile.phone
        userEmail = profile.email ?? ""
        userNIN = profile.nin ?? ""
        healthID = profile.profileID
        twoFactorEnabled = profile.twoFactorEnabled
        patientProfile = profile.patientProfile

        if let patient = profile.patientProfile {
            emergencyHealthProfile = EmergencyHealthProfile(
                bloodGroup: patient.bloodGroup ?? "Not provided",
                bloodPressure: patient.systolic.flatMap { systolic in
                    patient.diastolic.map { "\(systolic)/\($0) mmHg" }
                } ?? "Not provided",
                heartRate: patient.heartRate.map { "\($0) bpm" } ?? "Not provided",
                height: patient.heightCm.map { String(format: "%.0f cm", $0) } ?? "Not provided",
                weight: patient.weightKg.map { String(format: "%.1f kg", $0) } ?? "Not provided",
                allergies: patient.allergies?.nilIfEmpty ?? "None provided",
                conditions: patient.chronicConditions?.nilIfEmpty ?? "None provided",
                medications: patient.currentMedications?.nilIfEmpty ?? "None provided",
                nextOfKin: patient.emergencyContactName ?? "Not provided",
                nextOfKinRelationship: patient.emergencyContactRelationship ?? "",
                nextOfKinPhone: patient.emergencyContactPhone ?? "Not provided"
            )

            Task { await loadPatientPhotos(for: patient) }
        }

        if let provider = profile.providerProfile {
            let applicationType: ProviderApplicationType = provider.applicationType == "facility"
                ? .facility : .professional
            providerAccountProfile = ProviderAccountProfile(
                applicationType: applicationType,
                displayName: provider.displayName ?? profile.name,
                roleTitle: provider.roleTitle ?? (applicationType == .facility ? "Healthcare Facility" : "Health Professional"),
                registrationStatus: provider.registrationStatus.replacingOccurrences(of: "_", with: " ").capitalized,
                registrationReference: provider.registrationReference ?? profile.profileID,
                workplace: provider.workplace ?? "",
                facilityType: provider.facilityType ?? "",
                ownership: provider.ownership ?? "",
                district: provider.district ?? "",
                town: provider.town ?? "",
                address: provider.address ?? "",
                phone: provider.phone ?? profile.phone,
                email: provider.email ?? profile.email ?? "",
                licenceNumber: provider.licenceNumber ?? "",
                licenceExpiry: provider.licenceExpiry.flatMap(Self.apiDateFormatter.date(from:)) ?? Date(),
                specialty: provider.specialty ?? "",
                open24Hours: provider.open24Hours,
                emergencyAvailable: provider.emergencyAvailable,
                services: provider.services
            )
            providerHours = provider.open24Hours ? "Open 24 hours, every day" : "Set operating hours in Practice"
            providerServices = Set(provider.services.compactMap(CareService.init(rawValue:)))
        }
    }

    @MainActor
    private func loadPatientPhotos(for profile: PatientAuthProfile) async {
        if profile.hasProfilePhoto, let data = try? await api.patientPhoto("profile") {
            profilePhotoData = data
        }
        if profile.hasCoverPhoto, let data = try? await api.patientPhoto("cover") {
            coverPhotoData = data
        }
    }

    func completeLogin(as role: UserRole) {
        withAnimation(.easeInOut) {
            currentRole = role
            selectedTab = .home
            stage = (role == .provider) ? .providerPortal : .patient
        }
        Task {
            await syncPushDevice()
            await loadMobileData()
        }
    }

    func restoreSession() async -> Bool {
        do {
            let profile = try await api.currentProfile()
            prepareLogin(for: profile)
            currentRole = profile.role
            selectedTab = .home
            stage = profile.role == .provider ? .providerPortal : .patient
            await loadMobileData()
            return true
        } catch let APIError.server(status, _) where status == 401 {
            api.discardSession()
            return false
        } catch {
            return false
        }
    }

    func logout() {
        Task {
            if let token = await PushNotificationManager.shared.deviceToken {
                try? await api.unregisterPushDevice(token: token)
            }
            await api.logout()
        }
        messageThreads = []
        notifications = []
        withAnimation(.easeInOut) {
            selectedTab = .home
            stage = .auth
        }
    }

    private func syncPushDevice() async {
        await PushNotificationManager.shared.registerWithAppleIfAuthorized()
        guard let token = await PushNotificationManager.shared.deviceToken else { return }
        try? await api.registerPushDevice(
            token: token,
            environment: await PushNotificationManager.shared.environment
        )
    }

    // MARK: Backend discovery

    func loadMobileData() async {
        do {
            let data = try await api.mobileData()
            messageThreads = data.messageThreads.map(\.thread)
            notifications = data.notifications.map(\.notification)
        } catch {
            // Authenticated screens show their empty states when data cannot load.
        }
        await loadAppointments()
        if currentRole == .provider {
            await loadManagedAvailability()
        }
    }

    private func loadAppointments() async {
        guard let values = try? await api.appointments() else { return }
        appointments = values.map(\.appointment)
    }

    func loadDiscoveryIfNeeded() async {
        guard !hasLoadedDiscovery else { return }
        hasLoadedDiscovery = true
        do {
            async let providerRequest = api.providers()
            async let doctorRequest = api.doctors()
            let (providerDTOs, doctorDTOs) = try await (providerRequest, doctorRequest)

            var providerIDs: [UUID: Int] = [:]
            let providers = providerDTOs.map { dto -> Provider in
                let provider = dto.provider
                providerIDs[provider.id] = dto.id
                return provider
            }
            let providersByBackendID = Dictionary(uniqueKeysWithValues: zip(providerDTOs.map(\.id), providers))

            var doctorIDs: [UUID: Int] = [:]
            let doctors = doctorDTOs.compactMap { dto -> CareDoctor? in
                guard let facilityID = dto.facility?.id,
                      let provider = providersByBackendID[facilityID] else { return nil }
                let doctor = dto.doctor(at: provider)
                doctorIDs[doctor.id] = dto.id
                return doctor
            }

            if !providers.isEmpty {
                allProviders = providers
                backendProviderIDs = providerIDs
                favoriteIDs = favoriteIDs.intersection(Set(providers.map(\.id)))
            }
            allDoctors = doctors
            backendDoctorIDs = doctorIDs
        } catch {
            // Keep the bundled discovery data available while the local server
            // is offline. Auth and booking still surface their network errors.
            hasLoadedDiscovery = false
        }
    }

    fileprivate static let apiDateFormatter: DateFormatter = {
        let formatter = DateFormatter()
        formatter.calendar = Calendar(identifier: .gregorian)
        formatter.locale = Locale(identifier: "en_US_POSIX")
        formatter.dateFormat = "yyyy-MM-dd"
        return formatter
    }()

    private static let apiMonthFormatter: DateFormatter = {
        let formatter = DateFormatter()
        formatter.calendar = Calendar(identifier: .gregorian)
        formatter.locale = Locale(identifier: "en_US_POSIX")
        formatter.dateFormat = "yyyy-MM"
        return formatter
    }()

    private static func twentyFourHourTime(_ displayTime: String) -> String {
        let input = DateFormatter()
        input.locale = Locale(identifier: "en_US_POSIX")
        input.dateFormat = "h:mm a"
        let output = DateFormatter()
        output.locale = Locale(identifier: "en_US_POSIX")
        output.dateFormat = "HH:mm"
        return input.date(from: displayTime).map(output.string(from:)) ?? displayTime
    }
    // MARK: Chat attachments (photo / document / voice), looked up by message id
    var attachments: [AnyHashable: ChatAttachment] = [:]

    /// Sends a normal message (the caption shows in the conversation list preview)
    /// and attaches the file to that message.
    func sendAttachment(_ attachment: ChatAttachment, caption: String, to name: String) {
        sendMessage(caption, to: name)
        if let id = thread(named: name)?.messages.last?.id {
            attachments[id] = attachment
        }
    }
}

private extension NotificationDTO {
    var notification: NotificationItem {
        let date = MobileDateFormatting.date(from: createdAt)
        let category: NotificationFilter = switch self.category {
        case "alerts": .alerts
        case "updates": .updates
        default: .system
        }
        return NotificationItem(
            backendID: id,
            title: title,
            message: message,
            timeText: MobileDateFormatting.relativeText(for: date),
            category: category,
            icon: icon,
            isUnread: !read,
            section: MobileDateFormatting.section(for: date)
        )
    }
}

private extension MessageDTO {
    var message: ChatMessage {
        let messageAuthor: MessageAuthor = switch author {
        case "provider": .provider
        case "automated": .automated
        default: .user
        }
        return ChatMessage(
            backendID: id,
            author: messageAuthor,
            text: text,
            timeText: MobileDateFormatting.messageTime(for: MobileDateFormatting.date(from: sentAt))
        )
    }
}

private extension MessageThreadDTO {
    var thread: MessageThread {
        MessageThread(
            backendID: id,
            participantName: participantName,
            subtitle: subtitle,
            symbol: isAutomated ? "heart.text.square.fill" : "cross.case.fill",
            isOnline: isOnline,
            isAutomated: isAutomated,
            isVerified: verified,
            lastActiveAt: lastActiveAt.map { MobileDateFormatting.date(from: $0) },
            unreadCount: unreadCount,
            messages: messages.map(\.message)
        )
    }
}

private extension AppointmentDTO {
    var appointment: CareAppointment {
        CareAppointment(
            backendID: id,
            providerName: facility?.name ?? doctor?.name ?? "Healthcare provider",
            doctorName: doctor?.name ?? "Any available clinician",
            patientName: patient?.name,
            service: service ?? "General consultation",
            date: AppState.apiDateFormatter.date(from: appointmentDate) ?? Date(),
            time: Self.displayTime(appointmentTime),
            reason: reason ?? "",
            status: AppointmentStatus(apiValue: status)
        )
    }

    private static func displayTime(_ value: String) -> String {
        let input = DateFormatter()
        input.locale = Locale(identifier: "en_US_POSIX")
        input.dateFormat = "HH:mm"
        let output = DateFormatter()
        output.locale = Locale(identifier: "en_US_POSIX")
        output.dateFormat = "h:mm a"
        return input.date(from: value).map(output.string(from:)) ?? value
    }
}

private extension ProviderUnavailableDateDTO {
    var unavailableDate: ProviderUnavailableDate {
        ProviderUnavailableDate(id: id, date: date, reason: reason)
    }
}

private extension AppointmentStatus {
    init(apiValue: String) {
        self = switch apiValue {
        case "confirmed": .confirmed
        case "completed": .completed
        case "cancelled": .cancelled
        default: .requested
        }
    }

    var apiValue: String {
        switch self {
        case .requested: "pending"
        case .confirmed: "confirmed"
        case .completed: "completed"
        case .cancelled: "cancelled"
        }
    }
}

private enum MobileDateFormatting {
    private static let fractionalISOFormatter: ISO8601DateFormatter = {
        let formatter = ISO8601DateFormatter()
        formatter.formatOptions = [.withInternetDateTime, .withFractionalSeconds]
        return formatter
    }()

    private static let standardISOFormatter = ISO8601DateFormatter()

    static func date(from value: String?) -> Date {
        guard let value else { return Date() }
        return fractionalISOFormatter.date(from: value)
            ?? standardISOFormatter.date(from: value)
            ?? Date()
    }

    static func relativeText(for date: Date) -> String {
        let formatter = RelativeDateTimeFormatter()
        formatter.unitsStyle = .abbreviated
        return formatter.localizedString(for: date, relativeTo: Date())
    }

    static func section(for date: Date) -> String {
        if Calendar.current.isDateInToday(date) { return "Today" }
        if Calendar.current.isDateInYesterday(date) { return "Yesterday" }
        return "Earlier"
    }

    static func messageTime(for date: Date) -> String {
        if Calendar.current.isDateInToday(date) {
            return date.formatted(date: .omitted, time: .shortened)
        }
        return relativeText(for: date)
    }
}

private extension String {
    var nilIfEmpty: String? {
        isEmpty ? nil : self
    }
}

private extension ProviderDTO {
    var provider: Provider {
        let normalizedType = providerType.lowercased()
        let type: ProviderType
        switch normalizedType {
        case "hospital": type = .hospital
        case "pharmacy": type = .pharmacy
        case "laboratory", "lab": type = .lab
        case "ambulance": type = .ambulance
        default: type = .clinic
        }
        let lat = latitude ?? AppConfig.kampala.latitude
        let lon = longitude ?? AppConfig.kampala.longitude
        let latitudeKm = (lat - AppConfig.kampala.latitude) * 111
        let longitudeKm = (lon - AppConfig.kampala.longitude) * 111
            * cos(AppConfig.kampala.latitude * .pi / 180)
        return Provider(
            name: name,
            address: address ?? [town, district].compactMap { $0 }.joined(separator: ", "),
            type: type,
            rating: rating,
            reviewCount: reviewCount,
            distanceKm: sqrt(latitudeKm * latitudeKm + longitudeKm * longitudeKm),
            isOpenNow: true,
            is24Hours: open24Hours,
            closesAt: open24Hours ? nil : "6PM",
            phone: phone ?? "",
            latitude: lat,
            longitude: lon,
            services: services,
            isVerified: verified,
            imageURL: AppConfig.serverAssetURL(image)
        )
    }
}

private extension DoctorDTO {
    func doctor(at provider: Provider) -> CareDoctor {
        CareDoctor(
            name: name,
            specialty: specialty ?? "General Medicine",
            availability: "Contact facility for availability",
            rating: rating,
            imageName: "doctor_1",
            isAvailableNow: true,
            provider: provider,
            latitude: latitude ?? provider.latitude,
            longitude: longitude ?? provider.longitude
        )
    }
}
