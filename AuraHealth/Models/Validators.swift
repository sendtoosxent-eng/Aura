//
//  Validators.swift
//  AuraHealth
//
//  Created by Osxent on 12/10/2026.
//
import Foundation

enum Validators {
static func isValidName(_ text: String) -> Bool {
    let parts = text.trimmingCharacters(in: .whitespaces).split(separator: " ")
    return parts.count >= 2 && parts.allSatisfy { $0.count >= 2}
    
}
    static func isValidUgandaPhone(_ digits: String) -> Bool {
        digits.count == 9 &&   digits.allSatisfy(\.isNumber) && digits.first == "7"
    }
    
    static func isValidEmail(_ text: String) -> Bool {
        let email = text.trimmingCharacters(in: .whitespaces)
        let pattern = #"^[A-Z0-9a-z._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$"#
        return email.range(of: pattern, options: .regularExpression)  != nil
    }
    
    private static let ninPattern = #"^C[MF][0-9]{8}[A-Z0-9]{4}$"#
    
    static func normalizeNIN(_ text: String) -> String {
        text.uppercased().filter { $0.isLetter || $0.isNumber}

    }
    static func isValidNIN(_ text: String) -> Bool {
        normalizeNIN(text).range(of: ninPattern, options: .regularExpression) != nil
    }
}

