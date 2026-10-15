import Foundation
import Network
import Observation

// LEARN: NWPathMonitor is Apple's built-in "is the internet reachable?" watcher.
// It calls our closure whenever connectivity changes. The closure runs on a
// background queue, but UI state must change on the MAIN thread, so we hop
// there with `Task { @MainActor in … }`.
@Observable
final class NetworkMonitor {
    var isConnected = true

    @ObservationIgnored private let monitor = NWPathMonitor()

    init() {
        monitor.pathUpdateHandler = { [weak self] path in
            let online = path.status == .satisfied
            Task { @MainActor in
                self?.isConnected = online
            }
        }
        monitor.start(queue: DispatchQueue(label: "NetworkMonitor"))
    }

    deinit { monitor.cancel() }
}
