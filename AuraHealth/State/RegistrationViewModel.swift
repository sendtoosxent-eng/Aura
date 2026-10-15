import SwiftUI
import Observation
import UIKit

// MARK: - Supporting types

enum RegistrationStep: Int, CaseIterable {
    case bio, vitals, nextOfKin

    var title: String {
        switch self {
        case .bio:       return "Basic Information"
        case .vitals:    return "Health Vitals"
        case .nextOfKin: return "Next of Kin"
        }
    }

    var subtitle: String {
        switch self {
        case .bio:       return "Tell us who you are. Every field on this step is required."
        case .vitals:    return "Optional, but it helps health providers treat you faster in an emergency."
        case .nextOfKin: return "Who should we contact if you can't speak for yourself?"
        }
    }
    /// Short label under each stepper circle.
    var shortTitle: String {
        switch self {
        case .bio:       return "Profile"
        case .vitals:    return "Vitals"
        case .nextOfKin: return "Next of kin"
        }
    }

    /// SF Symbol shown in the heading tile.
    var icon: String {
        switch self {
        case .bio:       return "person.text.rectangle"
        case .vitals:    return "heart.text.square"
        case .nextOfKin: return "person.2.fill"
        }
    }

    var isOptional: Bool { self == .vitals }
}

enum BloodGroup: String, CaseIterable, Identifiable {
    case aPos = "A+", aNeg = "A-", bPos = "B+", bNeg = "B-"
    case abPos = "AB+", abNeg = "AB-", oPos = "O+", oNeg = "O-"
    var id: String { rawValue }
}

enum Relationship: String, CaseIterable, Identifiable {
    case spouse = "Spouse", parent = "Parent", child = "Child", sibling = "Sibling"
    case guardian = "Guardian", relative = "Other relative", friend = "Friend"
    var id: String { rawValue }
}

// MARK: - View model

@Observable
final class RegistrationViewModel {

    // Navigation
    var step: RegistrationStep = .bio
    var showErrors = false            // becomes true when Next is tapped with problems

    // Step 1: Bio (all required)
    var profileImage: UIImage?
    var coverImage: UIImage?
    var fullName = ""
    var phoneDigits = ""              // only the 9 digits after +256
    var email = ""
    var nin = ""
    var password = ""
    var passwordConfirmation = ""
    var usernameSuffix = Int.random(in: 100...999)

    // Step 2: Vitals (all optional). Text, because TextFields edit text.
    var bloodGroup: BloodGroup?
    var systolic = ""
    var diastolic = ""
    var heartRate = ""
    var heightCm = ""
    var weightKg = ""
    var allergies = ""
    var conditions = ""

    // Step 3: Next of kin (required) + consent
    var kinName = ""
    var kinRelationship: Relationship?
    var kinPhoneDigits = ""
    var shareWithProviders = false

    // MARK: Derived values

    /// "Musa Balondemu" → "musa.balondemu482". Recomputed live as the name changes.
    var username: String {
        let words = fullName.lowercased()
            .split(separator: " ")
            .map { String($0).filter { $0.isASCII && $0.isLetter } }
            .filter { !$0.isEmpty }
        guard let first = words.first else { return "" }
        let handle = words.count > 1 ? "\(first).\(words[words.count - 1])" : first
        return "\(handle)\(usernameSuffix)"
    }

    func regenerateUsername() { usernameSuffix = Int.random(in: 100...999) }

    var submittedPhone: String { "+256\(phoneDigits)" }

    // MARK: Step 1 errors (nil = valid)

    var photoError: String? { profileImage == nil ? "Add a profile photo" : nil }
    var coverError: String? { coverImage == nil ? "Add a cover photo" : nil }
    var nameError: String? {
        Validators.isValidName(fullName) ? nil : "Enter your first and last name"
    }
    var phoneError: String? {
        Validators.isValidUgandaPhone(phoneDigits) ? nil : "Enter a valid number, e.g. 770 000 000"
    }
    var emailError: String? {
        Validators.isValidEmail(email) ? nil : "Enter a valid email, e.g. name@example.com"
    }
    var ninError: String? {
        Validators.isValidNIN(nin) ? nil : "Enter a valid 14-character NIN, e.g. CM12345678ABCD"
    }
    var passwordError: String? {
        guard password.count >= 8,
              password.rangeOfCharacter(from: .uppercaseLetters) != nil,
              password.rangeOfCharacter(from: .lowercaseLetters) != nil,
              password.rangeOfCharacter(from: .decimalDigits) != nil,
              password.rangeOfCharacter(from: .alphanumerics.inverted) != nil else {
            return "Use 8+ characters with upper/lowercase, a number and a symbol"
        }
        return nil
    }
    var passwordConfirmationError: String? {
        passwordConfirmation == password ? nil : "Passwords do not match"
    }
    var bioIsValid: Bool {
        [photoError, coverError, nameError, phoneError, emailError, ninError,
         passwordError, passwordConfirmationError]
            .allSatisfy { $0 == nil }
    }

