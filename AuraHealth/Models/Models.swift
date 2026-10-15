import Foundation
import CoreLocation

// MARK: - Authentication

struct AuthProfile: Equatable {
    let name: String
    let personName: String
    let handle: String
    let profileID: String
    let phone: String
    let email: String?
    let nin: String?
    let accountType: String
    let registrationStatus: String?
    let role: UserRole
    let requiresSecurityOTP: Bool
    let patientProfile: PatientAuthProfile?
    let providerProfile: ProviderAuthProfile?

    var initials: String {
        name.split(separator: " ")
            .prefix(2)
            .compactMap(\.first)
            .map(String.init)
            .joined()
    }
}

struct PatientAuthProfile: Equatable {
    let dateOfBirth: String?
    let gender: String?
    let hasProfilePhoto: Bool
    let hasCoverPhoto: Bool
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
    let healthDataConsent: Bool
}

struct ProviderAuthProfile: Equatable {
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
    let services: [String]
}

// =============================================================================
// MODELS  —  plain data types. No UI here.
//
// LEARN: A `struct` is a value type: copying it makes an independent copy.
// Most data in SwiftUI apps is structs. `Identifiable` gives each item an `id`
// so ForEach / List can track it. `Hashable` lets it be used in a Set or as a
// NavigationStack route value.
// =============================================================================

// MARK: - Provider

enum ProviderType: String, CaseIterable, Identifiable, Hashable {
    // LEARN: An enum with String raw values: `.hospital.rawValue == "Hospital"`.
    case hospital = "Hospital"
    case clinic = "Clinic"
    case pharmacy = "Pharmacy"
    case lab = "Laboratory"
    case ambulance = "Ambulance"

    var id: String { rawValue }

    /// SF Symbol name (Apple's built-in icon set — no image files needed).
    var icon: String {
        switch self {
        case .hospital:  return "building.2.fill"
        case .clinic:    return "cross.case.fill"
        case .pharmacy:  return "pills.fill"
        case .lab:       return "testtube.2"
        case .ambulance: return "light.beacon.max.fill"
        }
    }
}

// MARK: - Conditions and care services

enum CareServiceGroup: String, CaseIterable, Identifiable {
    case commonConditions = "Common conditions"
    case womensHealth = "Women's health"
    case ongoingCare = "Ongoing care"
    case specialistCare = "Specialist care"

    var id: String { rawValue }
}

/// Terms patients commonly use when looking for treatment or preventive care.
enum CareService: String, CaseIterable, Identifiable, Hashable {
    case malaria = "Malaria testing & treatment"
    case respiratory = "Cough, flu & respiratory care"
    case typhoid = "Typhoid testing & treatment"
    case hiv = "HIV testing & care"
    case tuberculosis = "Tuberculosis care"
    case antenatal = "Antenatal care"
    case postnatal = "Postnatal care"
    case familyPlanning = "Family planning"
    case maternity = "Maternity & delivery"
    case immunisation = "Immunisation"
    case diabetes = "Diabetes care"
    case hypertension = "Hypertension care"
    case mentalHealth = "Mental health"
    case pediatrics = "Child health & pediatrics"
    case dental = "Dental care"
    case eyeCare = "Eye care"
    case laboratory = "Laboratory tests"

    var id: String { rawValue }

    var group: CareServiceGroup {
        switch self {
        case .malaria, .respiratory, .typhoid, .hiv, .tuberculosis:
            return .commonConditions
        case .antenatal, .postnatal, .familyPlanning, .maternity:
            return .womensHealth
        case .immunisation, .diabetes, .hypertension, .mentalHealth:
            return .ongoingCare
        case .pediatrics, .dental, .eyeCare, .laboratory:
            return .specialistCare
        }
    }

    var icon: String {
        switch self {
        case .malaria: return "allergens"
        case .respiratory: return "lungs.fill"
        case .typhoid, .tuberculosis: return "cross.case.fill"
        case .hiv: return "ribbon"
        case .antenatal, .postnatal, .maternity: return "figure.and.child.holdinghands"
        case .familyPlanning: return "person.2.fill"
        case .immunisation: return "syringe.fill"
        case .diabetes: return "drop.fill"
        case .hypertension: return "heart.text.square.fill"
        case .mentalHealth: return "brain.head.profile"
        case .pediatrics: return "figure.2.and.child.holdinghands"
        case .dental: return "mouth.fill"
        case .eyeCare: return "eye.fill"
        case .laboratory: return "testtube.2"
        }
    }
}

