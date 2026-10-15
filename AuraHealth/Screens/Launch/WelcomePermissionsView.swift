import SwiftUI
import CoreLocation
import UserNotifications

@MainActor
final class WelcomePermissions: NSObject, ObservableObject, CLLocationManagerDelegate {
    @Published var location: CLAuthorizationStatus = .notDetermined
    @Published var notifications: UNAuthorizationStatus = .notDetermined
    @Published var requesting = false
    @Published var failed = false
    private let manager = CLLocationManager()

    override init() {
        super.init()
        manager.delegate = self
        location = manager.authorizationStatus
    }

    nonisolated func locationManagerDidChangeAuthorization(_ manager: CLLocationManager) {
        let status = manager.authorizationStatus
        Task { @MainActor [weak self] in
            self?.location = status
            self?.requesting = false
        }
    }

    func refresh() async {
        location = manager.authorizationStatus
        notifications = await UNUserNotificationCenter.current().notificationSettings().authorizationStatus
    }

    func requestLocation() {
        guard location == .notDetermined else { return }
        requesting = true
        manager.requestWhenInUseAuthorization()
    }

    func requestNotifications() async {
        requesting = true
        defer { requesting = false }
        do {
            _ = try await UNUserNotificationCenter.current().requestAuthorization(options: [.alert, .sound, .badge])
            await PushNotificationManager.shared.registerWithAppleIfAuthorized()
            await refresh()
        } catch { failed = true }
    }
}

struct WelcomePermissionsView: View {
    let onContinue: () -> Void
    @StateObject private var permissions = WelcomePermissions()
    @Environment(\.scenePhase) private var scenePhase
    @Environment(\.openURL) private var openURL
    @AppStorage("preferredLanguage") private var languageCode = WelcomeLanguage.deviceDefault
    private var language: WelcomeLanguage { WelcomeLanguage(rawValue: languageCode) ?? .english }
    private var enabled: String { language.text("Enabled", "Kikkiriziddwa", "Imeruhusiwa") }
    private var allow: String { language.text("Allow", "Kkiriza", "Ruhusu") }
    private var settings: String { language.text("Open Settings", "Ggulawo enteekateeka", "Fungua mipangilio") }

