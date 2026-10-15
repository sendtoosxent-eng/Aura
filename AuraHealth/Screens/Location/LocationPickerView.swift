import SwiftUI
import MapKit

struct LocationPickerView: View {
    @Environment(AppState.self) private var appState
    @Environment(\.dismiss) private var dismiss
    @State private var query = ""
    @State private var selection: SavedCareLocation
    @State private var position: MapCameraPosition

    private let locations = [
        SavedCareLocation(name: "Current location", detail: "Kampala, Central Region", latitude: 0.3476, longitude: 32.5825),
        SavedCareLocation(name: "Mulago", detail: "Kawempe Division, Kampala", latitude: 0.3376, longitude: 32.5763),
        SavedCareLocation(name: "Ntinda", detail: "Nakawa Division, Kampala", latitude: 0.3548, longitude: 32.6119),
        SavedCareLocation(name: "Makindye", detail: "Makindye Division, Kampala", latitude: 0.2788, longitude: 32.5864),
        SavedCareLocation(name: "Wakiso", detail: "Wakiso District", latitude: 0.4044, longitude: 32.4594),
        SavedCareLocation(name: "Entebbe", detail: "Wakiso District", latitude: 0.0512, longitude: 32.4637)
    ]

    init() {
        let current = SavedCareLocation(name: "Current location", detail: "Kampala, Central Region",
                                        latitude: AppConfig.kampala.latitude, longitude: AppConfig.kampala.longitude)
        _selection = State(initialValue: current)
        _position = State(initialValue: .region(MKCoordinateRegion(
            center: CLLocationCoordinate2D(latitude: current.latitude, longitude: current.longitude),
            span: MKCoordinateSpan(latitudeDelta: 0.12, longitudeDelta: 0.12)
        )))
    }

    private var filteredLocations: [SavedCareLocation] {
        guard !query.isEmpty else { return locations }
        return locations.filter {
            $0.name.localizedCaseInsensitiveContains(query) || $0.detail.localizedCaseInsensitiveContains(query)
        }
    }

    var body: some View {
        VStack(spacing: 0) {
            HStack {
                Image(systemName: "magnifyingglass").foregroundStyle(AppColor.onSurfaceVariant)
                TextField("Search town, district or neighborhood", text: $query)
            }
            .inputBox()
            .padding(Spacing.md)

            Map(position: $position) {
                Marker(selection.name,
                       coordinate: CLLocationCoordinate2D(latitude: selection.latitude, longitude: selection.longitude))
                    .tint(AppColor.primary)
            }
            .frame(height: 230)

            List(filteredLocations) { location in
                Button {
                    select(location)
                } label: {
                    HStack(spacing: Spacing.md) {
                        Image(systemName: location.name == "Current location" ? "location.fill" : "mappin.circle.fill")
                            .foregroundStyle(AppColor.primary)
                        VStack(alignment: .leading) {
                            Text(location.name).font(AppFont.labelLg).foregroundStyle(AppColor.onSurface)
                            Text(location.detail).font(AppFont.bodyMd).foregroundStyle(AppColor.onSurfaceVariant)
                        }
                        Spacer()
                        if selection.name == location.name {
                            Image(systemName: "checkmark.circle.fill").foregroundStyle(AppColor.success)
                        }
                    }
                }
            }
            .listStyle(.plain)
        }
        .navigationTitle("Care Location")
        .navigationBarTitleDisplayMode(.inline)
        .safeAreaInset(edge: .bottom) {
            PrimaryButton(title: "Use This Location", icon: "location.fill") {
                appState.selectedCareLocation = selection
                dismiss()
            }
            .padding(Spacing.md)
            .background(AppColor.surface)
        }
    }

    private func select(_ location: SavedCareLocation) {
        selection = location
        withAnimation {
            position = .region(MKCoordinateRegion(
                center: CLLocationCoordinate2D(latitude: location.latitude, longitude: location.longitude),
                span: MKCoordinateSpan(latitudeDelta: 0.09, longitudeDelta: 0.09)
            ))
        }
    }
}

#Preview {
    NavigationStack { LocationPickerView() }.environment(AppState())
}
