import SwiftUI

struct SecurityView: View {
    @Environment(AppState.self) private var appState
    @State private var twoFactorEnabled = true
    @State private var loginAlerts = true
    @State private var sessionToRemove: TrustedSession?

    var body: some View {
        List {
            Section("Sign-in protection") {
                Toggle("Two-factor authentication", isOn: $twoFactorEnabled)
                Toggle("New device and IP alerts", isOn: $loginAlerts)
                NavigationLink {
                    ForgotPasswordView(phone: appState.userPhone)
                } label: {
                    Label("Change password", systemImage: "key.fill")
                }
            }

            Section {
                ForEach(appState.trustedSessions) { session in
                    HStack(spacing: Spacing.md) {
                        Image(systemName: session.device.contains("iPhone") ? "iphone" : "desktopcomputer")
                            .foregroundStyle(AppColor.primary)
                            .frame(width: 36, height: 36)
                            .background(AppColor.primaryTint, in: Circle())
                        VStack(alignment: .leading, spacing: 2) {
                            HStack {
                                Text(session.device).font(AppFont.labelLg)
                                if session.isCurrent {
                                    Text("CURRENT").font(.caption2.bold()).foregroundStyle(AppColor.success)
                                }
                            }
                            Text("\(session.location) · \(session.lastActive)")
                                .font(AppFont.labelSm).foregroundStyle(AppColor.onSurfaceVariant)
                        }
                        Spacer()
                        if !session.isCurrent {
                            Button { sessionToRemove = session } label: {
                                Image(systemName: "xmark.circle.fill").foregroundStyle(AppColor.primary)
                            }
                            .buttonStyle(.plain)
                            .accessibilityLabel("Remove \(session.device)")
                        }
                    }
                }
            } header: {
                Text("Trusted devices")
            } footer: {
                Text("A removed device must verify with an OTP the next time it signs in.")
            }

            Section("Privacy") {
                Label("Medical records are visible only to you and providers you authorize.", systemImage: "lock.shield.fill")
                    .font(AppFont.bodyMd)
                    .foregroundStyle(AppColor.onSurfaceVariant)
            }
        }
        .navigationTitle("Security & Privacy")
        .navigationBarTitleDisplayMode(.inline)
        .tint(AppColor.primary)
        .confirmationDialog(
            "Remove this trusted device?",
            isPresented: Binding(get: { sessionToRemove != nil }, set: { if !$0 { sessionToRemove = nil } }),
            presenting: sessionToRemove
        ) { session in
            Button("Remove \(session.device)", role: .destructive) {
                appState.removeTrustedSession(session)
                sessionToRemove = nil
            }
            Button("Cancel", role: .cancel) { sessionToRemove = nil }
        }
    }
}

#Preview {
    NavigationStack { SecurityView() }.environment(AppState())
}
