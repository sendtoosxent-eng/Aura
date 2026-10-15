import SwiftUI

// SCREEN 16, Provider portal (redesigned)
// Tabs: Dashboard · Inbox · Visits · Practice · Profile

struct ProviderPortalView: View {
    private enum PortalTab: Hashable { case dashboard, inbox, appointments, practice, profile }
    @State private var selectedTab: PortalTab = .dashboard

    var body: some View {
        TabView(selection: $selectedTab) {
            NavigationStack {
                ProviderDashboardView(
                    onAddService: { selectedTab = .practice },
                    onEditHours: { selectedTab = .practice },
                    onViewReviews: { selectedTab = .practice },
                    onViewAppointments: { selectedTab = .appointments }
                )
                .appDestinations()          // lets the bell push Route.notifications
            }
            .tabItem { Label("Dashboard", systemImage: "square.grid.2x2") }
            .tag(PortalTab.dashboard)

            NavigationStack { ProviderInboxView().appDestinations() }
                .tabItem { Label("Inbox", systemImage: "message.fill") }
                .tag(PortalTab.inbox)

            NavigationStack { ProviderAppointmentsView() }
                .tabItem { Label("Visits", systemImage: "calendar") }
                .tag(PortalTab.appointments)

            NavigationStack { ProviderPracticeView() }
                .tabItem { Label("Practice", systemImage: "cross.case") }
                .tag(PortalTab.practice)

            NavigationStack { ProviderProfileTab() }
                .tabItem { Label("Profile", systemImage: "person") }
                .tag(PortalTab.profile)
        }
        .tint(AppColor.primary)
    }
}

// MARK: - Demo data (replace with API data later)

// LEARN: describing rows as small Identifiable structs lets a ForEach build the UI.
// `private` at file level means only this file can see them.
private struct DayViews: Identifiable {
    let id = UUID()
    let day: String
    let value: Int
}

private struct TodayVisit: Identifiable {
    let id = UUID()
    let time: String
    let patient: String
    let reason: String
    let isConfirmed: Bool
}

// MARK: - Dashboard

struct ProviderDashboardView: View {
    var onAddService: () -> Void = {}
    var onEditHours: () -> Void = {}
    var onViewReviews: () -> Void = {}
    var onViewAppointments: () -> Void = {}

    // Local for now. A real app would send this change to your API.
    @State private var acceptingPatients = true

    private let weeklyViews = [
        DayViews(day: "Mon", value: 120), DayViews(day: "Tue", value: 160),
        DayViews(day: "Wed", value: 140), DayViews(day: "Thu", value: 210),
        DayViews(day: "Fri", value: 190), DayViews(day: "Sat", value: 260),
        DayViews(day: "Sun", value: 204)
    ]

    private let visits = [
        TodayVisit(time: "9:00 AM", patient: "John Doe", reason: "General consultation", isConfirmed: true),
        TodayVisit(time: "10:30 AM", patient: "Sarah Namono", reason: "Follow-up visit", isConfirmed: true),
        TodayVisit(time: "2:00 PM", patient: "Peter Okello", reason: "Blood pressure check", isConfirmed: false)
    ]

    private var peakValue: Int { weeklyViews.map(\.value).max() ?? 1 }

    private var greeting: String {
        let hour = Calendar.current.component(.hour, from: Date())
        if hour < 12 { return "Good morning" }
        if hour < 17 { return "Good afternoon" }
        return "Good evening"
    }

    var body: some View {
        VStack(spacing: 0) {
            topBar

            ScrollView {
                VStack(alignment: .leading, spacing: Spacing.lg) {
                    hero
                    statsRow
                    completenessCard
                    viewsCard
                    quickActions
                    todaysVisits
                    recentActivity
                }
                .padding(Spacing.md)
                .readableWidth()
            }
        }
        .background(AppColor.background.ignoresSafeArea())
        .toolbar(.hidden, for: .navigationBar)
    }

    // MARK: Top bar

    private var topBar: some View {
        HStack(spacing: Spacing.sm) {
            BrandMark(size: 36)
            Text(AppConfig.fullName)
                .font(AppFont.headlineMd)
                .foregroundStyle(AppColor.onSurface)
            Spacer()

            NavigationLink(value: Route.notifications) {
                ZStack(alignment: .topTrailing) {
                    Image(systemName: "bell")
                        .font(.title3)
                        .foregroundStyle(AppColor.onSurface)
                    Circle().fill(AppColor.primary)
                        .frame(width: 9, height: 9)
                        .offset(x: 2, y: -2)
                }
                .frame(width: 44, height: 44)
            }
            .accessibilityLabel("Notifications")
        }
        .padding(.horizontal, Spacing.md)
        .frame(minHeight: 56)
        .background(AppColor.background)
        .overlay(alignment: .bottom) { Divider() }
    }

