//
//  ChatSupport.swift
//  AuraHealth
//
//  Created by Osxent on 12/10/2026.
//

import SwiftUI
import UIKit

// MARK: - Attachments

/// What a message can carry besides text.
enum ChatAttachment {
    case image(UIImage)
    case document(name: String, sizeText: String, url: URL)
    case voice(url: URL, duration: TimeInterval)
}

// MARK: - Who you're talking to (header, avatars, status)

struct ChatParticipant {
    let name: String
    let assetName: String?      // optional photo in Assets.xcassets, named exactly like the person
    let symbol: String?         // SF Symbol fallback for automated senders
    let isVerified: Bool
    let isAutomated: Bool
    let isActive: Bool
    let lastSeen: Date?

    /// "Active now" or "Last seen 2 hours ago"
    var statusText: String {
        if isAutomated { return "Automated reminders" }
        if isActive { return "Active now" }
        guard let lastSeen else { return "Last seen recently" }
        let formatter = RelativeDateTimeFormatter()
        formatter.unitsStyle = .full
        return "Last seen " + formatter.localizedString(for: lastSeen, relativeTo: Date())
    }

    static func from(_ thread: MessageThread?, name: String) -> ChatParticipant {
        return ChatParticipant(
            name: name,
            assetName: name,
            symbol: thread?.isAutomated == true ? thread?.symbol : nil,
            isVerified: thread?.isVerified ?? false,
            isAutomated: thread?.isAutomated ?? false,
            isActive: thread?.isOnline ?? false,
            lastSeen: thread?.lastActiveAt
        )
    }
}

// MARK: - Round avatar: photo → symbol → initials

struct AvatarView: View {
    let name: String
    var assetName: String? = nil
    var symbol: String? = nil
    var size: CGFloat = 36
    var showsActiveDot = false

    private var initials: String {
        name.split(separator: " ")
            .filter { !$0.hasSuffix(".") }           // skip "Dr."
            .prefix(2)
            .compactMap { $0.first }
            .map(String.init)
            .joined()
    }

    var body: some View {
        ZStack {
            if let assetName, let uiImage = UIImage(named: assetName) {
                Image(uiImage: uiImage).resizable().scaledToFill()
            } else if let symbol {
                AppColor.primaryTint
                Image(systemName: symbol)
                    .font(.system(size: size * 0.42))
                    .foregroundStyle(AppColor.primary)
            } else {
                AppColor.primaryFixed
                Text(initials)
                    .font(.system(size: size * 0.38, weight: .bold))
                    .foregroundStyle(AppColor.primary)
            }
        }
        .frame(width: size, height: size)
        .clipShape(Circle())
        .overlay(alignment: .bottomTrailing) {          // after clipShape so it isn't cut off
            if showsActiveDot {
                Circle()
                    .fill(AppColor.success)
                    .frame(width: size * 0.3, height: size * 0.3)
                    .overlay(Circle().stroke(AppColor.surface, lineWidth: 2))
            }
        }
    }
}
