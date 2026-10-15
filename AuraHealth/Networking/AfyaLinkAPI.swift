import Foundation

struct AuthProfileDTO: Decodable {
    let id: Int
    let name: String
    let personName: String?
    let initials: String?
    let phone: String
    let email: String?
    let nin: String?
    let accountType: String
    let profileId: String
    let registrationStatus: String?
    let requiresSecurityOtp: Bool?
    let twoFactorEnabled: Bool?
    let patientProfile: PatientProfileDTO?
    let providerProfile: ProviderProfileDTO?
}

struct PatientProfileDTO: Decodable {
    let dateOfBirth: String?
    let gender: String?
    let hasProfilePhoto: Bool?
    let hasCoverPhoto: Bool?
    let bloodGroup: String?
    let systolic: Int?
    let diastolic: Int?
    let heartRate: Int?
    let heightCm: Double?
    let weightKg: Double?
    let allergies: String?
    let chronicConditions: String?
    let currentMedications: String?
    let emergencyContactName: String?
    let emergencyContactRelationship: String?
    let emergencyContactPhone: String?
    let healthDataConsent: Bool?
}

struct ProviderProfileDTO: Decodable {
    let applicationType: String
    let displayName: String?
    let roleTitle: String?
    let registrationStatus: String
    let registrationReference: String?
    let workplace: String?
    let facilityType: String?
    let ownership: String?
    let district: String?
    let town: String?
    let address: String?
    let phone: String?
    let email: String?
    let licenceNumber: String?
    let licenceExpiry: String?
    let specialty: String?
    let open24Hours: Bool
    let emergencyAvailable: Bool
    let image: String?
    let services: [String]
}

struct PatientRegistrationRequest {
    let name: String
    let phone: String
    let email: String
    let nin: String
    let password: String
    let profilePhoto: Data
    let coverPhoto: Data
    let bloodGroup: String?
    let systolic: String
    let diastolic: String
    let heartRate: String
    let heightCm: String
    let weightKg: String
    let allergies: String
    let chronicConditions: String
    let emergencyContactName: String
    let emergencyContactRelationship: String
    let emergencyContactPhone: String
}

struct PatientProfileUpdateRequest {
    let name: String
    let phone: String
    let email: String
    let nin: String
    let dateOfBirth: String
    let gender: String
    let profilePhoto: Data?
    let coverPhoto: Data?
    let bloodGroup: String
    let systolic: String
    let diastolic: String
    let heartRate: String
    let heightCm: String
    let weightKg: String
    let allergies: String
    let chronicConditions: String
    let currentMedications: String
    let emergencyContactName: String
    let emergencyContactRelationship: String
    let emergencyContactPhone: String
    let healthDataConsent: Bool
}

struct PatientRegistrationResponse: Decodable {
    let message: String
    let accessToken: String
    let data: AuthProfileDTO
}

struct LoginRequest: Encodable {
    let phone: String
    let password: String
    let role: String
    let deviceId: String
    let deviceName: String
}

struct LoginResponse: Decodable {
    let message: String?
    let requiresSecurityOtp: Bool
    let challengeId: Int?
    let expiresAt: String?
    let debugCode: String?
    let tokenType: String?
    let accessToken: String?
    let data: AuthProfileDTO?
}

struct LoginVerificationRequest: Encodable {
    let challengeId: Int
    let otp: String
    let deviceId: String
    let deviceName: String
}

struct ResendLoginRequest: Encodable {
    let challengeId: Int
    let deviceId: String
}

struct ResendLoginResponse: Decodable {
    let message: String
    let challengeId: Int
    let expiresAt: String
    let debugCode: String?
}

struct LookupRequest: Encodable {
    let phone: String
    let role: String
    let deviceId: String
}

private struct TwoFactorRequest: Encodable { let enabled: Bool }

struct ProviderDTO: Decodable {
    let id: Int
    let name: String
    let providerType: String
    let district: String?
    let town: String?
    let address: String?
    let phone: String?
    let latitude: Double?
    let longitude: Double?
    let rating: Double
    let reviewCount: Int
    let verified: Bool
    let open24Hours: Bool
    let emergencyAvailable: Bool
    let image: String?
    let services: [String]
}

struct DoctorFacilityDTO: Decodable {
    let id: Int
    let name: String
    let address: String?
    let phone: String?
}