    // MARK: Hero

    private var hero: some View {
        VStack(alignment: .leading, spacing: Spacing.md) {
            HStack(alignment: .top, spacing: Spacing.md) {
                VStack(alignment: .leading, spacing: 4) {
                    Text(greeting)
                        .font(AppFont.bodyMd)
                        .foregroundStyle(Color.white.opacity(0.85))

                    HStack(spacing: 6) {
                        Text("Dr. Mugisha")
                            .font(AppFont.headlineLg)
                            .foregroundStyle(Color.white)
                        Image(systemName: "checkmark.seal.fill")
                            .foregroundStyle(Color.white)
                            .accessibilityLabel("Verified")
                    }

                    Text("Mulago National Hospital")
                        .font(AppFont.bodyMd)
                        .foregroundStyle(Color.white.opacity(0.85))
                }

                Spacer(minLength: Spacing.sm)

                Text("DM")
                    .font(.system(.title3, weight: .bold))
                    .foregroundStyle(AppColor.primary)
                    .frame(width: 56, height: 56)
                    .background(Color.white)
                    .clipShape(Circle())
            }

            // `$acceptingPatients.animation()` animates the label change below.
            Toggle(isOn: $acceptingPatients.animation()) {
                VStack(alignment: .leading, spacing: 2) {
                    Text(acceptingPatients ? "Accepting patients" : "Not accepting patients")
                        .font(AppFont.labelLg)
                        .foregroundStyle(Color.white)
                    Text(acceptingPatients ? "Visible in search and Emergency Mode" : "Hidden from search results")
                        .font(AppFont.labelSm)
                        .foregroundStyle(Color.white.opacity(0.8))
                }
            }
            .toggleStyle(SwitchToggleStyle(tint: AppColor.success))
            .padding(Spacing.md)
            .background(Color.white.opacity(0.15))
            .clipShape(RoundedRectangle(cornerRadius: Radius.md))
        }
        .padding(Spacing.md)
        .frame(maxWidth: .infinity, alignment: .leading)
        .background(
            LinearGradient(colors: [AppColor.primary, AppColor.primaryContainer],
                           startPoint: .topLeading, endPoint: .bottomTrailing)
        )
        .clipShape(RoundedRectangle(cornerRadius: Radius.lg))
        .shadow(color: AppColor.primary.opacity(0.25), radius: 10, y: 5)
    }

    // MARK: Stats

    private var statsRow: some View {
        HStack(spacing: Spacing.sm) {
            statTile(icon: "star.fill", tint: AppColor.star, value: "4.8", label: "Avg rating")
            statTile(icon: "square.and.pencil", tint: AppColor.primary, value: "156", label: "Reviews")
            statTile(icon: "calendar", tint: AppColor.primary, value: "\(visits.count)", label: "Visits today")
        }
    }

    // maxWidth: .infinity on every tile = three equal columns at any width.
    private func statTile(icon: String, tint: Color, value: String, label: String) -> some View {
        VStack(spacing: 4) {
            Image(systemName: icon).foregroundStyle(tint)
            Text(value)
                .font(.system(.title2, weight: .bold))
                .foregroundStyle(AppColor.onSurface)
                .lineLimit(1)
                .minimumScaleFactor(0.7)
            Text(label)
                .font(AppFont.labelSm)
                .foregroundStyle(AppColor.onSurfaceVariant)
                .lineLimit(1)
                .minimumScaleFactor(0.8)
        }
        .frame(maxWidth: .infinity)
        .padding(.vertical, Spacing.md)
        .padding(.horizontal, Spacing.xs)
        .cardStyle(radius: Radius.md)
    }

    // MARK: Profile completeness

