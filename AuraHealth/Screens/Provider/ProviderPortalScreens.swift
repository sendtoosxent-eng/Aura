import SwiftUI

struct ProviderInboxView: View {
    @Environment(AppState.self) private var appState

    private var patientThreads: [MessageThread] {
        appState.messageThreads
    }

    var body: some View {
        List {
            Section {
                Label("Reply to patients and facility enquiries. Emergency symptoms should be directed to emergency services.",
                      systemImage: "info.circle")
                    .font(AppFont.bodyMd)
                    .foregroundStyle(AppColor.onSurfaceVariant)
            }

            Section("Patient conversations") {
                ForEach(patientThreads) { thread in
                    NavigationLink(value: Route.chat(thread.participantName)) {
                        HStack(spacing: Spacing.md) {
                            AvatarView(name: thread.participantName, size: 48,
                                       showsActiveDot: thread.isOnline)
                            VStack(alignment: .leading, spacing: 3) {
                                Text(thread.participantName).font(AppFont.labelLg)
                                Text(thread.lastMessage?.text ?? "Start conversation")
                                    .font(AppFont.bodyMd)
                                    .foregroundStyle(AppColor.onSurfaceVariant)
                                    .lineLimit(2)
                            }
                            Spacer()
                            if thread.unreadCount > 0 {
                                Text("\(thread.unreadCount)")
                                    .font(.caption.bold()).foregroundStyle(.white)
                                    .frame(width: 24, height: 24).background(AppColor.primary, in: Circle())
                            }
                        }
                    }
                }
            }
        }
        .navigationTitle("Provider Inbox")
        .navigationBarTitleDisplayMode(.inline)
    }
}

struct ProviderAppointmentsView: View {
    @Environment(AppState.self) private var appState

    var body: some View {
        List {
            ForEach(appState.appointments) { appointment in
                VStack(alignment: .leading, spacing: Spacing.sm) {
                    HStack {
                        VStack(alignment: .leading) {
                            Text(appointment.patientName ?? appointment.doctorName).font(AppFont.labelLg)
                            Text(appointment.providerName).font(AppFont.labelSm).foregroundStyle(AppColor.onSurfaceVariant)
                        }
                        Spacer()
                        Text(appointment.status.rawValue)
                            .font(AppFont.labelSm).foregroundStyle(AppColor.primary)
                    }
                    Text(appointment.service).font(AppFont.bodyMd)
                    Label(appointment.date.formatted(date: .abbreviated, time: .omitted) + " · " + appointment.time,
                          systemImage: "calendar")
                        .font(AppFont.bodyMd).foregroundStyle(AppColor.onSurfaceVariant)

                    if appointment.status == .requested {
                        HStack {
                            Button("Confirm") { update(appointment, status: .confirmed) }
                                .buttonStyle(.borderedProminent).tint(AppColor.primary)
                            Button("Decline") { update(appointment, status: .cancelled) }
                                .buttonStyle(.bordered).tint(AppColor.primary)
                        }
                    } else if appointment.status == .confirmed {
                        Button("Mark completed") { update(appointment, status: .completed) }
                            .buttonStyle(.bordered).tint(AppColor.success)
                    }
                }
                .padding(.vertical, Spacing.xs)
            }
        }
        .navigationTitle("Appointments")
        .navigationBarTitleDisplayMode(.inline)
    }

    private func update(_ appointment: CareAppointment, status: AppointmentStatus) {
        Task { try? await appState.updateAppointment(appointment, status: status) }
    }
}

struct ProviderPracticeView: View {
    private enum SectionTab: String, CaseIterable, Identifiable {
        case services = "Services", hours = "Hours", doctors = "Doctors", reviews = "Reviews"
        var id: String { rawValue }
    }

    @Environment(AppState.self) private var appState
    @State private var tab: SectionTab = .services
    @State private var newDoctor = ""
    @State private var acceptsEmergencyEnquiries = true
    @State private var replyReview: PublicReview?
    @State private var replyText = ""
    @State private var scheduleMonth = Date()
    @State private var selectedScheduleDate = Date().addingTimeInterval(86_400)
    @State private var closureReason = ""
    @State private var isUpdatingSchedule = false
    @State private var scheduleError: String?

    var body: some View {
        VStack(spacing: 0) {
            Picker("Practice settings", selection: $tab) {
                ForEach(SectionTab.allCases) { item in
                    Text(item == .doctors && appState.providerAccountProfile.applicationType == .professional
                         ? "Profile" : item.rawValue)
                        .tag(item)
                }
            }
            .pickerStyle(.segmented)
            .padding(Spacing.md)

            Form {
                switch tab {
                case .services: servicesSection
                case .hours: hoursSection
                case .doctors: doctorsSection
                case .reviews: reviewsSection
                }
            }
        }
        .navigationTitle("Practice")
        .navigationBarTitleDisplayMode(.inline)
        .task { await appState.loadManagedAvailability() }
        .alert("Reply to review", isPresented: Binding(
            get: { replyReview != nil },
            set: { if !$0 { replyReview = nil; replyText = "" } }
        )) {
            TextField("Public reply", text: $replyText)
            Button("Post") { replyReview = nil; replyText = "" }
                .disabled(replyText.trimmingCharacters(in: .whitespaces).isEmpty)
            Button("Cancel", role: .cancel) { replyReview = nil; replyText = "" }
        }
    }

