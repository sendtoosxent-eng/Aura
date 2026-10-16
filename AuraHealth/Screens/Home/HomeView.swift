import SwiftUI
import UIKit

// SCREEN 5, Home / Search (redesigned)
// Hero card · today's alerts · search · location · chips · providers · doctors · SOS

// LEARN: a tiny Identifiable struct lets ForEach build the doctor cards from data.
private struct PreviewDoctor: Identifiable {
    let id = UUID()
    let name: String
    let specialty: String
}

struct HomeView: View {
    @Environment(AppState.self) private var appState

    @State private var searchText = ""
    @State private var showFilters = false
    @State private var isLoading = true     // drives the skeleton loaders
    @State private var nearMeOnly = false

    private let doctors = [
        PreviewDoctor(name: "Dr. Sarah Namubiru", specialty: "Pediatrics"),
        PreviewDoctor(name: "Dr. James Okello", specialty: "General Surgery"),
        PreviewDoctor(name: "Dr. Grace Auma", specialty: "Family Medicine")
    ]

    private var results: [Provider] {
        appState.filteredProviders(search: searchText)
    }

    // MARK: Derived values for the hero card

    private var displayName: String {
        appState.userName.isEmpty ? "Dr. Sarah Namubiru" : appState.userName
    }

    private var displayHandle: String {
        appState.userHandle.isEmpty ? "s_namubiru" : appState.userHandle
    }

    private var initials: String {
        displayName.split(separator: " ")
            .filter { !$0.hasSuffix(".") }              // skip "Dr."
            .prefix(2)
            .compactMap { $0.first }
            .map(String.init)
            .joined()
    }

    private var greeting: String {
        let hour = Calendar.current.component(.hour, from: Date())
        if hour < 12 { return "Good morning" }
        if hour < 17 { return "Good afternoon" }
        return "Good evening"
    }

    var body: some View {
        VStack(spacing: 0) {
            ScrollView {
                VStack(alignment: .leading, spacing: Spacing.md) {
                    heroCard

                    sectionTitle("Today")
                    alertsRow

                    searchBar
                    locationRow
                    chipRow

                    providersHeader
                    if let discoveryError = appState.discoveryErrorMessage {
                        discoveryErrorCard(discoveryError)
                    }
                    listContent

                    doctorsPreview
                }
                .padding(Spacing.md)
                .padding(.bottom, 88)               // room for the floating SOS button
                .readableWidth()
            }
        }
        .background(AppColor.background.ignoresSafeArea())
        .toolbar(.hidden, for: .navigationBar)
        .overlay(alignment: .bottomTrailing) { sosButton }
        .sheet(isPresented: $showFilters) { FilterSheet() }
        .task {
            try? await Task.sleep(for: .milliseconds(900))
            isLoading = false
        }
    }

    // MARK: - Hero card (name, verification, details)