    private var completenessCard: some View {
        let progress = 0.8

        return VStack(alignment: .leading, spacing: Spacing.sm) {
            HStack {
                Label("Complete your profile", systemImage: "person.crop.circle.badge.checkmark")
                    .font(AppFont.labelLg)
                    .foregroundStyle(AppColor.primary)
                Spacer()
                Text("\(Int(progress * 100))%")
                    .font(AppFont.labelLg)
                    .foregroundStyle(AppColor.primary)
            }

            GeometryReader { geo in
                ZStack(alignment: .leading) {
                    Capsule().fill(AppColor.surface)
                    Capsule().fill(AppColor.primary)
                        .frame(width: geo.size.width * progress)
                }
            }
            .frame(height: 8)

            Text("Add a cover photo to help patients recognise your facility.")
                .font(AppFont.bodyMd)
                .foregroundStyle(AppColor.onSurfaceVariant)
        }
        .padding(Spacing.md)
        .frame(maxWidth: .infinity, alignment: .leading)
        .background(AppColor.primaryTint)
        .clipShape(RoundedRectangle(cornerRadius: Radius.lg))
        .overlay(RoundedRectangle(cornerRadius: Radius.lg).stroke(AppColor.outlineVariant, lineWidth: 1))
    }

    // MARK: Weekly views chart

    private var viewsCard: some View {
        VStack(alignment: .leading, spacing: Spacing.md) {
            HStack(alignment: .top) {
                VStack(alignment: .leading, spacing: 2) {
                    Text("Profile views")
                        .font(AppFont.labelLg)
                        .foregroundStyle(AppColor.onSurfaceVariant)
                    HStack(alignment: .firstTextBaseline, spacing: Spacing.sm) {
                        Text("1,284").font(.system(size: 34, weight: .bold))
                        Label("12%", systemImage: "arrow.up")
                            .font(AppFont.labelLg)
                            .foregroundStyle(AppColor.success)
                    }
                    Text("Total views this month")
                        .font(AppFont.bodyMd)
                        .foregroundStyle(AppColor.onSurfaceVariant)
                }
                Spacer()
                Image(systemName: "eye")
                    .foregroundStyle(AppColor.primary)
                    .frame(width: 40, height: 40)
                    .background(AppColor.primaryTint)
                    .clipShape(RoundedRectangle(cornerRadius: Radius.sm))
            }

            // Hand-made bar chart: each bar's height = its share of the biggest day.
            HStack(alignment: .bottom, spacing: Spacing.sm) {
                ForEach(weeklyViews) { item in
                    VStack(spacing: 6) {
                        RoundedRectangle(cornerRadius: 4)
                            .fill(item.value == peakValue ? AppColor.primary : AppColor.primaryFixed)
                            .frame(height: max(8, 90 * CGFloat(item.value) / CGFloat(peakValue)))
                        Text(item.day)
                            .font(AppFont.labelSm)
                            .foregroundStyle(AppColor.onSurfaceVariant)
                    }
                    .frame(maxWidth: .infinity)
                    .accessibilityElement(children: .ignore)
                    .accessibilityLabel("\(item.day): \(item.value) views")
                }
            }
            .frame(height: 118, alignment: .bottom)
        }
        .padding(Spacing.md)
        .frame(maxWidth: .infinity, alignment: .leading)
        .cardStyle(radius: Radius.lg)
    }

    // MARK: Quick actions

    private var quickActions: some View {
        VStack(alignment: .leading, spacing: Spacing.md) {
            Text("Quick actions")
                .font(AppFont.labelLg)
                .foregroundStyle(AppColor.onSurfaceVariant)

            // RESPONSIVE: as many 150pt tiles per row as fit. 2 on a phone, more on iPad.
            LazyVGrid(columns: [GridItem(.adaptive(minimum: 150), spacing: Spacing.md)],
                      spacing: Spacing.md) {
                actionTile(icon: "cross.case", title: "Add service",
                           subtitle: "List what you offer", filled: true, action: onAddService)
                actionTile(icon: "clock", title: "Edit hours",
                           subtitle: "Update opening times", filled: false, action: onEditHours)
                actionTile(icon: "star.bubble", title: "View reviews",
                           subtitle: "See patient feedback", filled: false, action: onViewReviews)
                actionTile(icon: "calendar", title: "Visits",
                           subtitle: "Manage appointments", filled: false, action: onViewAppointments)
            }
        }
    }