    var body: some View {
        ScrollView {
            VStack(alignment: .leading, spacing: 24) {
                HStack {
                    BrandMark(size: 60)
                    Spacer()
                    Button(language.skip, action: onContinue)
                        .font(.subheadline.weight(.semibold))
                        .foregroundStyle(Color(hex: 0xB71C1C))
                        .padding(.horizontal, 16)
                        .frame(minHeight: 44)
                        .background(AppColor.surface, in: Capsule())
                        .overlay(Capsule().stroke(AppColor.outlineVariant, lineWidth: 1))
                        .accessibilityHint(language.text("Continue without enabling permissions", "Genda mu maaso nga tokkirizza bino", "Endelea bila kuruhusu haya"))
                }
                .padding(.top, 24)
                Text(language.text("Make AfyaLink yours.", "Teekateeka AfyaLink yo.", "Weka AfyaLink upendavyo."))
                    .font(.largeTitle.bold()).foregroundStyle(AppColor.onSurface)
                Text(language.text("Choose what you share. You can continue without enabling these permissions.", "Londa by’ogabana. Osobola okugenda mu maaso nga tokkirizza bino.", "Chagua unachoshiriki. Unaweza kuendelea bila kuruhusu haya."))
                    .foregroundStyle(AppColor.onSurfaceVariant)
                permissionCard(
                    icon: "location.fill",
                    title: language.text("Your location", "Ekifo ky’oli", "Mahali ulipo"),
                    detail: language.text("Allow location while using AfyaLink to support nearby care discovery.", "Kkiriza AfyaLink okumanya ekifo ky’oli ng’ogikozesa okunoonya obujjanjabi obuli okumpi.", "Ruhusu eneo lako unapotumia AfyaLink kusaidia kutafuta huduma zilizo karibu."),
                    action: locationAction,
                    isEnabled: locationAllowed
                ) {
                    if permissions.location == .notDetermined { permissions.requestLocation() }
                    else { openSettings() }
                }
                permissionCard(
                    icon: "bell.badge.fill",
                    title: language.text("Notifications", "Obubaka", "Arifa"),
                    detail: language.text("Allow alerts, sounds and badges. You control them in iPhone Settings.", "Kkiriza obubaka n’amaloboozi. Osobola okubikyusa mu nteekateeka za iPhone.", "Ruhusu arifa, sauti na beji. Unaweza kuzibadilisha katika mipangilio ya iPhone."),
                    action: notificationAllowed ? enabled : permissions.notifications == .notDetermined ? allow : settings,
                    isEnabled: notificationAllowed
                ) {
                    if permissions.notifications == .notDetermined {
                        Task { await permissions.requestNotifications() }
                    } else { openSettings() }
                }
                Label {
                    VStack(alignment: .leading, spacing: 6) {
                        Text(language.text("Matches your phone", "Egoberera essimu yo", "Inafuata simu yako")).font(.headline)
                        Text(language.text("Light or dark, AfyaLink follows your iPhone’s appearance automatically.", "AfyaLink egoberera endabika y’essimu yo, etangaala oba enzikivu.", "AfyaLink inafuata mwonekano wa iPhone yako, mweupe au mweusi, kiotomatiki."))
                            .font(.subheadline).foregroundStyle(AppColor.onSurfaceVariant)
                    }
                } icon: { Image(systemName: "circle.lefthalf.filled").foregroundStyle(AppColor.success) }
                .padding(20).frame(maxWidth: .infinity, alignment: .leading)
                .background(AppColor.surface, in: RoundedRectangle(cornerRadius: 20))
                Button(action: onContinue) {
                    Text(language.next).font(.headline).foregroundStyle(.white)
                        .frame(maxWidth: .infinity, minHeight: 56)
                        .background(Color(hex: 0xB71C1C), in: RoundedRectangle(cornerRadius: 18))
                }
                .disabled(permissions.requesting)
            }
            .padding(24).frame(maxWidth: 620).frame(maxWidth: .infinity)
        }
        .background(AppColor.background.ignoresSafeArea())
        .task { await permissions.refresh() }
        .onChange(of: scenePhase) { _, phase in
            if phase == .active { Task { await permissions.refresh() } }
        }
        .alert(language.text("Please try again", "Ddamu ogezeeko", "Tafadhali jaribu tena"), isPresented: $permissions.failed) {
            Button(language.done, role: .cancel) {}
        }
    }

    private var locationAllowed: Bool {
        permissions.location == .authorizedAlways || permissions.location == .authorizedWhenInUse
    }
    private var notificationAllowed: Bool {
        [.authorized, .provisional, .ephemeral].contains(permissions.notifications)
    }
    private var locationAction: String {
        if locationAllowed { return enabled }
        if permissions.location == .restricted { return language.text("Restricted", "Kikugiddwa", "Imezuiwa") }
        return permissions.location == .notDetermined ? allow : settings
    }
    private func openSettings() {
        if let url = URL(string: UIApplication.openSettingsURLString) { openURL(url) }
    }
    private func permissionCard(icon: String, title: String, detail: String, action: String,
                                isEnabled: Bool, perform: @escaping () -> Void) -> some View {
        VStack(alignment: .leading, spacing: 14) {
            Label(title, systemImage: icon).font(.title3.bold()).foregroundStyle(AppColor.onSurface)
            Text(detail).foregroundStyle(AppColor.onSurfaceVariant).fixedSize(horizontal: false, vertical: true)
            Button(action, action: perform)
                .font(.headline).foregroundStyle(isEnabled ? AppColor.success : AppColor.primary)
                .frame(minHeight: 44)
                .disabled(isEnabled || permissions.requesting || (icon == "location.fill" && permissions.location == .restricted))
        }
        .padding(20).frame(maxWidth: .infinity, alignment: .leading)
        .background(AppColor.surface, in: RoundedRectangle(cornerRadius: 20))
    }
}

#Preview {
    WelcomePermissionsView(onContinue: {})
}
