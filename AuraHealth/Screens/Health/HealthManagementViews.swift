import SwiftUI

struct MedicationRemindersView: View {
    @Environment(AppState.self) private var appState
    @State private var showAddReminder = false

    var body: some View {
        ScrollView {
            VStack(spacing: Spacing.md) {
                InfoNote(text: "AfyaLink reminders support your care plan but do not replace instructions from your clinician.")

                ForEach(appState.medicationReminders) { reminder in
                    reminderCard(reminder)
                }

                SecondaryButton(title: "Add Medication Reminder", icon: "plus") {
                    showAddReminder = true
                }
            }
            .padding(Spacing.md)
            .readableWidth()
        }
        .background(AppColor.background.ignoresSafeArea())
        .redInlineTitle("Medication & Reminders")
        .sheet(isPresented: $showAddReminder) {
            AddMedicationReminderView()
        }
    }

    private func reminderCard(_ reminder: MedicationReminder) -> some View {
        VStack(alignment: .leading, spacing: Spacing.md) {
            HStack {
                Image(systemName: "pills.fill")
                    .foregroundStyle(.white)
                    .frame(width: 46, height: 46)
                    .background(AppColor.primary, in: RoundedRectangle(cornerRadius: Radius.md))
                VStack(alignment: .leading, spacing: 2) {
                    Text(reminder.medicine).font(AppFont.headlineMd)
                    Text(reminder.instructions)
                        .font(AppFont.bodyMd)
                        .foregroundStyle(AppColor.onSurfaceVariant)
                }
                Spacer()
                Toggle("", isOn: Binding(
                    get: { reminder.isEnabled },
                    set: { _ in appState.toggleReminder(reminder) }
                ))
                .labelsHidden()
                .tint(AppColor.primary)
            }

            Label(reminder.times.joined(separator: " · "), systemImage: "clock.fill")
                .font(AppFont.labelLg)
                .foregroundStyle(AppColor.onSurfaceVariant)

            if let refill = reminder.nextRefill {
                Label("Refill by \(refill.formatted(date: .abbreviated, time: .omitted))", systemImage: "calendar.badge.clock")
                    .font(AppFont.bodyMd)
                    .foregroundStyle(AppColor.onSurfaceVariant)
            }

            Button {
                appState.markMedicineTaken(reminder)
            } label: {
                Label(reminder.takenToday ? "Taken today" : "Mark as taken", systemImage: reminder.takenToday ? "checkmark.circle.fill" : "circle")
                    .font(AppFont.labelLg)
                    .foregroundStyle(reminder.takenToday ? AppColor.success : AppColor.primary)
                    .frame(maxWidth: .infinity, minHeight: 44)
                    .background(reminder.takenToday ? AppColor.successContainer : AppColor.primaryTint,
                                in: RoundedRectangle(cornerRadius: Radius.sm))
            }
        }
        .padding(Spacing.md)
        .cardStyle(radius: Radius.lg)
    }
}

private struct AddMedicationReminderView: View {
    @Environment(AppState.self) private var appState
    @Environment(\.dismiss) private var dismiss
    @State private var medicine = ""
    @State private var instructions = ""
    @State private var morning = true
    @State private var evening = false
    @State private var refillDate = Date().addingTimeInterval(30 * 86_400)

    var body: some View {
        NavigationStack {
            Form {
                Section("Medication") {
                    TextField("Medicine name", text: $medicine)
                    TextField("Dose and instructions", text: $instructions)
                }
                Section("Reminder times") {
                    Toggle("Morning · 8:00 AM", isOn: $morning)
                    Toggle("Evening · 8:00 PM", isOn: $evening)
                    DatePicker("Next refill", selection: $refillDate, in: Date()..., displayedComponents: .date)
                }
            }
            .navigationTitle("Add Reminder")
            .navigationBarTitleDisplayMode(.inline)
            .toolbar {
                ToolbarItem(placement: .cancellationAction) { Button("Cancel") { dismiss() } }
                ToolbarItem(placement: .confirmationAction) {
                    Button("Save") {
                        var times: [String] = []
                        if morning { times.append("8:00 AM") }
                        if evening { times.append("8:00 PM") }
                        appState.medicationReminders.append(
                            MedicationReminder(medicine: medicine, instructions: instructions,
                                               times: times, nextRefill: refillDate,
                                               isEnabled: true, takenToday: false)
                        )
                        dismiss()
                    }
                    .disabled(medicine.isEmpty || (!morning && !evening))
                }
            }
        }
    }
}