    private func actionTile(icon: String, title: String, subtitle: String,
                            filled: Bool, action: @escaping () -> Void) -> some View {
        Button(action: action) {
            VStack(alignment: .leading, spacing: Spacing.sm) {
                Image(systemName: icon)
                    .font(.title3)
                    .foregroundStyle(AppColor.primary)
                    .frame(width: 40, height: 40)
                    .background(filled ? Color.white : AppColor.primaryTint)
                    .clipShape(RoundedRectangle(cornerRadius: Radius.sm))

                VStack(alignment: .leading, spacing: 2) {
                    Text(title)
                        .font(AppFont.labelLg)
                        .foregroundStyle(filled ? Color.white : AppColor.onSurface)
                    Text(subtitle)
                        .font(AppFont.labelSm)
                        .foregroundStyle(filled ? Color.white.opacity(0.85) : AppColor.onSurfaceVariant)
                        .lineLimit(1)
                }
            }
            .padding(Spacing.md)
            .frame(maxWidth: .infinity, minHeight: 108, alignment: .leading)
            .background(filled ? AppColor.primary : AppColor.surface)
            .clipShape(RoundedRectangle(cornerRadius: Radius.lg))
            .overlay(
                RoundedRectangle(cornerRadius: Radius.lg)
                    .stroke(filled ? Color.clear : AppColor.outlineVariant, lineWidth: 1)
            )
        }
        .buttonStyle(.plain)
    }

    // MARK: Today's visits

    private var todaysVisits: some View {
        VStack(alignment: .leading, spacing: Spacing.md) {
            HStack {
                Text("Today's visits")
                    .font(AppFont.labelLg)
                    .foregroundStyle(AppColor.onSurfaceVariant)
                Spacer()
                Button("See all", action: onViewAppointments)
                    .font(AppFont.labelLg)
                    .foregroundStyle(AppColor.primary)
                    .frame(minHeight: 44)
            }

            VStack(spacing: 0) {
                ForEach(visits) { visit in
                    visitRow(visit)
                    if visit.id != visits.last?.id { Divider() }
                }
            }
            .cardStyle(radius: Radius.lg)
        }
    }

    private func visitRow(_ visit: TodayVisit) -> some View {
        HStack(spacing: Spacing.md) {
            Text(visit.time)
                .font(AppFont.labelLg)
                .foregroundStyle(AppColor.primary)
                .lineLimit(1)
                .minimumScaleFactor(0.8)
                .frame(width: 72, alignment: .leading)

            VStack(alignment: .leading, spacing: 2) {
                Text(visit.patient)
                    .font(AppFont.labelLg)
                    .foregroundStyle(AppColor.onSurface)
                Text(visit.reason)
                    .font(AppFont.bodyMd)
                    .foregroundStyle(AppColor.onSurfaceVariant)
                    .lineLimit(1)
            }

            Spacer(minLength: Spacing.sm)

            Text(visit.isConfirmed ? "Confirmed" : "Pending")
                .font(AppFont.labelSm)
                .foregroundStyle(visit.isConfirmed ? AppColor.success : AppColor.secondary)
                .padding(.horizontal, 10)
                .padding(.vertical, 4)
                .background(visit.isConfirmed ? AppColor.successContainer : AppColor.surfaceHigh)
                .clipShape(Capsule())
        }
        .padding(Spacing.md)
    }

    // MARK: Recent activity

    private var recentActivity: some View {
        VStack(alignment: .leading, spacing: Spacing.md) {
            Text("Recent activity")
                .font(AppFont.labelLg)
                .foregroundStyle(AppColor.onSurfaceVariant)

            VStack(spacing: 0) {
                activityRow(icon: "person.badge.clock", tint: AppColor.primary, bg: AppColor.primaryFixed,
                            title: "New appointment request",
                            detail: "John Doe requested a consultation", time: "2m ago")
                Divider()
                activityRow(icon: "hand.thumbsup", tint: AppColor.onSurfaceVariant, bg: AppColor.surfaceHigh,
                            title: "New 5-star review",
                            detail: "\"Dr. Mugisha is very professional…\"", time: "1h ago")
                Divider()
                activityRow(icon: "checkmark.shield.fill", tint: AppColor.success, bg: AppColor.successContainer,
                            title: "Credentials re-verified",
                            detail: "Valid for the 2024–2025 period", time: "Yesterday")
            }
            .cardStyle(radius: Radius.lg)
        }
    }

    private func activityRow(icon: String, tint: Color, bg: Color,
                             title: String, detail: String, time: String) -> some View {
        HStack(alignment: .top, spacing: Spacing.md) {
            Image(systemName: icon)
                .foregroundStyle(tint)
                .frame(width: 48, height: 48)
                .background(bg)
                .clipShape(Circle())

            VStack(alignment: .leading, spacing: 2) {
                Text(title).font(AppFont.labelLg).foregroundStyle(AppColor.onSurface)
                Text(detail).font(AppFont.bodyMd).foregroundStyle(AppColor.onSurfaceVariant)
            }

            Spacer(minLength: Spacing.sm)

            Text(time)
                .font(AppFont.labelSm)
                .foregroundStyle(AppColor.secondary)
        }
        .padding(Spacing.md)
    }
}