    private var heroCard: some View {
        VStack(alignment: .leading, spacing: Spacing.md) {
            HStack(alignment: .top, spacing: Spacing.md) {
                avatar

                VStack(alignment: .leading, spacing: 4) {
                    Text("\(greeting) 👋")
                        .font(AppFont.labelSm)
                        .foregroundStyle(Color.white.opacity(0.85))

                    HStack(spacing: 6) {
                        Text(displayName)
                            .font(AppFont.headlineLg)
                            .foregroundStyle(Color.white)
                            .lineLimit(2)
                            .fixedSize(horizontal: false, vertical: true)
                        Image(systemName: "checkmark.seal.fill")
                            .font(.subheadline)
                            .foregroundStyle(Color.white)
                            .accessibilityLabel("Verified user")
                    }

                    Text("@\(displayHandle)")
                        .font(AppFont.labelSm)
                        .foregroundStyle(Color.white.opacity(0.85))

                    Label("Identity verified", systemImage: "checkmark.shield.fill")
                        .font(AppFont.labelSm)
                        .foregroundStyle(Color.white)
                        .padding(.horizontal, 10)
                        .padding(.vertical, 4)
                        .background(Color.white.opacity(0.2), in: Capsule())
                        .padding(.top, 2)
                }

                Spacer(minLength: Spacing.xs)

                NavigationLink(value: Route.notifications) {
                    ZStack(alignment: .topTrailing) {
                        Image(systemName: "bell.fill")
                            .font(.body)
                            .foregroundStyle(AppColor.primary)
                            .frame(width: 44, height: 44)
                            .background(Color.white, in: Circle())
                        Circle().fill(AppColor.primaryContainer)
                            .frame(width: 10, height: 10)
                            .overlay(Circle().stroke(Color.white, lineWidth: 2))
                            .offset(x: 1, y: 1)
                    }
                }
                .accessibilityLabel("Notifications")
            }

            detailsPanel
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

    /// Signed-in profile photo, then the bundled placeholder, then initials.
    /// Green dot = active.
    private var avatar: some View {
        ZStack(alignment: .bottomTrailing) {
            Group {
                if let data = appState.profilePhotoData, let image = UIImage(data: data) {
                    Image(uiImage: image)
                        .resizable()
                        .scaledToFill()
                } else if UIImage(named: "WelcomeCare") != nil {
                    Image("WelcomeCare")
                        .resizable()
                        .scaledToFill()
                } else {
                    Text(initials)
                        .font(.system(.title3, weight: .bold))
                        .foregroundStyle(AppColor.primary)
                        .frame(maxWidth: .infinity, maxHeight: .infinity)
                        .background(Color.white)
                }
            }
            .frame(width: 64, height: 64)
            .clipShape(Circle())
            .overlay(Circle().stroke(Color.white, lineWidth: 3))

            Circle()
                .fill(AppColor.success)
                .frame(width: 14, height: 14)
                .overlay(Circle().stroke(Color.white, lineWidth: 2))
        }
    }

    /// Three equal columns separated by thin lines. Demo values except Health ID.
    private var detailsPanel: some View {
        HStack(spacing: 0) {
            heroDetail(icon: "shield.lefthalf.filled", label: "Health ID", value: appState.healthID)
            heroDivider
            heroDetail(icon: "drop.fill", label: "Blood group", value: "O+")
            heroDivider
            heroDetail(icon: "clock.arrow.circlepath", label: "Last visit", value: "3 days ago")
        }
        .padding(.vertical, Spacing.sm)
        .background(Color.white.opacity(0.15))
        .clipShape(RoundedRectangle(cornerRadius: Radius.md))
    }

    private func heroDetail(icon: String, label: String, value: String) -> some View {
        VStack(spacing: 3) {
            Image(systemName: icon)
                .font(.caption)
                .foregroundStyle(Color.white.opacity(0.85))
            Text(value)
                .font(AppFont.labelLg)
                .foregroundStyle(Color.white)
                .lineLimit(1)
                .minimumScaleFactor(0.75)
            Text(label)
                .font(AppFont.labelSm)
                .foregroundStyle(Color.white.opacity(0.8))
                .lineLimit(1)
        }
        .frame(maxWidth: .infinity)
        .accessibilityElement(children: .combine)
    }

    private var heroDivider: some View {
        Rectangle()
            .fill(Color.white.opacity(0.25))
            .frame(width: 1, height: 36)
    }

    // MARK: - Section helpers

    private func sectionTitle(_ text: String) -> some View {
        Text(text)
            .font(AppFont.headlineMd)
            .foregroundStyle(AppColor.onSurface)
            .padding(.top, Spacing.xs)
    }

    private var providersHeader: some View {
        HStack {
            sectionTitle("Nearby providers")
            Spacer()
            if !isLoading {
                Text("\(results.count) found")
                    .font(AppFont.labelSm)
                    .foregroundStyle(AppColor.onSurfaceVariant)
            }
        }
    }

    private func discoveryErrorCard(_ message: String) -> some View {
        HStack(alignment: .top, spacing: Spacing.sm) {
            Image(systemName: "wifi.exclamationmark")
                .foregroundStyle(AppColor.primary)
            VStack(alignment: .leading, spacing: 4) {
                Text("Live provider data is unavailable")
                    .font(AppFont.labelLg)
                Text(message)
                    .font(AppFont.bodyMd)
                    .foregroundStyle(AppColor.onSurfaceVariant)
                Button("Retry") {
                    Task { await appState.loadDiscoveryIfNeeded() }
                }
                .font(AppFont.labelLg)
                .foregroundStyle(AppColor.primary)
            }
        }
        .padding(Spacing.md)
        .frame(maxWidth: .infinity, alignment: .leading)
        .background(AppColor.primaryTint, in: RoundedRectangle(cornerRadius: Radius.md))
    }

    // MARK: - Alerts

    private var alertsRow: some View {
        ScrollView(.horizontal, showsIndicators: false) {
            HStack(spacing: Spacing.sm) {
                NavigationLink(value: Route.appointments) {
                    homeAlertLabel(icon: "calendar.badge.clock", tint: AppColor.primary,
                                   title: "Upcoming appointment", detail: "Dr. Namubiru · Tomorrow, 2:30 PM",
                                   action: "View details")
                }
                .buttonStyle(.plain)

                Button { appState.showEmergency = true } label: {
                    homeAlertLabel(icon: "location.circle.fill", tint: AppColor.success,
                                   title: "Emergency tracking", detail: "No active emergency request",
                                   action: "Open Emergency")
                }
                .buttonStyle(.plain)

                NavigationLink(value: Route.reminders) {
                    homeAlertLabel(icon: "pills.fill", tint: AppColor.star,
                                   title: "Care reminder", detail: "Refill your prescription this week",
                                   action: "Manage reminders")
                }
                .buttonStyle(.plain)
            }
        }
        .scrollClipDisabled()
        .accessibilityLabel("AfyaLink alerts and reminders")
    }

    private func homeAlertLabel(icon: String, tint: Color, title: String, detail: String, action: String) -> some View {
        VStack(alignment: .leading, spacing: 8) {
            HStack(spacing: 8) {
                Image(systemName: icon)
                    .foregroundStyle(tint)
                Text(title)
                    .font(AppFont.labelLg)
                    .foregroundStyle(AppColor.onSurface)
            }
            Text(detail)
                .font(AppFont.bodyMd)
                .foregroundStyle(AppColor.onSurfaceVariant)
                .multilineTextAlignment(.leading)
                .lineLimit(2)
            Text(action)
                .font(AppFont.labelSm)
                .foregroundStyle(tint)
        }
        .padding(14)
        .frame(width: 235, height: 124, alignment: .leading)
        .background(AppColor.surface, in: RoundedRectangle(cornerRadius: Radius.lg))
        .overlay(RoundedRectangle(cornerRadius: Radius.lg).stroke(AppColor.outlineVariant, lineWidth: 1))
    }

    // MARK: - Search bar

    private var searchBar: some View {
        HStack(spacing: Spacing.sm) {
            Image(systemName: "magnifyingglass")
                .font(.body.weight(.medium))
                .foregroundStyle(AppColor.primary)

            TextField("Search malaria, antenatal care, providers…", text: $searchText)
                .font(AppFont.bodyLg)
                .submitLabel(.search)
                .autocorrectionDisabled()

            if !searchText.isEmpty {
                Button { searchText = "" } label: {
                    Image(systemName: "xmark.circle.fill")
                        .foregroundStyle(AppColor.outline)
                }
            }

            Button { showFilters = true } label: {
                Image(systemName: "slider.horizontal.3")
                    .font(.body.weight(.semibold))
                    .foregroundStyle(AppColor.primary)
                    .frame(width: 44, height: 44)
                    .background(AppColor.primary.opacity(0.08), in: RoundedRectangle(cornerRadius: Radius.sm))
                    .overlay(alignment: .topTrailing) {
                        if appState.filters.activeCount > 0 {
                            Text("\(appState.filters.activeCount)")
                                .font(.caption2.bold())
                                .foregroundStyle(.white)
                                .frame(width: 18, height: 18)
                                .background(AppColor.primary)
                                .clipShape(Circle())
                                .offset(x: 4, y: -4)
                        }
                    }
            }
            .accessibilityLabel("Filters")
        }
        .padding(.leading, Spacing.md)
        .padding(.trailing, Spacing.xs)
        .frame(minHeight: 52)
        .background(AppColor.surface)
        .clipShape(RoundedRectangle(cornerRadius: Radius.md))
        .overlay(RoundedRectangle(cornerRadius: Radius.md).stroke(AppColor.outlineVariant, lineWidth: 1))
        .shadow(color: .black.opacity(0.04), radius: 8, x: 0, y: 4)
    }

    // MARK: - Location row

    private var locationRow: some View {
        HStack(spacing: Spacing.sm) {
            NavigationLink(value: Route.locationPicker) {
                HStack(spacing: 6) {
                    Image(systemName: "mappin.and.ellipse")
                        .foregroundStyle(AppColor.primary)
                        .font(.subheadline.weight(.semibold))
                    VStack(alignment: .leading, spacing: 0) {
                        Text("CURRENT LOCATION")
                            .font(.system(size: 9, weight: .bold))
                            .foregroundStyle(AppColor.outline)
                        HStack(spacing: 4) {
                            Text(appState.selectedCareLocation.detail)
                                .font(AppFont.labelLg)
                                .foregroundStyle(AppColor.onSurface)
                                .lineLimit(1)
                            Image(systemName: "chevron.down")
                                .font(.caption2.weight(.bold))
                                .foregroundStyle(AppColor.onSurfaceVariant)
                        }
                    }
                }
            }
            .buttonStyle(.plain)

            Spacer(minLength: 4)

            Button {
                nearMeOnly.toggle()
                appState.filters.radiusKm = nearMeOnly ? 10 : 50
            } label: {
                Label("Near Me", systemImage: nearMeOnly ? "location.fill" : "location")
                    .font(AppFont.labelSm)
                    .foregroundStyle(nearMeOnly ? Color.white : AppColor.primary)
                    .padding(.horizontal, 11)
                    .frame(minHeight: 38)
                    .background(nearMeOnly ? AppColor.primary : AppColor.primaryTint, in: Capsule())
                    .overlay(Capsule().stroke(AppColor.primary.opacity(0.25), lineWidth: 1))
            }
            .buttonStyle(.plain)
            .accessibilityLabel("Show selected services near me")
            .accessibilityValue(nearMeOnly ? "On" : "Off")
        }
        .padding(.horizontal, 14)
        .padding(.vertical, 8)
        .background(AppColor.surface, in: RoundedRectangle(cornerRadius: Radius.md))
        .overlay(
            RoundedRectangle(cornerRadius: Radius.md)
                .stroke(AppColor.outlineVariant.opacity(0.6), lineWidth: 1)
        )
    }

    // MARK: - Filter chips

    private var chipRow: some View {
        ScrollView(.horizontal, showsIndicators: false) {
            HStack(spacing: Spacing.sm) {
                FilterChip(title: "Malaria", icon: CareService.malaria.icon,
                           isSelected: appState.filters.careServices.contains(.malaria)) {
                    appState.filters.toggle(.malaria)
                }

                FilterChip(title: "Antenatal", icon: CareService.antenatal.icon,
                           isSelected: appState.filters.careServices.contains(.antenatal)) {
                    appState.filters.toggle(.antenatal)
                }

                FilterChip(title: "Family Planning", icon: CareService.familyPlanning.icon,
                           isSelected: appState.filters.careServices.contains(.familyPlanning)) {
                    appState.filters.toggle(.familyPlanning)
                }

                FilterChip(title: "Open Now", icon: "clock",
                           isSelected: appState.filters.openNow) {
                    appState.filters.openNow.toggle()
                }

                FilterChip(title: "24hrs", icon: "clock.badge.checkmark",
                           isSelected: appState.filters.open24h) {
                    appState.filters.open24h.toggle()
                }

                FilterChip(title: "Pharmacy", icon: "pills.fill",
                           isSelected: appState.filters.types.contains(.pharmacy)) {
                    appState.filters.toggle(.pharmacy)
                }

                FilterChip(title: "Hospitals", icon: "cross.case.fill",
                           isSelected: appState.filters.types.contains(.hospital)) {
                    appState.filters.toggle(.hospital)
                }

                FilterChip(title: "Clinics", icon: "stethoscope",
                           isSelected: appState.filters.types.contains(.clinic)) {
                    appState.filters.toggle(.clinic)
                }

                FilterChip(title: "Lab Tests", icon: "testtube.2",
                           isSelected: appState.filters.types.contains(.lab)) {
                    appState.filters.toggle(.lab)
                }

                FilterChip(title: "Ambulance", icon: "cross.vial.fill",
                           isSelected: appState.filters.types.contains(.ambulance)) {
                    appState.filters.toggle(.ambulance)
                }
            }
        }
        .scrollClipDisabled()
    }

    // MARK: - Provider list

    @ViewBuilder
    private var listContent: some View {
        if isLoading {
            ForEach(0..<3, id: \.self) { _ in ProviderRowSkeleton() }
        } else if results.isEmpty {
            emptyState
        } else {
            if let first = results.first {
                NavigationLink(value: Route.provider(first)) {
                    FeaturedProviderCard(provider: first)
                }
                .buttonStyle(.plain)
            }

            ForEach(results.dropFirst()) { provider in
                NavigationLink(value: Route.provider(provider)) {
                    ProviderRow(provider: provider)
                }
                .buttonStyle(.plain)
            }
        }
    }

    private var emptyState: some View {
        VStack(spacing: Spacing.sm) {
            Image(systemName: "magnifyingglass")
                .font(.largeTitle)
                .foregroundStyle(AppColor.outline)
            Text("No providers found")
                .font(AppFont.headlineMd)
            Text("Try changing your search or filters.")
                .font(AppFont.bodyMd)
                .foregroundStyle(AppColor.onSurfaceVariant)
            Button("Clear filters") {
                appState.filters = ProviderFilters()
                searchText = ""
            }
            .foregroundStyle(AppColor.primary)
            .frame(minHeight: 44)
        }
        .frame(maxWidth: .infinity)
        .padding(.vertical, Spacing.xl)
    }

    // MARK: - Available doctors

    private var doctorsPreview: some View {
        VStack(alignment: .leading, spacing: Spacing.sm) {
            HStack {
                sectionTitle("Available doctors")
                Spacer()
                NavigationLink(value: Route.appointments) {
                    Text("View appointments")
                        .font(AppFont.labelSm)
                        .foregroundStyle(AppColor.primary)
                        .frame(minHeight: 44)
                }
            }

            ScrollView(.horizontal, showsIndicators: false) {
                HStack(spacing: Spacing.sm) {
                    ForEach(doctors) { doctor in
                        NavigationLink(value: Route.chat(doctor.name)) {
                            doctorCard(doctor)
                        }
                        .buttonStyle(.plain)
                    }
                }
            }
            .scrollClipDisabled()
        }
    }

    private func doctorCard(_ doctor: PreviewDoctor) -> some View {
        VStack(alignment: .leading, spacing: 6) {
            ZStack(alignment: .bottomTrailing) {
                Image("WelcomeCare")
                    .resizable()
                    .scaledToFill()
                    .frame(width: 56, height: 56)
                    .background(AppColor.primaryFixed)
                    .clipShape(Circle())

                Circle()
                    .fill(AppColor.success)
                    .frame(width: 12, height: 12)
                    .overlay(Circle().stroke(AppColor.surface, lineWidth: 2))
            }

            HStack(spacing: 4) {
                Text(doctor.name)
                    .font(AppFont.labelLg)
                    .foregroundStyle(AppColor.onSurface)
                    .lineLimit(1)
                Image(systemName: "checkmark.seal.fill")
                    .font(.caption2)
                    .foregroundStyle(AppColor.primary)
                    .accessibilityLabel("Verified")
            }

            Text(doctor.specialty)
                .font(AppFont.labelSm)
                .foregroundStyle(AppColor.primary)

            Text("Available today")
                .font(AppFont.labelSm)
                .foregroundStyle(AppColor.success)
        }
        .frame(width: 160, alignment: .leading)
        .padding(Spacing.sm)
        .background(AppColor.surface)
        .clipShape(RoundedRectangle(cornerRadius: Radius.md))
        .overlay(RoundedRectangle(cornerRadius: Radius.md).stroke(AppColor.outlineVariant, lineWidth: 1))
    }

    // MARK: - Floating Emergency SOS Button

    private var sosButton: some View {
        Button {
            appState.showEmergency = true
        } label: {
            VStack(spacing: 2) {
                Image(systemName: "light.beacon.max.fill")
                    .font(.title3)
                Text("SOS")
                    .font(.caption.weight(.heavy))
            }
            .foregroundStyle(.white)
            .frame(width: 64, height: 64)
            .background(AppColor.primaryContainer)
            .clipShape(Circle())
            .background(PulseRing().frame(width: 64, height: 64))
            .shadow(color: .black.opacity(0.25), radius: 8, y: 4)
        }
        .padding(Spacing.md)
        .accessibilityLabel("Emergency SOS")
    }
}

#Preview {
    NavigationStack { HomeView().appDestinations() }
        .environment(AppState())
}