struct MedicalRecordsView: View {
    @Environment(AppState.self) private var appState
    @State private var filter: MedicalRecordType?
    @State private var selectedRecord: MedicalRecord?

    private var records: [MedicalRecord] {
        guard let filter else { return appState.medicalRecords }
        return appState.medicalRecords.filter { $0.type == filter }
    }

    var body: some View {
        ScrollView {
            VStack(spacing: Spacing.md) {
                ScrollView(.horizontal, showsIndicators: false) {
                    HStack {
                        recordChip("All", type: nil)
                        ForEach(MedicalRecordType.allCases, id: \.self) { recordChip($0.rawValue, type: $0) }
                    }
                }

                ForEach(records) { record in
                    Button { selectedRecord = record } label: {
                        HStack(spacing: Spacing.md) {
                            Image(systemName: record.type.icon)
                                .font(.title3)
                                .foregroundStyle(AppColor.primary)
                                .frame(width: 48, height: 48)
                                .background(AppColor.primaryTint, in: RoundedRectangle(cornerRadius: Radius.md))
                            VStack(alignment: .leading, spacing: 3) {
                                Text(record.title).font(AppFont.labelLg).foregroundStyle(AppColor.onSurface)
                                Text(record.provider).font(AppFont.bodyMd).foregroundStyle(AppColor.onSurfaceVariant)
                                Text(record.date.formatted(date: .abbreviated, time: .omitted))
                                    .font(AppFont.labelSm).foregroundStyle(AppColor.secondary)
                            }
                            Spacer()
                            Image(systemName: "chevron.right").foregroundStyle(AppColor.outline)
                        }
                        .padding(Spacing.md)
                        .cardStyle(radius: Radius.md)
                    }
                    .buttonStyle(.plain)
                }
            }
            .padding(Spacing.md)
            .readableWidth()
        }
        .background(AppColor.background.ignoresSafeArea())
        .redInlineTitle("Medical Records")
        .sheet(item: $selectedRecord) { record in
            NavigationStack {
                VStack(alignment: .leading, spacing: Spacing.md) {
                    Label(record.type.rawValue, systemImage: record.type.icon)
                        .font(AppFont.headlineMd).foregroundStyle(AppColor.primary)
                    Text(record.title).font(AppFont.headlineLg)
                    Text(record.provider).foregroundStyle(AppColor.onSurfaceVariant)
                    Divider()
                    Text(record.detail).font(AppFont.bodyLg)
                    ShareLink(item: "\(record.title) — \(record.detail)") {
                        Label("Share record", systemImage: "square.and.arrow.up")
                            .frame(maxWidth: .infinity, minHeight: 48)
                    }
                    Spacer()
                }
                .padding(Spacing.lg)
                .navigationTitle("Record Details")
                .navigationBarTitleDisplayMode(.inline)
            }
            .presentationDetents([.medium, .large])
        }
    }

    private func recordChip(_ title: String, type: MedicalRecordType?) -> some View {
        Button { filter = type } label: {
            Text(title).font(AppFont.labelLg)
                .foregroundStyle(filter == type ? .white : AppColor.primary)
                .padding(.horizontal, Spacing.md).frame(minHeight: 42)
                .background(filter == type ? AppColor.primary : AppColor.surface, in: Capsule())
                .overlay(Capsule().stroke(AppColor.outlineVariant))
        }
    }
}

struct EmergencyHealthCardView: View {
    @Environment(AppState.self) private var appState
    @State private var editing = false