struct DoctorDTO: Decodable {
    let id: Int
    let name: String
    let specialty: String?
    let rating: Double
    let verified: Bool
    let image: String?
    let latitude: Double?
    let longitude: Double?
    let facility: DoctorFacilityDTO?
}

struct CreateAppointmentRequest: Encodable {
    let healthcareProviderId: Int?
    let doctorId: Int?
    let appointmentDate: String
    let appointmentTime: String
    let appointmentType = "in_person"
    let service: String
    let reason: String?
}

struct AppointmentDTO: Decodable {
    struct Party: Decodable {
        let id: Int
        let name: String
        let specialty: String?
    }

    let id: Int
    let appointmentDate: String
    let appointmentTime: String
    let service: String?
    let reason: String?
    let status: String
    let facility: Party?
    let doctor: Party?
    let patient: Party?
}

struct AppointmentResponse: Decodable {
    let message: String
    let data: AppointmentDTO
}

struct ProviderUnavailableDateDTO: Decodable {
    let id: Int
    let date: String
    let reason: String?
}

struct ProviderAvailabilityDTO: Decodable {
    let providerId: Int
    let month: String
    let unavailableDates: [ProviderUnavailableDateDTO]
}

private struct AppointmentStatusRequest: Encodable { let status: String }
private struct UnavailableDateRequest: Encodable { let date: String; let reason: String? }
private struct PushDeviceRequest: Encodable {
    let token: String
    let environment: String
    let appBundleId: String
}
private struct PushDeviceResponse: Decodable { let message: String }

struct MobileDataDTO: Decodable {
    let notifications: [NotificationDTO]
    let messageThreads: [MessageThreadDTO]
}

struct NotificationDTO: Decodable {
    let id: Int
    let title: String
    let message: String
    let category: String
    let icon: String
    let read: Bool
    let createdAt: String?
}

struct MessageDTO: Decodable {
    let id: Int
    let author: String
    let text: String
    let sentAt: String
}

struct MessageThreadDTO: Decodable {
    let id: Int
    let participantName: String
    let subtitle: String
    let isOnline: Bool
    let isAutomated: Bool
    let verified: Bool
    let lastActiveAt: String?
    let unreadCount: Int
    let messages: [MessageDTO]
}

private struct MessageTextRequest: Encodable { let text: String }

private struct StartConversationRequest: Encodable {
    let healthcareProviderId: Int?
    let doctorId: Int?
    let text: String
}

struct RegistrationFacilityDTO: Decodable, Identifiable, Hashable {
    let id: Int
    let name: String
    let providerType: String
    let district: String?
    let town: String?
    let address: String?
}

struct ProviderRegistrationStatusDTO: Decodable {
    let id: Int
    let applicationType: String
    let status: String
    let reference: String?
    let healthcareProviderId: Int?
    let personName: String
    let facilityName: String?
    let emailVerified: Bool?
    let phoneVerified: Bool?
}

struct ProviderIdentityRequest {
    let applicationType: String
    let healthcareProviderId: Int?
    let firstName: String
    let middleName: String?
    let lastName: String
    let dateOfBirth: String
    let sex: String
    let nationality: String
    let profilePhoto: URL
    let phone: String
    let email: String
    let nin: String
    let password: String
    let passwordConfirmation: String
}

struct ProviderApplicationRequest {
    let applicationType: String
    let healthcareProviderId: Int?
    let regulatoryFields: [String: String]
    let services: [String]
    let documents: [String: URL]
}

struct ProviderIdentityResponse: Decodable {
    let message: String
    let accessToken: String
    let applicationId: Int
    let nextStep: String
    let emailSent: Bool
    let data: ProviderRegistrationStatusDTO
}

struct ProviderSubmissionResponse: Decodable {
    let message: String
    let reference: String
}

struct VerificationResponse: Decodable {
    let message: String
    let verified: Bool?
    let data: AuthProfileDTO?
    let debugCode: String?
}

enum LoginOutcome {
    case authenticated(AuthProfile)
    case securityCodeRequired(challengeID: Int, debugCode: String?)
}

final class AfyaLinkAPI: @unchecked Sendable {
    static let shared = AfyaLinkAPI()
    private let client: APIClient

    init(client: APIClient = .shared) {
        self.client = client
    }

