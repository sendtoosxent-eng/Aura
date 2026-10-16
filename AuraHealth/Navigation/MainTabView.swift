import SwiftUI

// The 5-tab shell for patients: Home · Map · Emergency · Messages · Account.
struct MainTabView: View {
    @Environment(AppState.self) private var appState
    @State private var showOffline = false

    var body: some View {
        // LEARN: To make a *binding* ($) to a property of an @Observable object
        // we wrap it in @Bindable. It lets us write $state.selectedTab below.
        @Bindable var state = appState

        TabView(selection: $state.selectedTab) {

            // LEARN: Each tab gets ITS OWN NavigationStack, so pushing a screen in
            // one tab doesn't affect the others (and the tab bar stays visible).
            NavigationStack { HomeView().appDestinations() }
                .tabItem { Label("Home", systemImage: "house") }
                .tag(MainTab.home)

            NavigationStack { ProvidersMapView().appDestinations() }
                .tabItem { Label("Map", systemImage: "map") }
                .tag(MainTab.map)

            // The Emergency tab has no content of its own: tapping it opens the
            // full-screen EmergencyView instead (see .onChange below).
            Color.clear
                .tabItem { Label("Emergency", systemImage: "light.beacon.max") }
                .tag(MainTab.emergency)

            NavigationStack { MessagesView().appDestinations() }
                .tabItem { Label("Messages", systemImage: "message.fill") }
                .badge(appState.messageThreads.reduce(0) { $0 + $1.unreadCount })
                .tag(MainTab.messages)

            NavigationStack { AccountView().appDestinations() }
                .tabItem { Label("Account", systemImage: "person") }
                .tag(MainTab.account)
        }
        .tint(AppColor.primary)
        // Intercept the Emergency tab: bounce back to the previous tab and open
        // the emergency screen over everything.
        .onChange(of: state.selectedTab) { oldTab, newTab in
            if newTab == .emergency {
                state.selectedTab = oldTab
                state.showEmergency = true
            }
        }
        .fullScreenCover(isPresented: $state.showEmergency) {
            EmergencyView()
        }
        // When the internet drops, show the "No Connection" screen.
        .onChange(of: appState.network.isConnected) { _, connected in
            if !connected { showOffline = true }
        }
        .fullScreenCover(isPresented: $showOffline) {
            NoInternetView(
                onRetry: { if appState.network.isConnected { showOffline = false } },
                onViewOffline: { showOffline = false }
            )
        }
        .task {
            await appState.loadDiscoveryIfNeeded()
        }
    }
}

#Preview {
    MainTabView().environment(AppState())
}
