import Foundation

enum ProviderApplicationType: String, CaseIterable, Identifiable, Hashable {
    case facility = "Healthcare Facility"
    case professional = "Health Professional"

    var id: String { rawValue }
    var backendValue: String { self == .facility ? "facility" : "professional" }
    var icon: String { self == .facility ? "building.2.fill" : "stethoscope" }
}

struct ProviderAccountProfile: Equatable {
    var applicationType: ProviderApplicationType = .professional
    var displayName = "Dr. Mugisha"
    var roleTitle = "Health Professional"
    var registrationStatus = "Verified"
    var registrationReference = "HCP-2048"
    var workplace = "Mulago National Hospital"
    var facilityType = ""
    var ownership = ""
    var district = "Kampala"
    var town = ""
    var address = ""
    var phone = "+256 700 000 000"
    var email = ""
    var licenceNumber = ""
    var licenceExpiry = Date().addingTimeInterval(365 * 86_400)
    var specialty = "General Medicine"
    var open24Hours = false
    var emergencyAvailable = false
    var services: [String] = []
}

enum AppointmentStatus: String, CaseIterable, Hashable {
    case requested = "Requested"
    case confirmed = "Confirmed"
    case completed = "Completed"
    case cancelled = "Cancelled"
}

struct CareAppointment: Identifiable, Hashable {
    let id = UUID()
    var backendID: Int? = nil
    let providerName: String
    let doctorName: String
    var patientName: String? = nil
    let service: String
    let date: Date
    let time: String
    let reason: String
    var status: AppointmentStatus
}

struct ProviderUnavailableDate: Identifiable, Hashable {
    let id: Int
    let date: String
    let reason: String?
}

struct MedicationReminder: Identifiable, Hashable {
    let id = UUID()
    var medicine: String
    var instructions: String
    var times: [String]
    var nextRefill: Date?
    var isEnabled: Bool
    var takenToday: Bool
}

enum MedicalRecordType: String, CaseIterable, Hashable {
    case prescription = "Prescription"
    case laboratory = "Laboratory Result"
    case visit = "Visit Summary"
    case diagnosis = "Diagnosis"

    var icon: String {
        switch self {
        case .prescription: return "pills.fill"
        case .laboratory: return "testtube.2"
        case .visit: return "cross.case.fill"
        case .diagnosis: return "stethoscope"
        }
    }
}

struct MedicalRecord: Identifiable, Hashable {
    let id = UUID()
    let title: String
    let provider: String
    let date: Date
    let type: MedicalRecordType
    let detail: String
}

struct SavedCareLocation: Identifiable, Hashable {
    let id = UUID()
    let name: String
    let detail: String
    let latitude: Double
    let longitude: Double
}

struct EmergencyHealthProfile: Equatable {
    var bloodGroup = "Not provided"
    var bloodPressure = "Not provided"
    var heartRate = "Not provided"
    var height = "Not provided"
    var weight = "Not provided"
    var allergies = "None provided"
    var conditions = "None provided"
    var medications = "None provided"
    var nextOfKin = "Not provided"
    var nextOfKinRelationship = ""
    var nextOfKinPhone = "Not provided"
}

struct TrustedSession: Identifiable, Hashable {
    let id = UUID()
    let device: String
    let location: String
    let lastActive: String
    var isCurrent: Bool
}
