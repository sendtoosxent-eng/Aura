import SwiftUI
import MapKit

// SCREEN 7 — Map view (design: map_view)
// Apple Maps with a red pin per provider, floating buttons, and a bottom panel.
struct ProvidersMapView: View {
    @Environment(AppState.self) private var appState

    private enum MapSelection: Hashable {
        case provider(UUID)
        case doctor(UUID)
    }

    // LEARN: Since iOS 17, Map is declarative like the rest of SwiftUI.
    // `MapCameraPosition` describes where the camera looks; we can change it
    // in code (the "locate me" button) and the map animates there.
    @State private var position: MapCameraPosition = .region(Self.kampalaRegion)
    @State private var selectedItem: MapSelection?
    @State private var showFilters = false

    static let kampalaRegion = MKCoordinateRegion(
        center: AppConfig.kampala,
        span: MKCoordinateSpan(latitudeDelta: 0.12, longitudeDelta: 0.12)
    )

    private var providers: [Provider] { appState.filteredProviders() }
    private var doctors: [CareDoctor] {
        let visibleProviderIDs = Set(providers.map(\.id))
        return appState.allDoctors.filter { visibleProviderIDs.contains($0.provider.id) }
    }
    private var selectedProvider: Provider? {
        guard case .provider(let id) = selectedItem else { return nil }
        return providers.first { $0.id == id }
    }
    private var selectedDoctor: CareDoctor? {
        guard case .doctor(let id) = selectedItem else { return nil }
        return doctors.first { $0.id == id }
    }

    var body: some View {
        VStack(spacing: 0) {
            AppHeader(title: AppConfig.fullName)

            ZStack(alignment: .bottom) {
                // `selection:` tells the map which pin the user tapped.
                // Each Marker needs a matching `.tag(...)`.
                Map(position: $position, selection: $selectedItem) {
                    ForEach(providers) { provider in
                        Marker(provider.name,
                               systemImage: provider.type.icon,
                               coordinate: provider.coordinate)
                            .tint(AppColor.primaryContainer)
                            .tag(MapSelection.provider(provider.id))
                    }
                    ForEach(doctors) { doctor in
                        Marker(doctor.name,
                               systemImage: "stethoscope",
                               coordinate: doctor.coordinate)
                            .tint(.blue)
                            .tag(MapSelection.doctor(doctor.id))
                    }
                }
                .mapStyle(.standard)

                bottomPanel
            }
            // Floating round buttons on the top-right of the map.
            .overlay(alignment: .topTrailing) { floatingButtons }
        }
        .background(AppColor.background.ignoresSafeArea())
        .toolbar(.hidden, for: .navigationBar)
        .sheet(isPresented: $showFilters) { FilterSheet() }
    }

    // MARK: Pieces

    private var floatingButtons: some View {
        VStack(spacing: Spacing.sm) {
            mapButton("line.3.horizontal.decrease", label: "Filters") { showFilters = true }
            mapButton("location.fill", label: "Centre map") {
                withAnimation { position = .region(Self.kampalaRegion) }
            }
        }
        .padding(Spacing.md)
    }

    private func mapButton(_ symbol: String, label: String, action: @escaping () -> Void) -> some View {
        Button(action: action) {
            Image(systemName: symbol)
                .font(.headline).foregroundStyle(AppColor.primary)
                .frame(width: 48, height: 48)
                .background(.white)
                .clipShape(RoundedRectangle(cornerRadius: Radius.md))
                .shadow(color: .black.opacity(0.15), radius: 4, y: 2)
        }
        .accessibilityLabel(label)
    }