    func lookup(phone: String, role: UserRole) async throws -> AuthProfile {
        let response: DataEnvelope<AuthProfileDTO> = try await client.send(
            "auth/lookup",
            method: "POST",
            body: LookupRequest(phone: phone, role: role.apiValue, deviceId: client.sessionStore.deviceID)
        )
        return response.data.profile(role: role)
    }

    func login(phone: String, password: String, role: UserRole) async throws -> LoginOutcome {
        let response: LoginResponse = try await client.send(
            "auth/login",
            method: "POST",
            body: LoginRequest(
                phone: phone,
                password: password,
                role: role.apiValue,
                deviceId: client.sessionStore.deviceID,
                deviceName: client.sessionStore.deviceName
            )
        )
        if response.requiresSecurityOtp {
            guard let challengeID = response.challengeId else { throw APIError.invalidResponse }
            return .securityCodeRequired(challengeID: challengeID, debugCode: response.debugCode)
        }
        guard let token = response.accessToken, let dto = response.data else { throw APIError.invalidResponse }
        client.sessionStore.accessToken = token
        return .authenticated(dto.profile(role: role))
    }

    func verifyLogin(challengeID: Int, code: String, role: UserRole) async throws -> AuthProfile {
        let response: LoginResponse = try await client.send(
            "auth/login/verify",
            method: "POST",
            body: LoginVerificationRequest(
                challengeId: challengeID,
                otp: code,
                deviceId: client.sessionStore.deviceID,
                deviceName: client.sessionStore.deviceName
            )
        )
        guard let token = response.accessToken, let dto = response.data else { throw APIError.invalidResponse }
        client.sessionStore.accessToken = token
        return dto.profile(role: role)
    }

    func resendLoginCode(challengeID: Int) async throws -> ResendLoginResponse {
        try await client.send(
            "auth/login/resend",
            method: "POST",
            body: ResendLoginRequest(
                challengeId: challengeID,
                deviceId: client.sessionStore.deviceID
            )
        )
    }

    func currentProfile() async throws -> AuthProfile {
        let response: DataEnvelope<AuthProfileDTO> = try await client.send(
            "auth/me",
            authenticated: true
        )
        return response.data.profile(role: response.data.inferredRole)
    }

    func updateTwoFactor(enabled: Bool) async throws -> AuthProfile {
        let response: DataEnvelope<AuthProfileDTO> = try await client.send(
            "auth/two-factor",
            method: "PATCH",
            body: TwoFactorRequest(enabled: enabled),
            authenticated: true
        )
        return response.data.profile(role: response.data.inferredRole)
    }

    func updatePatientProfile(_ request: PatientProfileUpdateRequest) async throws -> AuthProfile {
        var form = MultipartFormData()
        form.addField("name", value: request.name)
        form.addField("phone", value: request.phone)
        form.addField("email", value: request.email)
        form.addField("nin", value: request.nin)
        form.addField("date_of_birth", value: request.dateOfBirth)
        form.addField("gender", value: request.gender)
        form.addField("blood_group", value: request.bloodGroup)
        form.addField("systolic", value: request.systolic)
        form.addField("diastolic", value: request.diastolic)
        form.addField("heart_rate", value: request.heartRate)
        form.addField("height_cm", value: request.heightCm)
        form.addField("weight_kg", value: request.weightKg)
        form.addField("allergies", value: request.allergies)
        form.addField("chronic_conditions", value: request.chronicConditions)
        form.addField("current_medications", value: request.currentMedications)
        form.addField("emergency_contact_name", value: request.emergencyContactName)
        form.addField("emergency_contact_relationship", value: request.emergencyContactRelationship)
        form.addField("emergency_contact_phone", value: request.emergencyContactPhone)
        form.addField("health_data_consent", value: request.healthDataConsent ? "1" : "0")
        if let data = request.profilePhoto {
            form.addFile("profile_photo", fileName: "profile.jpg", mimeType: "image/jpeg", data: data)
        }
        if let data = request.coverPhoto {
            form.addFile("cover_photo", fileName: "cover.jpg", mimeType: "image/jpeg", data: data)
        }

        let response: DataEnvelope<AuthProfileDTO> = try await client.sendMultipart(
            "profile", form: form, authenticated: true
        )
        return response.data.profile(role: .patient)
    }

    func patientPhoto(_ type: String) async throws -> Data {
        try await client.data("profile/photos/\(type)", authenticated: true)
    }

