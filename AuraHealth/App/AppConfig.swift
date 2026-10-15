import CoreLocation

// LEARN: An `enum` with no cases is a common Swift trick for a "namespace":
// a named bucket of constants that can never be instantiated by mistake.
//
// Keep the native apps aligned with the existing AfyaLink Uganda web platform.
enum AppConfig {
    static let brand = "AfyaLink"
    static let fullName = "AfyaLink Uganda"

    /// Override this at runtime with the `AFYALINK_API_BASE_URL` environment
    /// variable when testing against a backend on another computer/device.
    static var apiBaseURL: URL {
        let configured = ProcessInfo.processInfo.environment["AFYALINK_API_BASE_URL"]
            ?? UserDefaults.standard.string(forKey: "AFYALINK_API_BASE_URL")
            ?? "http://127.0.0.1:8000/api/v1/"
        let normalized = configured.hasSuffix("/") ? configured : configured + "/"
        return URL(string: normalized)!
    }

    static func serverAssetURL(_ path: String?) -> URL? {
        guard let path, !path.isEmpty else { return nil }
        if let absolute = URL(string: path), absolute.scheme != nil { return absolute }
        var components = URLComponents(url: apiBaseURL, resolvingAgainstBaseURL: false)
        components?.path = "/"
        components?.query = nil
        components?.fragment = nil
        guard let serverRoot = components?.url else { return nil }
        return URL(string: path, relativeTo: serverRoot)?.absoluteURL
    }

    // The design said "911 / 999". 911 is not used in Uganda, so we use the
    // real numbers: 999 (police/ambulance) and 112 (general emergency).
    static let emergencyNumber = "999"
    static let emergencyLabel = "999 / 112"

    // Central Kampala. Used as the default map centre and "your location" until
    // we add real GPS (CoreLocation) later.
    static let kampala = CLLocationCoordinate2D(latitude: 0.3476, longitude: 32.5825)
}