    /// Bottom card. Shows a summary normally, or the tapped provider's details.
    private var bottomPanel: some View {
        VStack(alignment: .leading, spacing: Spacing.sm) {
            Capsule().fill(AppColor.outlineVariant).frame(width: 40, height: 4)
                .frame(maxWidth: .infinity)

            if let selectedProvider {
                VStack(spacing: Spacing.sm) {
                    HStack(spacing: Spacing.md) {
                        ProviderThumbnail(provider: selectedProvider).frame(width: 64, height: 64)
                        VStack(alignment: .leading, spacing: 2) {
                            Text(selectedProvider.name).font(AppFont.labelLg).foregroundStyle(AppColor.onSurface)
                                .multilineTextAlignment(.leading)
                            Text("\(selectedProvider.statusText) • \(selectedProvider.distanceText)")
                                .font(AppFont.bodyMd).foregroundStyle(AppColor.onSurfaceVariant)
                            RatingLabel(rating: selectedProvider.rating)
                        }
                        Spacer()
                    }

                    HStack(spacing: Spacing.sm) {
                        NavigationLink(value: Route.provider(selectedProvider)) {
                            Label("View profile", systemImage: "person.text.rectangle")
                                .frame(maxWidth: .infinity, minHeight: 44)
                                .background(AppColor.primaryTint, in: RoundedRectangle(cornerRadius: Radius.sm))
                        }
                        NavigationLink(value: Route.bookAppointment(selectedProvider)) {
                            Label("Book", systemImage: "calendar.badge.plus")
                                .foregroundStyle(.white)
                                .frame(maxWidth: .infinity, minHeight: 44)
                                .background(AppColor.primary, in: RoundedRectangle(cornerRadius: Radius.sm))
                        }
                        CallCircleButton(phone: selectedProvider.phone, filled: true)
                    }
                    .font(AppFont.labelSm)
                }
                .foregroundStyle(AppColor.primary)
            } else if let selectedDoctor {
                doctorPanel(selectedDoctor)
            } else {
                HStack(alignment: .bottom) {
                    VStack(alignment: .leading, spacing: 2) {
                        Text("FOUND \(providers.count) FACILITIES · \(doctors.count) DOCTORS")
                            .font(AppFont.labelLg).foregroundStyle(AppColor.primary)
                        Text("Kampala District").font(AppFont.headlineLg)
                    }
                    Spacer()
                    Button { showFilters = true } label: {
                        Label("REFINE", systemImage: "slider.horizontal.3")
                            .font(AppFont.labelLg)
                            .foregroundStyle(AppColor.onSurfaceVariant)
                            .padding(.horizontal, Spacing.md)
                            .frame(minHeight: 44)
                            .background(AppColor.surfaceHigh)
                            .clipShape(Capsule())
                    }
                }
            }
        }
        .padding(Spacing.md)
        .frame(maxWidth: .infinity)
        .background(
            UnevenRoundedRectangle(topLeadingRadius: Radius.xl, topTrailingRadius: Radius.xl)
                .fill(AppColor.surface)
                .shadow(color: .black.opacity(0.12), radius: 8, y: -2)
        )
    }

    private func doctorPanel(_ doctor: CareDoctor) -> some View {
        VStack(alignment: .leading, spacing: Spacing.sm) {
            HStack(spacing: Spacing.md) {
                Image(systemName: "person.crop.circle.fill")
                    .font(.system(size: 54))
                    .foregroundStyle(.blue)
                    .frame(width: 64, height: 64)
                    .background(Color.blue.opacity(0.1), in: Circle())
                VStack(alignment: .leading, spacing: 3) {
                    Text(doctor.name)
                        .font(AppFont.headlineMd)
                        .foregroundStyle(AppColor.onSurface)
                    Text(doctor.specialty)
                        .font(AppFont.bodyMd)
                        .foregroundStyle(AppColor.primary)
                        .lineLimit(2)
                    Text("\(doctor.provider.name) · \(doctor.availability)")
                        .font(AppFont.labelSm)
                        .foregroundStyle(AppColor.onSurfaceVariant)
                        .lineLimit(2)
                }
                Spacer(minLength: 0)
            }

            HStack(spacing: Spacing.sm) {
                NavigationLink(value: Route.provider(doctor.provider)) {
                    Label("Facility", systemImage: "building.2")
                        .frame(maxWidth: .infinity, minHeight: 42)
                }
                NavigationLink(value: Route.chat(doctor.name)) {
                    Label("Message", systemImage: "message")
                        .frame(maxWidth: .infinity, minHeight: 42)
                }
                NavigationLink(value: Route.bookDoctor(doctor)) {
                    Label("Book", systemImage: "calendar.badge.plus")
                        .foregroundStyle(.white)
                        .frame(maxWidth: .infinity, minHeight: 42)
                        .background(AppColor.primary, in: RoundedRectangle(cornerRadius: Radius.sm))
                }
            }
            .font(AppFont.labelSm)
            .foregroundStyle(AppColor.primary)
        }
    }
}

#Preview {
    NavigationStack { ProvidersMapView().appDestinations() }
        .environment(AppState())
}
