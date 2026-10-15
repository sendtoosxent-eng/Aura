import SwiftUI
import MapKit
import UIKit

// SCREEN 8 — Emergency Mode (design: emergency_mode)
// Opens full-screen over the whole app. Big call button, share location,
// SMS, and the nearest OPEN facilities with one-tap CALL.
struct EmergencyView: View {
    @Environment(AppState.self) private var appState
    @Environment(\.dismiss) private var dismiss

    @State private var showVitals = false

    // Placeholder text — a real app uses CoreLocation + reverse geocoding.
    private let currentPlace = "Mulago Hill, Kampala"

    private var locationURL: URL {
        URL(string: "https://maps.apple.com/?ll=\(AppConfig.kampala.latitude),\(AppConfig.kampala.longitude)")!
    }

    var body: some View {
        VStack(spacing: 0) {
            topBar

            ScrollView {
                VStack(spacing: Spacing.md) {
                    mapWithLocation
                    callButton
                    HStack(spacing: Spacing.md) { shareTile; smsTile }
                    facilitiesSection
                }
                .padding(Spacing.md)
                .readableWidth()
            }

            bottomBar
        }
        .background(AppColor.background.ignoresSafeArea())
        .sheet(isPresented: $showVitals) {
            NavigationStack { EmergencyHealthCardView() }
        }
    }

    // MARK: Pieces

    private var topBar: some View {
        HStack {
            Button { dismiss() } label: {
                Image(systemName: "xmark").font(.title3.weight(.semibold))
                    .frame(width: 44, height: 44)
            }
            .accessibilityLabel("Close emergency mode")
            Text("EMERGENCY MODE").font(AppFont.headlineLg)
            Spacer()
            Image(systemName: "asterisk").font(.title2.weight(.bold))
        }
        .foregroundStyle(.white)
        .padding(.horizontal, Spacing.sm)
        .frame(maxWidth: .infinity)
        .background(AppColor.primary.ignoresSafeArea(edges: .top))
    }

    /// Small non-interactive map with a "your current location" card on top.
    private var mapWithLocation: some View {
        Map(initialPosition: .region(MKCoordinateRegion(
            center: AppConfig.kampala,
            span: MKCoordinateSpan(latitudeDelta: 0.05, longitudeDelta: 0.05))),
            interactionModes: []) {
                Marker("You", systemImage: "location.fill", coordinate: AppConfig.kampala)
                    .tint(AppColor.primaryContainer)
            }
            .frame(height: 170)
            .clipShape(RoundedRectangle(cornerRadius: Radius.lg))
            .overlay(alignment: .bottom) {
                HStack(spacing: Spacing.md) {
                    Image(systemName: "scope").font(.title).foregroundStyle(AppColor.primary)
                    VStack(alignment: .leading, spacing: 2) {
                        Text("YOUR CURRENT LOCATION")
                            .font(AppFont.labelSm).tracking(1.5).foregroundStyle(AppColor.onSurfaceVariant)
                        Text(currentPlace).font(AppFont.headlineMd).foregroundStyle(AppColor.onSurface)
                    }
                    Spacer()
                }
                .padding(Spacing.md)
                .background(.ultraThinMaterial)
                .clipShape(RoundedRectangle(cornerRadius: Radius.lg))
                .padding(Spacing.sm)
            }
    }

    private var callButton: some View {
        Button {
            PhoneCaller.call(AppConfig.emergencyNumber)
        } label: {
            HStack(spacing: Spacing.md) {
                Image(systemName: "phone.fill").font(.title)
                Text("CALL \(AppConfig.emergencyLabel)")
                    .font(.system(.title, weight: .bold))
                    .minimumScaleFactor(0.7).lineLimit(1)   // never overflows on small phones
            }
            .foregroundStyle(.white)
            .frame(maxWidth: .infinity, minHeight: 88)
            .background(AppColor.primary)
            .clipShape(RoundedRectangle(cornerRadius: Radius.lg))
            .background(PulseRing(cornerRadius: Radius.lg))
        }
        .padding(.vertical, Spacing.xs)
    }