    func registerPatient(_ request: PatientRegistrationRequest) async throws -> AuthProfile {
        var form = MultipartFormData()
        form.addField("name", value: request.name)
        form.addField("phone", value: request.phone)
        form.addField("email", value: request.email)
        form.addField("nin", value: request.nin)
        form.addField("password", value: request.password)
        form.addField("password_confirmation", value: request.password)
        form.addField("blood_group", value: request.bloodGroup)
        form.addField("systolic", value: request.systolic.nilIfEmpty)
        form.addField("diastolic", value: request.diastolic.nilIfEmpty)
        form.addField("heart_rate", value: request.heartRate.nilIfEmpty)
        form.addField("height_cm", value: request.heightCm.nilIfEmpty)
        form.addField("weight_kg", value: request.weightKg.nilIfEmpty)
        form.addField("allergies", value: request.allergies.nilIfEmpty)
        form.addField("chronic_conditions", value: request.chronicConditions.nilIfEmpty)
        form.addField("emergency_contact_name", value: request.emergencyContactName)
        form.addField("emergency_contact_relationship", value: request.emergencyContactRelationship)
        form.addField("emergency_contact_phone", value: request.emergencyContactPhone)
        form.addField("health_data_consent", value: "1")
        form.addField("device_name", value: client.sessionStore.deviceName)
        form.addFile("profile_photo", fileName: "profile.jpg", mimeType: "image/jpeg", data: request.profilePhoto)
        form.addFile("cover_photo", fileName: "cover.jpg", mimeType: "image/jpeg", data: request.coverPhoto)

        let response: PatientRegistrationResponse = try await client.sendMultipart(
            "patient-registration",
            form: form
        )
        client.sessionStore.accessToken = response.accessToken
        return response.data.profile(role: .patient)
    }

    func sendPatientRegistrationCode() async throws -> VerificationResponse {
        try await client.send(
            "patient-registration/phone/send",
            method: "POST",
            body: EmptyRequest(),
            authenticated: true
        )
    }

    func verifyPatientRegistration(code: String) async throws -> AuthProfile {
        let response: VerificationResponse = try await client.send(
            "patient-registration/phone/verify",
            method: "POST",
            body: ProviderPhoneVerificationRequest(otp: code),
            authenticated: true
        )
        guard let profile = response.data else { throw APIError.invalidResponse }
        return profile.profile(role: .patient)
    }

    func providers() async throws -> [ProviderDTO] {
        let response: PageEnvelope<ProviderDTO> = try await client.send("providers?per_page=50&map_only=1")
        return response.data
    }

    func doctors() async throws -> [DoctorDTO] {
        let response: PageEnvelope<DoctorDTO> = try await client.send("doctors?per_page=50")
        return response.data
    }

    func createAppointment(_ request: CreateAppointmentRequest) async throws -> AppointmentDTO {
        let response: AppointmentResponse = try await client.send(
            "appointments", method: "POST", body: request, authenticated: true
        )
        return response.data
    }

    func appointments() async throws -> [AppointmentDTO] {
        let response: PageEnvelope<AppointmentDTO> = try await client.send(
            "appointments", authenticated: true
        )
        return response.data
    }

    func updateAppointment(_ appointmentID: Int, status: String) async throws -> AppointmentDTO {
        let response: AppointmentResponse = try await client.send(
            "appointments/\(appointmentID)",
            method: "PATCH",
            body: AppointmentStatusRequest(status: status),
            authenticated: true
        )
        return response.data
    }

    func cancelAppointment(_ appointmentID: Int) async throws {
        let _: LoginMessageResponse = try await client.send(
            "appointments/\(appointmentID)/cancel",
            method: "POST",
            body: EmptyRequest(),
            authenticated: true
        )
    }

    func providerAvailability(providerID: Int, month: String) async throws -> [ProviderUnavailableDateDTO] {
        let response: DataEnvelope<ProviderAvailabilityDTO> = try await client.send(
            "providers/\(providerID)/availability?month=\(month)"
        )
        return response.data.unavailableDates
    }

    func managedProviderAvailability() async throws -> [ProviderUnavailableDateDTO] {
        let response: DataEnvelope<[ProviderUnavailableDateDTO]> = try await client.send(
            "provider-availability", authenticated: true
        )
        return response.data
    }