    private var servicesSection: some View {
        Section {
            ForEach(CareServiceGroup.allCases) { group in
                DisclosureGroup(group.rawValue) {
                    ForEach(CareService.allCases.filter { $0.group == group }) { service in
                        Toggle(isOn: Binding(
                            get: { appState.providerServices.contains(service) },
                            set: { enabled in
                                if enabled { appState.providerServices.insert(service) }
                                else { appState.providerServices.remove(service) }
                            }
                        )) {
                            Label(service.rawValue, systemImage: service.icon)
                        }
                    }
                }
            }
        } header: { Text("Services patients can discover") }
    }

    private var hoursSection: some View {
        Group {
            Section(appState.providerAccountProfile.applicationType == .facility ? "Operating hours" : "Consultation availability") {
                TextField("e.g. Monday–Saturday · 8 AM–8 PM", text: Binding(
                    get: { appState.providerHours }, set: { appState.providerHours = $0 }
                ), axis: .vertical)
                Toggle("Open 24 hours", isOn: Binding(
                    get: { appState.providerAccountProfile.open24Hours },
                    set: { appState.providerAccountProfile.open24Hours = $0 }
                ))
                Toggle("Emergency services available", isOn: Binding(
                    get: { appState.providerAccountProfile.emergencyAvailable },
                    set: { appState.providerAccountProfile.emergencyAvailable = $0 }
                ))
                Toggle("Accept emergency enquiries", isOn: $acceptsEmergencyEnquiries)
            }

            Section("Appointment availability") {
                MonthCalendarView(
                    month: $scheduleMonth,
                    selection: $selectedScheduleDate,
                    unavailableDates: Set(appState.providerUnavailableDates.map(\.date)),
                    allowsUnavailableSelection: true
                )

                if let closedDate = selectedUnavailableDate {
                    Label(closedDate.reason ?? "Closed for appointments", systemImage: "calendar.badge.exclamationmark")
                        .foregroundStyle(AppColor.primary)
                    Button("Make this date available") {
                        updateClosedDate(closedDate)
                    }
                    .disabled(isUpdatingSchedule)
                } else {
                    TextField("Reason (optional)", text: $closureReason)
                    Button("Mark this date unavailable") {
                        updateClosedDate(nil)
                    }
                    .disabled(isUpdatingSchedule)
                }

                if isUpdatingSchedule {
                    ProgressView("Updating calendar…")
                }
                if let scheduleError {
                    Label(scheduleError, systemImage: "exclamationmark.circle.fill")
                        .font(AppFont.bodyMd)
                        .foregroundStyle(AppColor.primary)
                }
            }
        }
    }

    private var selectedUnavailableDate: ProviderUnavailableDate? {
        let selectedKey = CalendarDateFormatting.key(for: selectedScheduleDate)
        return appState.providerUnavailableDates.first { $0.date == selectedKey }
    }

    private func updateClosedDate(_ existing: ProviderUnavailableDate?) {
        isUpdatingSchedule = true
        scheduleError = nil
        Task {
            do {
                if let existing {
                    try await appState.reopenProviderDate(existing)
                } else {
                    try await appState.closeProviderDate(selectedScheduleDate, reason: closureReason)
                    closureReason = ""
                }
                await MainActor.run { isUpdatingSchedule = false }
            } catch {
                await MainActor.run {
                    isUpdatingSchedule = false
                    scheduleError = error.localizedDescription
                }
            }
        }
    }

    private var doctorsSection: some View {
        Group {
            if appState.providerAccountProfile.applicationType == .facility {
                Section("Clinical team") {
                    ForEach(appState.providerDoctors, id: \.self) { doctor in
                        Label(doctor, systemImage: "stethoscope")
                    }
                }
                Section("Add clinician") {
                    TextField("Clinician's full name", text: $newDoctor)
                    Button("Add clinician") {
                        let clean = newDoctor.trimmingCharacters(in: .whitespaces)
                        guard !clean.isEmpty else { return }
                        appState.providerDoctors.append(clean)
                        newDoctor = ""
                    }
                    .disabled(newDoctor.trimmingCharacters(in: .whitespaces).isEmpty)
                }
            } else {
                Section("Public professional profile") {
                    TextField("Specialty", text: Binding(
                        get: { appState.providerAccountProfile.specialty },
                        set: { appState.providerAccountProfile.specialty = $0 }
                    ))
                    LabeledContent("Verified workplace", value: appState.providerAccountProfile.workplace)
                    LabeledContent("Registration status", value: appState.providerAccountProfile.registrationStatus)
                }
            }
        }
    }

    private var reviewsSection: some View {
        Section("Recent patient reviews") {
            ForEach(MockData.publicReviews) { review in
                VStack(alignment: .leading, spacing: 5) {
                    HStack { Text(review.author).font(AppFont.labelLg); Spacer(); StarRow(rating: Double(review.rating), size: 12) }
                    Text(review.text).font(AppFont.bodyMd).foregroundStyle(AppColor.onSurfaceVariant)
                    Button("Reply") { replyReview = review }
                        .font(AppFont.labelLg).foregroundStyle(AppColor.primary)
                }
                .padding(.vertical, Spacing.xs)
            }
        }
    }
}
