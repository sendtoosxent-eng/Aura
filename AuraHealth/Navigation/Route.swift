import SwiftUI

// =============================================================================
// ROUTES — "which screen can I push?"
//
// LEARN: NavigationStack is driven by VALUES. A button says
//     NavigationLink(value: Route.provider(p)) { ... }
// and the stack looks up which screen matches that value in
// `.navigationDestination(for: Route.self)`. Benefits: screens don't need to
// know about each other, and adding a screen = one new case + one new line.
// =============================================================================
enum Route: Hashable {
    case provider(Provider)
    case notifications
    case myReviews
    case favorites
    case chat(String)
    case writeReview(Provider)
    case appointments
    case bookAppointment(Provider)
    case bookDoctor(CareDoctor)
    case reminders
    case medicalRecords
    case locationPicker
    case emergencyHealthCard
    case security
}

extension View {
    /// Attach this once at the root of each NavigationStack.
    func appDestinations() -> some View {
        self.navigationDestination(for: Route.self) { route in
            switch route {
            case .provider(let provider):
                ProviderProfileView(provider: provider)
            case .notifications:
                NotificationsView()
            case .myReviews:
                MyReviewsView()
            case .favorites:
                FavoritesView()
            case .chat(let participantName):
                ConversationView(participantName: participantName)
            case .writeReview(let provider):
                WriteReviewView(provider: provider)
            case .appointments:
                AppointmentsView()
            case .bookAppointment(let provider):
                BookAppointmentView(provider: provider)
            case .bookDoctor(let doctor):
                BookAppointmentView(provider: doctor.provider, doctor: doctor)
            case .reminders:
                MedicationRemindersView()
            case .medicalRecords:
                MedicalRecordsView()
            case .locationPicker:
                LocationPickerView()
            case .emergencyHealthCard:
                EmergencyHealthCardView()
            case .security:
                SecurityView()
            }
        }
    }
}