struct Provider: Identifiable, Hashable {
    // LEARN: `let id = UUID()` — every Provider gets a unique id when created.
    // Because it has a default value, it's NOT part of the memberwise init.
    let id = UUID()

    let name: String
    let address: String
    let type: ProviderType
    let rating: Double
    let reviewCount: Int
    let distanceKm: Double
    let isOpenNow: Bool
    let is24Hours: Bool
    let closesAt: String?        // e.g. "6PM" — nil for 24h places
    let phone: String
    let latitude: Double
    let longitude: Double
    let services: [String]
    var isVerified: Bool = true
    var imageURL: URL? = nil

    // LEARN: Computed properties — calculated every time you read them.
    var coordinate: CLLocationCoordinate2D {
        CLLocationCoordinate2D(latitude: latitude, longitude: longitude)
    }

    /// "Open 24 Hours" / "Closes 6PM" / "Closed"
    var statusText: String {
        if is24Hours { return "Open 24 Hours" }
        if isOpenNow { return closesAt.map { "Closes \($0)" } ?? "Open Now" }
        return "Closed"
    }

    /// Shorter version for the favourites list.
    var shortStatus: String {
        if is24Hours { return "Open 24h" }
        if isOpenNow { return closesAt.map { "Closes \($0)" } ?? "Open" }
        return "Closed"
    }

    var reviewCountText: String {
        reviewCount >= 1000
            ? String(format: "%.1fk", Double(reviewCount) / 1000)
            : "\(reviewCount)"
    }

    var distanceText: String { String(format: "%.1f km", distanceKm) }

    /// Rough drive time. Real apps ask a routing API; this is a placeholder.
    var etaMinutes: Int { max(2, Int((distanceKm * 2.5).rounded()) + 1) }
}

// MARK: - Doctors

/// A clinician linked to the facility where the appointment will take place.
/// Keeping doctors in the shared model lets profiles, maps and booking use the
/// same source of truth.
struct CareDoctor: Identifiable, Hashable {
    let id = UUID()
    let name: String
    let specialty: String
    let availability: String
    let rating: Double
    let imageName: String
    let isAvailableNow: Bool
    let provider: Provider
    let latitude: Double
    let longitude: Double

    var coordinate: CLLocationCoordinate2D {
        CLLocationCoordinate2D(latitude: latitude, longitude: longitude)
    }
}

// MARK: - Reviews

enum ReviewFilter: String, CaseIterable, Identifiable {
    case all = "All Reviews"
    case hospitals = "Hospitals"
    case specialists = "Specialists"
    case pharmacies = "Pharmacies"
    var id: String { rawValue }
}

/// A review that the signed-in user WROTE (shown on "My Reviews").
struct Review: Identifiable, Hashable {
    let id = UUID()
    let providerName: String
    let dateText: String
    let rating: Int
    let text: String
    let helpfulCount: Int
    let category: ReviewFilter   // never .all
}

/// A review someone else wrote, shown on a provider's profile.
struct PublicReview: Identifiable, Hashable {
    let id = UUID()
    let author: String
    let timeAgo: String
    let rating: Int
    let text: String

    var initials: String {
        author.split(separator: " ").prefix(2).compactMap { $0.first }.map(String.init).joined()
    }
}

// MARK: - Notifications

enum NotificationFilter: String, CaseIterable, Identifiable {
    case all = "All"
    case alerts = "Alerts"
    case updates = "Updates"
    case system = "System"
    var id: String { rawValue }
}

struct NotificationItem: Identifiable, Hashable {
    let id = UUID()
    var backendID: Int? = nil
    let title: String
    let message: String
    let timeText: String
    let category: NotificationFilter // alerts / updates / system (never .all)
    let icon: String                 // SF Symbol
    var isUnread: Bool
    let section: String              // "Today", "Yesterday", "Earlier"
}

// MARK: - Messaging

enum MessageAuthor: Hashable {
    case user, provider, automated
}

struct ChatMessage: Identifiable, Hashable {
    let id = UUID()
    var backendID: Int? = nil
    let author: MessageAuthor
    let text: String
    let timeText: String
}

struct MessageThread: Identifiable, Hashable {
    let id = UUID()
    var backendID: Int? = nil
    let participantName: String
    let subtitle: String
    let symbol: String
    var isOnline: Bool
    var isAutomated: Bool
    var isVerified: Bool = false
    var lastActiveAt: Date? = nil
    var unreadCount: Int
    var messages: [ChatMessage]

    var lastMessage: ChatMessage? { messages.last }
}
