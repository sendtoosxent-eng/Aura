import SwiftUI

// LEARN: @main marks the entry point of the whole app. Only ONE struct in the
// project can have it. A SwiftUI app is a struct that conforms to `App` and
// returns one or more `Scene`s. On iPhone, a WindowGroup = "the app's window".
@main
struct AuraHealthApp: App {
    @UIApplicationDelegateAdaptor(AuraHealthAppDelegate.self) private var appDelegate

    // LEARN: @State here means "this view/struct owns this object and keeps it
    // alive". We create ONE AppState for the whole app and share it everywhere
    // through the environment (see .environment(appState) below).
    @State private var appState = AppState()

    var body: some Scene {
        WindowGroup {
            RootView()
                // Any view inside RootView can now read it with:
                //   @Environment(AppState.self) private var appState
                .environment(appState)
        }
    }
}
