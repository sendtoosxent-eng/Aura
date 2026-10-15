import SwiftUI

// SCREEN 13 — Notifications (design: notifications)
// Filter chips, notifications grouped by day, unread dots, "mark all read" button.
struct NotificationsView: View {
    @Environment(AppState.self) private var appState
    @State private var filter: NotificationFilter = .all
    @State private var showOlder = false

    private var items: [NotificationItem] { appState.notifications }

    private var sections: [String] { showOlder ? ["Today", "Yesterday", "Earlier"] : ["Today", "Yesterday"] }

    private func rows(in section: String) -> [NotificationItem] {
        items.filter { $0.section == section && (filter == .all || $0.category == filter) }
    }

    var body: some View {
        ScrollView {
            VStack(alignment: .leading, spacing: 0) {
                chips.padding(Spacing.md)

                ForEach(sections, id: \.self) { section in
                    let rows = self.rows(in: section)
                    if !rows.isEmpty {
                        Text(section.uppercased())
                            .font(AppFont.labelLg).tracking(1)
                            .foregroundStyle(AppColor.onSurfaceVariant)
                            .padding(.horizontal, Spacing.md).padding(.vertical, Spacing.sm)
                            .frame(maxWidth: .infinity, alignment: .leading)

                        VStack(spacing: 0) {
                            ForEach(rows) { item in
                                row(item)
                                Divider()
                            }
                        }
                        .background(AppColor.surface)
                    }
                }

                if !showOlder {
                    SecondaryButton(title: "View Older Notifications") {
                        withAnimation { showOlder = true }
                    }
                    .frame(maxWidth: 280)
                    .frame(maxWidth: .infinity)
                    .padding(Spacing.lg)
                }
            }
            .readableWidth()
            .padding(.bottom, 80)   // room for the floating button
        }
        .background(AppColor.background.ignoresSafeArea())
        .redInlineTitle("Notifications")
        .overlay(alignment: .bottomTrailing) { markAllReadButton }
    }

    // MARK: Pieces

    private var chips: some View {
        ScrollView(.horizontal, showsIndicators: false) {
            HStack(spacing: Spacing.sm) {
                ForEach(NotificationFilter.allCases) { f in
                    Button { filter = f } label: {
                        Text(f.rawValue)
                            .font(AppFont.labelLg)
                            .foregroundStyle(filter == f ? Color.white : AppColor.onSurface)
                            .padding(.horizontal, Spacing.lg).frame(minHeight: 44)
                            .background(filter == f ? AppColor.primary : AppColor.surface)
                            .clipShape(Capsule())
                            .overlay(Capsule().stroke(AppColor.outlineVariant, lineWidth: filter == f ? 0 : 1))
                    }
                }
            }
        }
        .scrollClipDisabled()
    }

    private func row(_ item: NotificationItem) -> some View {
        // Tapping a row marks it as read.
        Button {
            appState.markNotificationRead(item)
        } label: {
            HStack(alignment: .top, spacing: Spacing.md) {
                Image(systemName: item.icon)
                    .font(.title3)
                    .foregroundStyle(item.category == .system ? AppColor.onSurfaceVariant : AppColor.primary)
                    .frame(width: 48, height: 48)
                    .background(item.category == .system ? AppColor.surfaceLow : AppColor.primaryTint)
                    .clipShape(Circle())

                VStack(alignment: .leading, spacing: 4) {
                    HStack(alignment: .top) {
                        Text(item.title).font(AppFont.labelLg).foregroundStyle(AppColor.onSurface)
                            .multilineTextAlignment(.leading)
                        Spacer(minLength: Spacing.sm)
                        Text(item.timeText).font(AppFont.labelSm).foregroundStyle(AppColor.onSurfaceVariant)
                        if item.isUnread {
                            Circle().fill(AppColor.primary).frame(width: 8, height: 8).padding(.top, 4)
                        }
                    }
                    Text(item.message).font(AppFont.bodyMd).foregroundStyle(AppColor.onSurfaceVariant)
                        .multilineTextAlignment(.leading)
                }
            }
            .padding(Spacing.md)
            .frame(maxWidth: .infinity, alignment: .leading)
            .contentShape(Rectangle())
        }
        .buttonStyle(.plain)
    }

    private var markAllReadButton: some View {
        Button {
            withAnimation { appState.markAllNotificationsRead() }
        } label: {
            Image(systemName: "checkmark.circle")
                .font(.title2).foregroundStyle(.white)
                .frame(width: 60, height: 60)
                .background(AppColor.primary).clipShape(Circle())
                .shadow(color: .black.opacity(0.2), radius: 8, y: 4)
        }
        .padding(Spacing.md)
        .accessibilityLabel("Mark all as read")
    }
}

#Preview {
    NavigationStack { NotificationsView() }.environment(AppState())
}
