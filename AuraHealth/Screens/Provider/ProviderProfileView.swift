import SwiftUI
import MapKit
import UIKit

// MARK: - Image from the asset catalog, with a safe fallback

/// Shows Assets.xcassets/<name> if it exists, otherwise a tinted placeholder.
/// Callers give it a size and clip shape, e.g. .frame(...).clipShape(Circle()).
private struct AssetPhoto: View {
    let name: String
    var symbol: String = "photo"

    var body: some View {
        if let uiImage = UIImage(named: name) {       // nil when the asset doesn't exist
            Image(uiImage: uiImage)
                .resizable()
                .scaledToFill()
        } else {
            ZStack {
                LinearGradient(colors: [AppColor.primaryFixed, AppColor.surfaceHigh],
                               startPoint: .topLeading, endPoint: .bottomTrailing)
                Image(systemName: symbol)
                    .font(.title)
                    .foregroundStyle(AppColor.primary.opacity(0.7))
            }
        }
    }
}

// SCREEN 15, Provider profile (redesigned)
// Cover + avatar · name · stat tiles · pinned tabs (Services / Doctors / Hours / Reviews) · action bar
struct ProviderProfileView: View {
    let provider: Provider

    @Environment(AppState.self) private var appState
    @State private var tab: ProfileTab = .services

    // Names of images in Assets.xcassets. Missing ones fall back to a placeholder.
    private let avatarImageName = "WelcomeCare"
    private let avatarSize: CGFloat = 88

    private var doctors: [CareDoctor] { appState.doctors(at: provider) }

    enum ProfileTab: String, CaseIterable, Identifiable {
        case services = "Services"
        case doctors = "Doctors"
        case hours = "Hours"
        case reviews = "Reviews"
        var id: String { rawValue }
    }

    var body: some View {
        ScrollView {
            // LEARN: pinnedViews makes a Section's HEADER stick to the top while
            // its content scrolls. Here the header is the tab bar.
            LazyVStack(alignment: .leading, spacing: 0, pinnedViews:[]) {
                heroHeader
                VStack(alignment: .leading, spacing: Spacing.md) {
                    
                    identity
                    statsRow
                }
                .padding(.horizontal, Spacing.md)
                .padding(.top, Spacing.sm)
                .padding(.bottom, Spacing.md)

                Section {
                    tabContent
                        .padding(Spacing.md)
                        .id(tab)                    // new identity per tab → fades
                        .transition(.opacity)
                } header: {
                    tabBar
                }
            }
            .readableWidth()
        }
        .ignoresSafeArea(edges:  .top)
        .animation(.easeInOut(duration: 0.2), value: tab)
        .background(AppColor.background.ignoresSafeArea())
        .navigationBarTitleDisplayMode(.inline)
        .toolbarColorScheme(.dark, for: .navigationBar)    // white back arrow, icons and status bar
        .tint(.white)        .toolbar {
            ToolbarItemGroup(placement: .topBarTrailing) {
                NavigationLink(value: Route.chat(provider.name)) {
                    Image(systemName: "message.fill")
                }
                .accessibilityLabel("Message \(provider.name)")
                ShareLink(item: "\(provider.name) — \(provider.address)") {
                    Image(systemName: "square.and.arrow.up")
                }
                Button { appState.toggleFavorite(provider) } label: {
                    Image(systemName: appState.isFavorite(provider) ? "heart.fill" : "heart")
                }
                .accessibilityLabel(appState.isFavorite(provider) ? "Remove from favorites" : "Add to favorites")
            }
        }
        // Pinned footer. The scroll content stops above it automatically,
        // so no manual "padding(.bottom, 96)" is needed.
        .safeAreaInset(edge: .bottom, spacing: 0) { actionBar }
    }

    // MARK: - Hero: cover card + overlapping avatar