    /// LEARN: ShareLink is the built-in iOS share sheet (Messages, WhatsApp…).
    private var shareTile: some View {
        ShareLink(item: locationURL, message: Text("I need help. This is my location.")) {
            actionTileLabel(icon: "location.circle", title: "Share Location")
        }
    }

    private var smsTile: some View {
        Button {
            let body = "EMERGENCY! I need help. My location: \(locationURL.absoluteString)"
            let encoded = body.addingPercentEncoding(withAllowedCharacters: .urlQueryAllowed) ?? ""
            if let url = URL(string: "sms:&body=\(encoded)") {
                UIApplication.shared.open(url)
            }
        } label: {
            actionTileLabel(icon: "message", title: "Emergency SMS")
        }
    }

    private func actionTileLabel(icon: String, title: String) -> some View {
        VStack(spacing: Spacing.sm) {
            Image(systemName: icon).font(.title)
            Text(title).font(AppFont.labelLg)
        }
        .foregroundStyle(AppColor.primary)
        .frame(maxWidth: .infinity, minHeight: 96)
        .background(AppColor.surface)
        .clipShape(RoundedRectangle(cornerRadius: Radius.lg))
        .overlay(RoundedRectangle(cornerRadius: Radius.lg).stroke(AppColor.primary, lineWidth: 2))
    }

    private var facilitiesSection: some View {
        VStack(alignment: .leading, spacing: Spacing.sm) {
            HStack {
                Image(systemName: "cross.case").foregroundStyle(AppColor.primary)
                Text("Open Emergency Services").font(AppFont.headlineMd)
                Spacer()
                Text("LIVE NOW")
                    .font(AppFont.labelSm).foregroundStyle(AppColor.success)
                    .padding(.horizontal, 10).padding(.vertical, 4)
                    .background(AppColor.successContainer).clipShape(Capsule())
            }

            ForEach(appState.emergencyProviders.prefix(3)) { provider in
                HStack(spacing: Spacing.md) {
                    VStack(alignment: .leading, spacing: 6) {
                        Text(provider.name).font(AppFont.bodyLg).foregroundStyle(AppColor.onSurface)
                            .multilineTextAlignment(.leading)
                        HStack(spacing: 6) {
                            Image(systemName: "car.fill").foregroundStyle(AppColor.secondary)
                            Text("\(provider.distanceText) • \(provider.etaMinutes) mins")
                                .font(AppFont.labelLg).foregroundStyle(AppColor.primary)
                        }
                    }
                    Spacer(minLength: Spacing.sm)
                    CallPillButton(phone: provider.phone)
                }
                .padding(Spacing.md)
                .cardStyle(radius: Radius.lg)
            }
        }
    }

    private var bottomBar: some View {
        HStack(spacing: 0) {
            Button { dismiss() } label: {
                VStack(spacing: 4) {
                    Image(systemName: "xmark").font(.title3.weight(.bold))
                    Text("Exit Emergency").font(AppFont.labelSm)
                }
                .foregroundStyle(AppColor.primary)
                .frame(maxWidth: .infinity, minHeight: 56)
            }
            Divider().frame(height: 36)
            Button { showVitals = true } label: {
                VStack(spacing: 4) {
                    Image(systemName: "cross.case").font(.title3)
                    Text("My Vitals").font(AppFont.labelSm)
                }
                .foregroundStyle(AppColor.onSurfaceVariant)
                .frame(maxWidth: .infinity, minHeight: 56)
            }
        }
        .background(AppColor.surface.ignoresSafeArea(edges: .bottom))
        .overlay(alignment: .top) { Divider() }
    }

}

#Preview {
    EmergencyView().environment(AppState())
}