    var body: some View {
        @Bindable var state = appState
        ScrollView {
            VStack(spacing: Spacing.md) {
                VStack(spacing: Spacing.sm) {
                    Image(systemName: "staroflife.fill")
                        .font(.system(size: 42)).foregroundStyle(.white)
                    Text(appState.userName).font(AppFont.headlineLg).foregroundStyle(.white)
                    Text(appState.healthID).font(AppFont.labelLg).foregroundStyle(.white.opacity(0.85))
                }
                .frame(maxWidth: .infinity)
                .padding(Spacing.lg)
                .background(AppColor.primary, in: RoundedRectangle(cornerRadius: Radius.lg))

                if editing {
                    Group {
                        healthField("Blood group", text: $state.emergencyHealthProfile.bloodGroup)
                        healthField("Blood pressure", text: $state.emergencyHealthProfile.bloodPressure)
                        healthField("Heart rate", text: $state.emergencyHealthProfile.heartRate)
                        healthField("Height", text: $state.emergencyHealthProfile.height)
                        healthField("Weight", text: $state.emergencyHealthProfile.weight)
                        healthField("Allergies", text: $state.emergencyHealthProfile.allergies)
                        healthField("Conditions", text: $state.emergencyHealthProfile.conditions)
                        healthField("Current medications", text: $state.emergencyHealthProfile.medications)
                        healthField("Next of kin", text: $state.emergencyHealthProfile.nextOfKin)
                        healthField("Emergency phone", text: $state.emergencyHealthProfile.nextOfKinPhone)
                    }
                } else {
                    healthRow("Blood group", value: appState.emergencyHealthProfile.bloodGroup, icon: "drop.fill")
                    healthRow("Blood pressure", value: appState.emergencyHealthProfile.bloodPressure, icon: "waveform.path.ecg")
                    healthRow("Heart rate", value: appState.emergencyHealthProfile.heartRate, icon: "heart.fill")
                    healthRow("Height", value: appState.emergencyHealthProfile.height, icon: "ruler")
                    healthRow("Weight", value: appState.emergencyHealthProfile.weight, icon: "scalemass")
                    healthRow("Allergies", value: appState.emergencyHealthProfile.allergies, icon: "allergens")
                    healthRow("Conditions", value: appState.emergencyHealthProfile.conditions, icon: "heart.text.square")
                    healthRow("Medications", value: appState.emergencyHealthProfile.medications, icon: "pills.fill")
                    healthRow("Next of kin", value: [
                        appState.emergencyHealthProfile.nextOfKin,
                        appState.emergencyHealthProfile.nextOfKinRelationship,
                        appState.emergencyHealthProfile.nextOfKinPhone
                    ].filter { !$0.isEmpty }.joined(separator: " · "), icon: "person.2.fill")
                }

                Image(systemName: "qrcode")
                    .font(.system(size: 110))
                    .foregroundStyle(AppColor.onSurface)
                Text("Emergency QR")
                    .font(AppFont.labelLg)
                Text("A responder can scan this card to access the health details you choose to share.")
                    .font(AppFont.bodyMd).foregroundStyle(AppColor.onSurfaceVariant).multilineTextAlignment(.center)

                ShareLink(item: emergencySummary) {
                    Label("Share Emergency Card", systemImage: "square.and.arrow.up")
                        .font(AppFont.labelLg)
                        .frame(maxWidth: .infinity, minHeight: 48)
                        .background(AppColor.primaryTint, in: RoundedRectangle(cornerRadius: Radius.sm))
                }
            }
            .padding(Spacing.md)
            .readableWidth(560)
        }
        .background(AppColor.background.ignoresSafeArea())
        .redInlineTitle("Emergency Health Card")
        .toolbar {
            ToolbarItem(placement: .topBarTrailing) {
                Button(editing ? "Done" : "Edit") { editing.toggle() }
            }
        }
    }

    private func healthRow(_ title: String, value: String, icon: String) -> some View {
        LabeledContent {
            Text(value).foregroundStyle(AppColor.onSurface).multilineTextAlignment(.trailing)
        } label: {
            Label(title, systemImage: icon).foregroundStyle(AppColor.primary)
        }
        .padding(Spacing.md).cardStyle(radius: Radius.md)
    }

    private func healthField(_ title: String, text: Binding<String>) -> some View {
        LabeledField(title) { TextField(title, text: text).inputBox() }
    }

    private var emergencySummary: String {
        let p = appState.emergencyHealthProfile
        return "Emergency card for \(appState.userName). Blood group: \(p.bloodGroup). Blood pressure: \(p.bloodPressure). Heart rate: \(p.heartRate). Allergies: \(p.allergies). Conditions: \(p.conditions). Medications: \(p.medications). Next of kin: \(p.nextOfKin), \(p.nextOfKinRelationship), \(p.nextOfKinPhone)."
    }
}