    // MARK: Step 2 errors. Blank is fine; if filled it must make sense.

    private func rangeError(_ text: String, _ range: ClosedRange<Int>, _ message: String) -> String? {
        let t = text.trimmingCharacters(in: .whitespaces)
        if t.isEmpty { return nil }
        guard let value = Int(t), range.contains(value) else { return message }
        return nil
    }

    var bpError: String? {
        if systolic.isEmpty && diastolic.isEmpty { return nil }
        guard let s = Int(systolic), let d = Int(diastolic),
              (70...250).contains(s), (40...150).contains(d), s > d
        else { return "Enter both numbers, e.g. 120 / 80" }
        return nil
    }
    var heartRateError: String? { rangeError(heartRate, 30...220, "Enter a number between 30 and 220") }
    var heightError: String? { rangeError(heightCm, 50...250, "Enter height in cm, 50 to 250") }
    var weightError: String? { rangeError(weightKg, 2...400, "Enter weight in kg, 2 to 400") }
    var vitalsIsValid: Bool {
        [bpError, heartRateError, heightError, weightError].allSatisfy { $0 == nil }
    }

    // MARK: Step 3 errors

    var kinNameError: String? {
        Validators.isValidName(kinName) ? nil : "Enter their first and last name"
    }
    var kinRelationshipError: String? { kinRelationship == nil ? "Choose a relationship" : nil }
    var kinPhoneError: String? {
        if !Validators.isValidUgandaPhone(kinPhoneDigits) { return "Enter a valid number, e.g. 770 000 000" }
        if kinPhoneDigits == phoneDigits { return "Use a different number from your own" }
        return nil
    }
    var consentError: String? {
        shareWithProviders ? nil : "Please allow providers to view this in an emergency"
    }
    var kinIsValid: Bool {
        [kinNameError, kinRelationshipError, kinPhoneError, consentError].allSatisfy { $0 == nil }
    }

    // MARK: Moving between steps

    var isFirstStep: Bool { step == .bio }
    var isLastStep: Bool { step == .nextOfKin }

    private var currentStepIsValid: Bool {
        switch step {
        case .bio:       return bioIsValid
        case .vitals:    return vitalsIsValid
        case .nextOfKin: return kinIsValid
        }
    }

    /// Tries to go forward. Returns TRUE only when the LAST step passed (= form finished).
    func advance() -> Bool {
        guard currentStepIsValid else { showErrors = true; return false }
        showErrors = false
        if let next = RegistrationStep(rawValue: step.rawValue + 1) {
            step = next
            return false
        }
        return true
    }

    /// Goes back one step. Returns TRUE if there was no previous step (caller should leave).
    func goBack() -> Bool {
        showErrors = false
        if let previous = RegistrationStep(rawValue: step.rawValue - 1) {
            step = previous
            return false
        }
        return true
    }

    /// "Skip this step" on the optional vitals screen.
    func skipVitals() {
        bloodGroup = nil
        systolic = ""; diastolic = ""; heartRate = ""
        heightCm = ""; weightKg = ""; allergies = ""; conditions = ""
        showErrors = false
        step = .nextOfKin
    }

    // TEMPORARY: lets us test navigation before the real fields exist. Delete later.
    func fillTestData() {
        profileImage = UIImage(systemName: "person.fill")
        coverImage = UIImage(systemName: "photo")
        fullName = "Musa Balondemu"; phoneDigits = "770123456"
        email = "musa@example.com"; nin = "CM12345678ABCD"
        password = "Strong!Pass1"; passwordConfirmation = "Strong!Pass1"
        kinName = "Sarah Namono"; kinRelationship = .spouse
        kinPhoneDigits = "780123456"; shareWithProviders = true
    }
}
