import UIKit
import UserNotifications

extension Notification.Name {
    static let afyaLinkRemoteNotificationReceived = Notification.Name("afyaLinkRemoteNotificationReceived")
}

@MainActor
final class PushNotificationManager {
    static let shared = PushNotificationManager()

    private let tokenKey = "afyalink.apns-device-token"

    var deviceToken: String? {
        UserDefaults.standard.string(forKey: tokenKey)
    }

    var environment: String {
#if DEBUG
        "development"
#else
        "production"
#endif
    }

    func registerWithAppleIfAuthorized() async {
        let settings = await UNUserNotificationCenter.current().notificationSettings()
        guard [.authorized, .provisional, .ephemeral].contains(settings.authorizationStatus) else { return }
        UIApplication.shared.registerForRemoteNotifications()
    }

    func store(deviceToken data: Data) {
        let token = data.map { String(format: "%02x", $0) }.joined()
        UserDefaults.standard.set(token, forKey: tokenKey)
        Task { try? await AfyaLinkAPI.shared.registerPushDevice(token: token, environment: environment) }
    }
}

final class AuraHealthAppDelegate: NSObject, UIApplicationDelegate, UNUserNotificationCenterDelegate {
    func application(
        _ application: UIApplication,
        didFinishLaunchingWithOptions launchOptions: [UIApplication.LaunchOptionsKey: Any]? = nil
    ) -> Bool {
        UNUserNotificationCenter.current().delegate = self
        Task { await PushNotificationManager.shared.registerWithAppleIfAuthorized() }
        return true
    }

    func application(
        _ application: UIApplication,
        didRegisterForRemoteNotificationsWithDeviceToken deviceToken: Data
    ) {
        Task { @MainActor in PushNotificationManager.shared.store(deviceToken: deviceToken) }
    }

    func application(
        _ application: UIApplication,
        didFailToRegisterForRemoteNotificationsWithError error: Error
    ) {
        // Registration is retried the next time the app becomes active.
    }

    func userNotificationCenter(
        _ center: UNUserNotificationCenter,
        willPresent notification: UNNotification
    ) async -> UNNotificationPresentationOptions {
        NotificationCenter.default.post(name: .afyaLinkRemoteNotificationReceived, object: nil)
        return [.banner, .list, .sound, .badge]
    }

    func userNotificationCenter(
        _ center: UNUserNotificationCenter,
        didReceive response: UNNotificationResponse
    ) async {
        NotificationCenter.default.post(name: .afyaLinkRemoteNotificationReceived, object: nil)
        try? await center.setBadgeCount(0)
    }

    func applicationDidBecomeActive(_ application: UIApplication) {
        Task {
            try? await UNUserNotificationCenter.current().setBadgeCount(0)
            await PushNotificationManager.shared.registerWithAppleIfAuthorized()
            NotificationCenter.default.post(name: .afyaLinkRemoteNotificationReceived, object: nil)
        }
    }

    func application(
        _ application: UIApplication,
        didReceiveRemoteNotification userInfo: [AnyHashable: Any],
        fetchCompletionHandler completionHandler: @escaping (UIBackgroundFetchResult) -> Void
    ) {
        NotificationCenter.default.post(name: .afyaLinkRemoteNotificationReceived, object: nil)
        completionHandler(.newData)
    }
}