    private var heroHeader: some View {
        ZStack(alignment: .bottomLeading) {
            Color.clear
                .frame(height: 260)                                   // was 160: taller, since it now includes the status-bar area
                .overlay { ProviderThumbnail(provider: provider, cornerRadius: 0, iconSize: 64) }
                .overlay {                                            // dark fade at the BOTTOM
                    LinearGradient(colors: [.clear, .black.opacity(0.35)],
                                   startPoint: .center, endPoint: .bottom)
                }
                .overlay {                                            // dark fade at the TOP so the back/share/heart icons stay readable
                    LinearGradient(colors: [.black.opacity(0.5), .clear],
                                   startPoint: .top, endPoint: .center)
                }
                .clipped()                                            // was clipShape(RoundedRectangle…): square corners, flush to the edges

            avatar
                .offset(x: Spacing.md, y: avatarSize / 2)
        }
        .padding(.bottom, avatarSize / 2)
        .overlay(alignment: .bottomTrailing) {
            HStack(spacing: Spacing.sm) {
                typeChip
                StatusBadge(isOpen: provider.isOpenNow)
            }
            .padding(.horizontal, Spacing.md)                         // NEW: chips no longer touch the screen edge
            .padding(.bottom, Spacing.xs)
        }
    }

    private var avatar: some View {
        Color.clear
            .frame(width: avatarSize, height: avatarSize)
            .overlay { AssetPhoto(name: avatarImageName, symbol: provider.type.icon) }
            .clipShape(Circle())
            .overlay(Circle().stroke(AppColor.background, lineWidth: 4))   // ring matches the page
            .shadow(color: .black.opacity(0.15), radius: 6, y: 3)
    }

    private var typeChip: some View {
        Label(provider.type.rawValue, systemImage: provider.type.icon)
            .font(AppFont.labelSm)
            .foregroundStyle(AppColor.primary)
            .padding(.horizontal, 10)
            .padding(.vertical, 4)
            .background(AppColor.primaryTint)
            .clipShape(Capsule())
    }

    // MARK: - Identity + stat tiles

    private var identity: some View {
        VStack(alignment: .leading, spacing: 6) {
            HStack(alignment: .firstTextBaseline, spacing: 6) {
                Text(provider.name)
                    .font(AppFont.headlineLg)
                    .foregroundStyle(AppColor.onSurface)
                    .multilineTextAlignment(.leading)
                if provider.isVerified {
                    Image(systemName: "checkmark.seal.fill")
                        .foregroundStyle(AppColor.primary)
                        .accessibilityLabel("Verified")
                }
            }
            Label(provider.address, systemImage: "mappin.and.ellipse")
                .font(AppFont.bodyLg)
                .foregroundStyle(AppColor.onSurfaceVariant)
        }
    }

    private var statsRow: some View {
        HStack(spacing: Spacing.sm) {
            statTile(icon: "star.fill", tint: AppColor.star,
                     value: String(format: "%.1f", provider.rating),
                     caption: "\(provider.reviewCountText) reviews")

            statTile(icon: "location.fill", tint: AppColor.primary,
                     value: provider.distanceText,
                     caption: "~\(provider.etaMinutes) min drive")

            statTile(icon: "clock.fill",
                     tint: provider.isOpenNow ? AppColor.success : AppColor.secondary,
                     value: provider.isOpenNow ? "Open" : "Closed",
                     caption: provider.is24Hours ? "24 hours" : (provider.closesAt.map { "Until \($0)" } ?? "Today"))
        }
    }