    func closeProviderDate(date: String, reason: String?) async throws -> ProviderUnavailableDateDTO {
        let response: DataEnvelope<ProviderUnavailableDateDTO> = try await client.send(
            "provider-availability",
            method: "POST",
            body: UnavailableDateRequest(date: date, reason: reason),
            authenticated: true
        )
        return response.data
    }

    func reopenProviderDate(_ unavailableDateID: Int) async throws {
        let _: LoginMessageResponse = try await client.send(
            "provider-availability/\(unavailableDateID)",
            method: "DELETE",
            body: EmptyRequest(),
            authenticated: true
        )
    }

    func mobileData() async throws -> MobileDataDTO {
        let response: DataEnvelope<MobileDataDTO> = try await client.send(
            "mobile-data", authenticated: true
        )
        return response.data
    }

    func sendMessage(threadID: Int, text: String) async throws -> MessageDTO {
        let response: DataEnvelope<MessageDTO> = try await client.send(
            "message-threads/\(threadID)/messages",
            method: "POST",
            body: MessageTextRequest(text: text),
            authenticated: true
        )
        return response.data
    }

    func startConversation(providerID: Int?, doctorID: Int?, text: String) async throws -> MessageThreadDTO {
        let response: DataEnvelope<MessageThreadDTO> = try await client.send(
            "message-threads",
            method: "POST",
            body: StartConversationRequest(
                healthcareProviderId: providerID,
                doctorId: doctorID,
                text: text
            ),
            authenticated: true
        )
        return response.data
    }

    func markThreadRead(_ threadID: Int) async throws {
        let _: LoginMessageResponse = try await client.send(
            "message-threads/\(threadID)/read",
            method: "POST",
            body: EmptyRequest(),
            authenticated: true
        )
    }

    func markNotificationRead(_ notificationID: Int) async throws {
        let _: LoginMessageResponse = try await client.send(
            "notifications/\(notificationID)/read",
            method: "PATCH",
            body: EmptyRequest(),
            authenticated: true
        )
    }

    func markAllNotificationsRead() async throws {
        let _: LoginMessageResponse = try await client.send(
            "notifications/read-all",
            method: "POST",
            body: EmptyRequest(),
            authenticated: true
        )
    }

    func registerPushDevice(token: String, environment: String) async throws {
        guard client.sessionStore.accessToken != nil else { return }
        let _: PushDeviceResponse = try await client.send(
            "push/devices",
            method: "PUT",
            body: PushDeviceRequest(
                token: token,
                environment: environment,
                appBundleId: Bundle.main.bundleIdentifier ?? "ug.afyalink.ios"
            ),
            authenticated: true
        )
    }

    func unregisterPushDevice(token: String) async throws {
        let _: PushDeviceResponse = try await client.send(
            "push/devices",
            method: "DELETE",
            body: ["token": token],
            authenticated: true
        )
    }

    func registrationFacilities() async throws -> [RegistrationFacilityDTO] {
        let response: DataEnvelope<[RegistrationFacilityDTO]> = try await client.send(
            "provider-registration/facilities"
        )
        return response.data
    }

    func createProviderIdentity(_ request: ProviderIdentityRequest) async throws -> ProviderIdentityResponse {
        var form = MultipartFormData()
        form.addField("application_type", value: request.applicationType)
        form.addField("healthcare_provider_id", value: request.healthcareProviderId.map(String.init))
        form.addField("first_name", value: request.firstName)
        form.addField("middle_name", value: request.middleName)
        form.addField("last_name", value: request.lastName)
        form.addField("date_of_birth", value: request.dateOfBirth)
        form.addField("sex", value: request.sex)
        form.addField("nationality", value: request.nationality)
        form.addField("phone", value: request.phone)
        form.addField("email", value: request.email)
        form.addField("nin", value: request.nin)
        form.addField("password", value: request.password)
        form.addField("password_confirmation", value: request.passwordConfirmation)
        form.addField("device_name", value: client.sessionStore.deviceName)
        try form.addFile("profile_photo", url: request.profilePhoto)

        let response: ProviderIdentityResponse = try await client.sendMultipart(
            "provider-registration/identity",
            form: form
        )
        client.sessionStore.accessToken = response.accessToken
        return response
    }

    func providerRegistrationStatus() async throws -> ProviderRegistrationStatusDTO {
        let response: DataEnvelope<ProviderRegistrationStatusDTO> = try await client.send(
            "provider-registration/status",
            authenticated: true
        )
        return response.data
    }