// MARK: - Profile tab

/// Identity, verification status, and account actions for the provider.
struct ProviderProfileTab: View {
    @Environment(AppState.self) private var appState
    @State private var showLogoutConfirm = false

    var body: some View {
        ScrollView {
            VStack(spacing: Spacing.md) {
                identityCard
                verificationCard
                menuCard
            }
            .padding(Spacing.md)
            .readableWidth(560)
        }
        .background(AppColor.background.ignoresSafeArea())
        .toolbar(.hidden, for: .navigationBar)
        .confirmationDialog("Log out of \(AppConfig.brand)?",
                            isPresented: $showLogoutConfirm,
                            titleVisibility: .visible) {
            Button("Log out", role: .destructive) { appState.logout() }
            Button("Cancel", role: .cancel) {}
        }
    }

    private var identityCard: some View {
        VStack(spacing: Spacing.sm) {
            Text("DM")
                .font(.system(size: 30, weight: .bold))
                .foregroundStyle(AppColor.primary)
                .frame(width: 88, height: 88)
                .background(AppColor.primaryFixed)
                .clipShape(Circle())
                .overlay(Circle().stroke(AppColor.surface, lineWidth: 4))
                .shadow(color: .black.opacity(0.12), radius: 6, y: 3)

            HStack(spacing: 6) {
                Text("Dr. Mugisha")
                    .font(AppFont.headlineLg)
                    .foregroundStyle(AppColor.onSurface)
                Image(systemName: "checkmark.seal.fill")
                    .foregroundStyle(AppColor.primary)
                    .accessibilityLabel("Verified")
            }

            Text("Mulago National Hospital")
                .font(AppFont.bodyLg)
                .foregroundStyle(AppColor.onSurfaceVariant)

            Label("Health provider", systemImage: "cross.case.fill")
                .font(AppFont.labelSm)
                .foregroundStyle(AppColor.primary)
                .padding(.horizontal, 10)
                .padding(.vertical, 4)
                .background(AppColor.primaryTint)
                .clipShape(Capsule())
        }
        .padding(Spacing.lg)
        .frame(maxWidth: .infinity)
        .cardStyle(radius: Radius.lg)
    }

    private var verificationCard: some View {
        HStack(spacing: Spacing.md) {
            Image(systemName: "checkmark.shield.fill")
                .font(.title2)
                .foregroundStyle(AppColor.success)
                .frame(width: 48, height: 48)
                .background(AppColor.successContainer)
                .clipShape(Circle())

            VStack(alignment: .leading, spacing: 2) {
                Text("Credentials verified")
                    .font(AppFont.labelLg)
                    .foregroundStyle(AppColor.onSurface)
                Text("Your professional credentials are valid for 2024–2025.")
                    .font(AppFont.bodyMd)
                    .foregroundStyle(AppColor.onSurfaceVariant)
            }
            Spacer(minLength: 0)
        }
        .padding(Spacing.md)
        .cardStyle(radius: Radius.lg)
    }

    private var menuCard: some View {
        VStack(spacing: 0) {
            Button { appState.completeLogin(as: .patient) } label: {
                menuRow(icon: "arrow.left.arrow.right", title: "Switch to patient app")
            }
            Divider().padding(.leading, 56)
            Button { showLogoutConfirm = true } label: {
                menuRow(icon: "rectangle.portrait.and.arrow.right", title: "Log out", isDestructive: true)
            }
        }
        .buttonStyle(.plain)
        .cardStyle(radius: Radius.lg)
    }

    private func menuRow(icon: String, title: String, isDestructive: Bool = false) -> some View {
        HStack(spacing: Spacing.md) {
            Image(systemName: icon)
                .font(.title3)
                .foregroundStyle(isDestructive ? AppColor.primary : AppColor.onSurfaceVariant)
                .frame(width: 28)
            Text(title)
                .font(AppFont.labelLg)
                .foregroundStyle(isDestructive ? AppColor.primary : AppColor.onSurface)
            Spacer()
            Image(systemName: "chevron.right")
                .font(.caption.weight(.bold))
                .foregroundStyle(AppColor.outline)
        }
        .padding(.horizontal, Spacing.md)
        .frame(minHeight: 56)
        .contentShape(Rectangle())
    }
}

#Preview {
    ProviderPortalView().environment(AppState())
}