    /// maxWidth: .infinity on each tile in an HStack = three equal columns at any width.
    private func statTile(icon: String, tint: Color, value: String, caption: String) -> some View {
        VStack(spacing: 4) {
            Image(systemName: icon).foregroundStyle(tint)
            Text(value)
                .font(AppFont.headlineMd)
                .foregroundStyle(AppColor.onSurface)
                .lineLimit(1)
                .minimumScaleFactor(0.7)
            Text(caption)
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

    // MARK: - Tabs

    private var tabBar: some View {
        HStack(spacing: 0) {
            ForEach(ProfileTab.allCases) { t in
                Button { tab = t } label: {
                    VStack(spacing: 6) {
                        Text(t.rawValue)
                            .font(AppFont.labelLg)
                            .foregroundStyle(tab == t ? AppColor.primary : AppColor.onSurfaceVariant)
                            .lineLimit(1)
                            .minimumScaleFactor(0.8)
                            .frame(maxWidth: .infinity, minHeight: 40)
                        Rectangle()
                            .fill(tab == t ? AppColor.primary : Color.clear)
                            .frame(height: 3)
                    }
                }
                .buttonStyle(.plain)
            }
        }
        .padding(.horizontal, Spacing.md)
        .background(AppColor.background)          // opaque, so content doesn't show through when pinned
        .overlay(alignment: .bottom) { Divider() }
    }

    @ViewBuilder
    private var tabContent: some View {
        switch tab {
        case .services: servicesSection
        case .doctors:  doctorsSection
        case .hours:    hoursSection
        case .reviews:  reviewsSection
        }
    }

    private func sectionTitle(_ text: String) -> some View {
        Text(text).font(AppFont.headlineMd).foregroundStyle(AppColor.onSurface)
    }

    // MARK: - Services

    private var servicesSection: some View {
        VStack(alignment: .leading, spacing: Spacing.md) {
            sectionTitle("Available Services")

            // RESPONSIVE: as many 150pt-wide tiles per row as the screen fits.
            LazyVGrid(columns: [GridItem(.adaptive(minimum: 150), spacing: Spacing.md)],
                      spacing: Spacing.md) {
                ForEach(provider.services, id: \.self) { service in
                    HStack(spacing: Spacing.sm) {
                        Image(systemName: icon(for: service))
                            .foregroundStyle(AppColor.primary)
                            .frame(width: 36, height: 36)
                            .background(AppColor.primaryTint)
                            .clipShape(RoundedRectangle(cornerRadius: Radius.sm))
                        Text(service)
                            .font(AppFont.labelLg)
                            .foregroundStyle(AppColor.onSurface)
                            .lineLimit(2)
                        Spacer(minLength: 0)
                    }
                    .padding(Spacing.sm)
                    .frame(maxWidth: .infinity, minHeight: 56, alignment: .leading)
                    .cardStyle(radius: Radius.md)
                }
            }
        }
    }

    private func icon(for service: String) -> String {
        if let careService = CareService.allCases.first(where: {
            service.localizedCaseInsensitiveContains($0.rawValue)
        }) {
            return careService.icon
        }
        let s = service.lowercased()
        if s.contains("er") && s.contains("24") { return "light.beacon.max" }
        if s.contains("radio") { return "waveform.path.ecg" }
        if s.contains("lab") || s.contains("patho") { return "testtube.2" }
        if s.contains("pharm") || s.contains("prescri") { return "pills" }
        if s.contains("pedi") || s.contains("child") { return "figure.and.child.holdinghands" }
        if s.contains("mater") { return "figure.2.and.child.holdinghands" }
        if s.contains("dental") || s.contains("ortho") { return "mouth" }
        if s.contains("eye") || s.contains("glass") || s.contains("lens") { return "eye" }
        return "cross.case"
    }

    // MARK: - Doctors

    private var doctorsSection: some View {
        VStack(alignment: .leading, spacing: Spacing.md) {
            HStack {
                sectionTitle("Doctors & Specialists")
                Spacer()
                Text("\(doctors.filter(\.isAvailableNow).count) available today")
                    .font(AppFont.labelSm)
                    .foregroundStyle(AppColor.success)
            }

            ForEach(doctors) { doctor in
                doctorCard(doctor)
            }
        }
    }

    private func doctorCard(_ doctor: CareDoctor) -> some View {
        HStack(alignment: .top, spacing: Spacing.md) {
            Color.clear
                .frame(width: 56, height: 56)
                .overlay { AssetPhoto(name: doctor.imageName, symbol: "person.fill") }
                .clipShape(Circle())
                .overlay(Circle().stroke(AppColor.outlineVariant, lineWidth: 1))

            VStack(alignment: .leading, spacing: 4) {
                HStack(alignment: .top) {
                    Text(doctor.name)
                        .font(AppFont.labelLg)
                        .foregroundStyle(AppColor.onSurface)
                    Spacer(minLength: Spacing.xs)
                    HStack(spacing: 2) {
                        Image(systemName: "star.fill").font(.caption2).foregroundStyle(AppColor.star)
                        Text(String(format: "%.1f", doctor.rating)).font(.caption.weight(.bold))
                    }
                }

                Text(doctor.specialty)
                    .font(AppFont.bodyMd)
                    .foregroundStyle(AppColor.primary)
                    .lineLimit(2)

                HStack(spacing: 6) {
                    Circle()
                        .fill(doctor.isAvailableNow ? AppColor.success : AppColor.secondary)
                        .frame(width: 8, height: 8)
                    Text(doctor.isAvailableNow ? "Available" : "Unavailable")
                        .fontWeight(.semibold)
                        .foregroundStyle(doctor.isAvailableNow ? AppColor.success : AppColor.secondary)
                    Text("• \(doctor.availability)")
                        .foregroundStyle(AppColor.secondary)
                }
                .font(AppFont.labelSm)

                HStack(spacing: Spacing.md) {
                    NavigationLink(value: Route.chat(doctor.name)) {
                        Label("Message", systemImage: "message.fill")
                            .font(AppFont.labelSm)
                            .foregroundStyle(AppColor.primary)
                            .frame(minHeight: 36)
                    }
                    NavigationLink(value: Route.bookDoctor(doctor)) {
                        Label("Book", systemImage: "calendar.badge.plus")
                            .font(AppFont.labelSm)
                            .foregroundStyle(.white)
                            .padding(.horizontal, Spacing.sm)
                            .frame(minHeight: 36)
                            .background(AppColor.primary, in: Capsule())
                    }
                }
            }
        }
        .padding(Spacing.md)
        .frame(maxWidth: .infinity, alignment: .leading)
        .cardStyle(radius: Radius.md)
    }

    // MARK: - Hours

    private var hoursSection: some View {
        let symbols = Calendar.current.weekdaySymbols          // ["Sunday", "Monday", …]
        let todayIndex = Calendar.current.component(.weekday, from: Date()) - 1
        let order = Array(1...6) + [0]                         // Monday … Saturday, then Sunday

        return VStack(alignment: .leading, spacing: Spacing.md) {
            HStack {
                sectionTitle("Operating Hours")
                Spacer()
                StatusBadge(isOpen: provider.isOpenNow, text: provider.statusText)
            }

            VStack(spacing: 0) {
                ForEach(order, id: \.self) { i in
                    let isToday = i == todayIndex
                    HStack {
                        Text(isToday ? "\(symbols[i]) (Today)" : symbols[i])
                            .fontWeight(isToday ? .bold : .regular)
                        Spacer()
                        Text(hoursText(forWeekdayIndex: i))
                            .fontWeight(isToday ? .bold : .regular)
                            .foregroundStyle(isToday ? AppColor.primary : AppColor.onSurfaceVariant)
                    }
                    .font(AppFont.bodyLg)
                    .padding(Spacing.md)
                    .background(isToday ? AppColor.primaryTint : Color.clear)

                    if i != order.last { Divider() }
                }
            }
            .cardStyle(radius: Radius.md)
        }
    }

    private func hoursText(forWeekdayIndex i: Int) -> String {
        if provider.is24Hours { return "Open 24 Hours" }
        if i == 0 { return "Closed" }                          // Sunday
        return "8:00 AM – \(provider.closesAt ?? "6PM")"
    }

    // MARK: - Reviews

    private var reviewsSection: some View {
        VStack(alignment: .leading, spacing: Spacing.md) {
            HStack {
                sectionTitle("Reviews")
                Spacer()
                NavigationLink(value: Route.writeReview(provider)) {
                    Label("Write a Review", systemImage: "square.and.pencil")
                        .font(AppFont.labelLg)
                        .foregroundStyle(AppColor.primary)
                        .frame(minHeight: 44)
                }
            }

            HStack(spacing: Spacing.lg) {
                VStack(spacing: 4) {
                    Text(String(format: "%.1f", provider.rating))
                        .font(.system(size: 44, weight: .bold))
                        .foregroundStyle(AppColor.onSurface)
                    StarRow(rating: provider.rating, size: 14)
                    Text("\(provider.reviewCount) ratings")
                        .font(AppFont.labelSm)
                        .foregroundStyle(AppColor.secondary)
                }

                VStack(spacing: 6) {
                    ForEach(Array(zip([5, 4, 3, 2, 1], [0.78, 0.15, 0.04, 0.02, 0.01])), id: \.0) { star, share in
                        HStack(spacing: 6) {
                            Text("\(star)").font(AppFont.labelSm).frame(width: 12)
                            GeometryReader { geo in
                                ZStack(alignment: .leading) {
                                    Capsule().fill(AppColor.surfaceHigh)
                                    Capsule().fill(AppColor.primary)
                                        .frame(width: geo.size.width * share)
                                }
                            }
                            .frame(height: 6)
                        }
                    }
                }
            }
            .padding(Spacing.md)
            .cardStyle(radius: Radius.md)

            ForEach(MockData.publicReviews) { review in
                VStack(alignment: .leading, spacing: Spacing.sm) {
                    HStack(spacing: Spacing.sm) {
                        Text(review.initials)
                            .font(AppFont.labelLg)
                            .foregroundStyle(AppColor.primary)
                            .frame(width: 40, height: 40)
                            .background(AppColor.primaryFixed)
                            .clipShape(Circle())

                        VStack(alignment: .leading, spacing: 0) {
                            Text(review.author).font(AppFont.labelLg)
                            Text(review.timeAgo).font(AppFont.labelSm).foregroundStyle(AppColor.secondary)
                        }
                        Spacer()
                        StarRow(rating: Double(review.rating), size: 12)
                    }
                    Text(review.text)
                        .font(AppFont.bodyMd)
                        .foregroundStyle(AppColor.onSurfaceVariant)
                }
                .padding(Spacing.md)
                .frame(maxWidth: .infinity, alignment: .leading)
                .cardStyle(radius: Radius.md)
            }
        }
    }

    // MARK: - Bottom action bar

    private var actionBar: some View {
        HStack(spacing: Spacing.md) {
            SecondaryButton(title: "Directions", icon: "arrow.triangle.turn.up.right.diamond") {
                openDirections()
            }
            NavigationLink(value: Route.bookAppointment(provider)) {
                Label("Book", systemImage: "calendar.badge.plus")
                    .font(AppFont.labelLg)
                    .foregroundStyle(.white)
                    .frame(maxWidth: .infinity, minHeight: 52)
                    .background(AppColor.primary, in: RoundedRectangle(cornerRadius: Radius.sm))
            }
            CallCircleButton(phone: provider.phone, filled: true)
        }
        .padding(.horizontal, Spacing.md)
        .padding(.top, Spacing.md)
        .padding(.bottom, Spacing.sm)
        .readableWidth()
        .background(
            AppColor.surface
                .shadow(color: .black.opacity(0.08), radius: 8, y: -2)
                .ignoresSafeArea(edges: .bottom)
        )
    }

    private func openDirections() {
        let item = MKMapItem(placemark: MKPlacemark(coordinate: provider.coordinate))
        item.name = provider.name
        item.openInMaps(launchOptions: [MKLaunchOptionsDirectionsModeKey: MKLaunchOptionsDirectionsModeDriving])
    }
}

#Preview {
    NavigationStack { ProviderProfileView(provider: MockData.providers[0]).appDestinations() }
        .environment(AppState())
}