    func resendProviderEmail() async throws -> VerificationResponse {
        try await client.send(
            "provider-registration/email/resend",
            method: "POST",
            body: EmptyRequest(),
            authenticated: true
        )
    }

    func sendProviderPhoneCode() async throws -> VerificationResponse {
        try await client.send(
            "provider-registration/phone/send",
            method: "POST",
            body: EmptyRequest(),
            authenticated: true
        )
    }

    func verifyProviderPhone(code: String) async throws -> VerificationResponse {
        try await client.send(
            "provider-registration/phone/verify",
            method: "POST",
            body: ProviderPhoneVerificationRequest(otp: code),
            authenticated: true
        )
    }

    func submitProviderApplication(_ request: ProviderApplicationRequest) async throws -> ProviderSubmissionResponse {
        var form = MultipartFormData()
        form.addField("application_type", value: request.applicationType)
        form.addField("healthcare_provider_id", value: request.healthcareProviderId.map(String.init))
        for (name, value) in request.regulatoryFields {
            form.addField("regulatory[\(name)]", value: value)
        }
        for service in request.services {
            form.addField("regulatory[services][]", value: service)
        }
        form.addField("declaration", value: "1")
        for (documentType, url) in request.documents {
            try form.addFile("documents[\(documentType)]", url: url)
        }
        return try await client.sendMultipart(
            "provider-registration/application",
            form: form,
            authenticated: true
        )
    }

    func logout() async {
        if client.sessionStore.accessToken != nil {
            let _: LoginMessageResponse? = try? await client.send(
                "auth/logout", method: "POST", body: EmptyRequest(), authenticated: true
            )
        }
        client.sessionStore.accessToken = nil
    }

    func discardSession() {
        client.sessionStore.accessToken = nil
    }
}

private struct ProviderPhoneVerificationRequest: Encodable { let otp: String }

private struct LoginMessageResponse: Decodable { let message: String }

private extension AuthProfileDTO {
    var inferredRole: UserRole {
        ["facility", "professional"].contains(accountType) ? .provider : .patient
    }

    func profile(role: UserRole) -> AuthProfile {
        AuthProfile(
            name: name,
            personName: personName ?? name,
            handle: (personName ?? name).lowercased().replacingOccurrences(of: " ", with: "_"),
            profileID: profileId,
            phone: phone,
            email: email,
            nin: nin,
            accountType: accountType,
            registrationStatus: registrationStatus,
            role: role,
            requiresSecurityOTP: requiresSecurityOtp ?? false,
            twoFactorEnabled: twoFactorEnabled ?? false,
            patientProfile: patientProfile?.profile,
            providerProfile: providerProfile?.profile
        )
    }
}

private extension PatientProfileDTO {
    var profile: PatientAuthProfile {
        PatientAuthProfile(
            dateOfBirth: dateOfBirth,
            gender: gender,
            hasProfilePhoto: hasProfilePhoto ?? false,
            hasCoverPhoto: hasCoverPhoto ?? false,
            bloodGroup: bloodGroup,
            systolic: systolic,
            diastolic: diastolic,
            heartRate: heartRate,
            heightCm: heightCm,
            weightKg: weightKg,
            allergies: allergies,
            chronicConditions: chronicConditions,
            currentMedications: currentMedications,
            emergencyContactName: emergencyContactName,
            emergencyContactRelationship: emergencyContactRelationship,
            emergencyContactPhone: emergencyContactPhone,
            healthDataConsent: healthDataConsent ?? false
        )
    }
}

private extension ProviderProfileDTO {
    var profile: ProviderAuthProfile {
        ProviderAuthProfile(
            applicationType: applicationType,
            displayName: displayName,
            roleTitle: roleTitle,
            registrationStatus: registrationStatus,
            registrationReference: registrationReference,
            workplace: workplace,
            facilityType: facilityType,
            ownership: ownership,
            district: district,
            town: town,
            address: address,
            phone: phone,
            email: email,
            licenceNumber: licenceNumber,
            licenceExpiry: licenceExpiry,
            specialty: specialty,
            open24Hours: open24Hours,
            emergencyAvailable: emergencyAvailable,
            services: services
        )
    }
}

private extension String {
    var nilIfEmpty: String? {
        isEmpty ? nil : self
    }
}

extension UserRole {
    var apiValue: String { self == .patient ? "patient" : "provider" }
}
