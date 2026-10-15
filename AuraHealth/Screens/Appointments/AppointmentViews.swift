import SwiftUI

struct AppointmentsView: View {
    @Environment(AppState.self) private var appState
    @State private var showingHistory = false

    private var visibleAppointments: [CareAppointment] {
        appState.appointments.filter {
            showingHistory ? [.completed, .cancelled].contains($0.status) : ![.completed, .cancelled].contains($0.status)
        }
    }

    var body: some View {
        ScrollView {
            VStack(spacing: Spacing.md) {
                Picker("Appointments", selection: $showingHistory) {
                    Text("Upcoming").tag(false)
                    Text("History").tag(true)
                }
                .pickerStyle(.segmented)

                if visibleAppointments.isEmpty {
                    ContentUnavailableView(
                        showingHistory ? "No appointment history" : "No upcoming appointments",
                        systemImage: "calendar.badge.clock",
                        description: Text("Book an appointment from a provider's profile.")
                    )
                    .padding(.top, Spacing.xl)
                } else {
                    ForEach(visibleAppointments) { appointment in
                        appointmentCard(appointment)
                    }
                }
            }
            .padding(Spacing.md)
            .readableWidth()
        }
        .background(AppColor.background.ignoresSafeArea())
        .redInlineTitle("My Appointments")
    }

    private func appointmentCard(_ appointment: CareAppointment) -> some View {
        VStack(alignment: .leading, spacing: Spacing.md) {
            HStack(alignment: .top) {
                VStack(alignment: .leading, spacing: 4) {
                    Text(appointment.providerName)
                        .font(AppFont.headlineMd)
                        .foregroundStyle(AppColor.onSurface)
                    Text(appointment.doctorName)
                        .font(AppFont.bodyMd)
                        .foregroundStyle(AppColor.primary)
                }
                Spacer()
                Text(appointment.status.rawValue)
                    .font(AppFont.labelSm)
                    .foregroundStyle(appointment.status == .confirmed ? AppColor.success : AppColor.primary)
                    .padding(.horizontal, 10)
                    .padding(.vertical, 5)
                    .background(appointment.status == .confirmed ? AppColor.successContainer : AppColor.primaryTint, in: Capsule())
            }

            Label(appointment.service, systemImage: "cross.case")
            Label(appointment.date.formatted(date: .abbreviated, time: .omitted) + " · " + appointment.time,
                  systemImage: "calendar")
            if !appointment.reason.isEmpty {
                Label(appointment.reason, systemImage: "text.bubble")
            }

            HStack(spacing: Spacing.sm) {
                NavigationLink(value: Route.chat(appointment.doctorName)) {
                    Label("Message", systemImage: "message.fill")
                        .font(AppFont.labelLg)
                        .frame(maxWidth: .infinity, minHeight: 44)
                        .background(AppColor.primaryTint, in: RoundedRectangle(cornerRadius: Radius.sm))
                }
                if !showingHistory {
                    Button("Cancel") { appState.cancelAppointment(appointment) }
                        .font(AppFont.labelLg)
                        .foregroundStyle(AppColor.primary)
                        .frame(maxWidth: .infinity, minHeight: 44)
                        .overlay(RoundedRectangle(cornerRadius: Radius.sm).stroke(AppColor.primary))
                }
            }
        }
        .font(AppFont.bodyMd)
        .foregroundStyle(AppColor.onSurfaceVariant)
        .padding(Spacing.md)
        .cardStyle(radius: Radius.lg)
    }
}

struct BookAppointmentView: View {
    let provider: Provider
    let preselectedDoctor: CareDoctor?

    @Environment(AppState.self) private var appState
    @Environment(\.dismiss) private var dismiss
    @State private var service = ""
    @State private var doctor: String
    @State private var date = Date().addingTimeInterval(86_400)
    @State private var displayedMonth = Date()
    @State private var unavailableDates: [ProviderUnavailableDate] = []
    @State private var isLoadingAvailability = false
    @State private var time = "9:00 AM"
    @State private var reason = ""
    @State private var showConfirmation = false
    @State private var isSubmitting = false
    @State private var bookingError: String?

    private let times = ["9:00 AM", "10:30 AM", "12:00 PM", "2:30 PM", "4:00 PM"]

    private var unavailableDateKeys: Set<String> {
        Set(unavailableDates.map(\.date))
    }

    init(provider: Provider, doctor: CareDoctor? = nil) {
        self.provider = provider
        self.preselectedDoctor = doctor
        let facilityDoctors = MockData.doctors.filter { $0.provider.id == provider.id }
        _doctor = State(initialValue: doctor?.name ?? facilityDoctors.first?.name ?? "Any available clinician")
    }

    private var doctors: [String] {
        let names = appState.doctors(at: provider).map(\.name)
        return names + ["Any available clinician"]
    }

    var body: some View {
        Form {
            Section("Provider") {
                LabeledContent("Facility", value: provider.name)
                if let preselectedDoctor {
                    LabeledContent("Specialist", value: preselectedDoctor.specialty)
                }
                Picker("Service", selection: $service) {
                    Text("Select a service").tag("")
                    ForEach(provider.services, id: \.self) { Text($0).tag($0) }
                }
                Picker("Doctor", selection: $doctor) {
                    ForEach(doctors, id: \.self) { Text($0).tag($0) }
                }
            }

            Section("Date and time") {
                MonthCalendarView(
                    month: $displayedMonth,
                    selection: $date,
                    unavailableDates: unavailableDateKeys
                )
                if isLoadingAvailability {
                    HStack {
                        ProgressView()
                        Text("Checking provider availability…")
                    }
                    .font(AppFont.bodyMd)
                }
                Picker("Available time", selection: $time) {
                    ForEach(times, id: \.self) { Text($0).tag($0) }
                }
            }

            Section("Reason for visit") {
                TextField("Briefly describe what you need help with", text: $reason, axis: .vertical)
                    .lineLimit(3...6)
            }

            Section {
                Label("The facility will confirm your request. For an emergency, use Emergency Mode instead.",
                      systemImage: "info.circle")
                    .font(AppFont.bodyMd)
                    .foregroundStyle(AppColor.onSurfaceVariant)
            }

            if let bookingError {
                Section {
                    Label(bookingError, systemImage: "exclamationmark.circle.fill")
                        .foregroundStyle(AppColor.primary)
                }
            }
        }
        .navigationTitle(preselectedDoctor == nil ? "Book Appointment" : "Book Doctor")
        .navigationBarTitleDisplayMode(.inline)
        .safeAreaInset(edge: .bottom) {
            PrimaryButton(
                title: isSubmitting ? "Sending request…" : "Request Appointment",
                icon: "calendar.badge.plus",
                isEnabled: !service.isEmpty
                    && !isSubmitting
                    && !isLoadingAvailability
                    && !unavailableDateKeys.contains(CalendarDateFormatting.key(for: date))
            ) {
                submitAppointment()
            }
            .padding(Spacing.md)
            .background(AppColor.surface)
        }
        .alert("Appointment requested", isPresented: $showConfirmation) {
            Button("Done") { dismiss() }
        } message: {
            Text("\(doctor) at \(provider.name) will confirm your selected time.")
        }
        .task { await loadAvailability() }
        .onChange(of: displayedMonth) { _, _ in Task { await loadAvailability() } }
    }

    private func submitAppointment() {
        bookingError = nil
        isSubmitting = true
        let selectedDoctor = preselectedDoctor ?? appState.doctors(at: provider).first { $0.name == doctor }
        Task {
            do {
                try await appState.requestAppointment(
                    provider: provider,
                    doctor: selectedDoctor,
                    service: service,
                    date: date,
                    time: time,
                    reason: reason
                )
                await MainActor.run {
                    isSubmitting = false
                    showConfirmation = true
                }
            } catch {
                await MainActor.run {
                    isSubmitting = false
                    bookingError = error.localizedDescription
                }
            }
        }
    }

    @MainActor
    private func loadAvailability() async {
        isLoadingAvailability = true
        defer { isLoadingAvailability = false }
        do {
            unavailableDates = try await appState.unavailableDates(for: provider, month: displayedMonth)
            if unavailableDateKeys.contains(CalendarDateFormatting.key(for: date)) {
                date = CalendarDateFormatting.firstAvailableDate(
                    in: displayedMonth,
                    unavailableDates: unavailableDateKeys
                ) ?? date
            }
        } catch {
            bookingError = error.localizedDescription
        }
    }
}

struct MonthCalendarView: View {
    @Binding var month: Date
    @Binding var selection: Date
    let unavailableDates: Set<String>
    var allowsUnavailableSelection = false
    var allowsPastDates = false

    private let calendar = Calendar(identifier: .gregorian)
    private let columns = Array(repeating: GridItem(.flexible(), spacing: 4), count: 7)

    private var monthStart: Date {
        calendar.date(from: calendar.dateComponents([.year, .month], from: month)) ?? month
    }

    private var calendarDays: [Date?] {
        let leading = calendar.component(.weekday, from: monthStart) - 1
        let count = calendar.range(of: .day, in: .month, for: monthStart)?.count ?? 30
        return Array(repeating: nil, count: leading) + (0..<count).map {
            calendar.date(byAdding: .day, value: $0, to: monthStart)
        }
    }

    var body: some View {
        VStack(spacing: Spacing.sm) {
            HStack {
                Button { moveMonth(-1) } label: { Image(systemName: "chevron.left") }
                    .disabled(!allowsPastDates && isCurrentMonth)
                Spacer()
                Text(monthStart.formatted(.dateTime.month(.wide).year()))
                    .font(AppFont.headlineMd)
                Spacer()
                Button { moveMonth(1) } label: { Image(systemName: "chevron.right") }
            }
            .buttonStyle(.plain)
            .foregroundStyle(AppColor.primary)

            LazyVGrid(columns: columns, spacing: 7) {
                ForEach(calendar.veryShortWeekdaySymbols, id: \.self) { symbol in
                    Text(symbol).font(AppFont.labelSm).foregroundStyle(AppColor.onSurfaceVariant)
                }
                ForEach(Array(calendarDays.enumerated()), id: \.offset) { _, day in
                    if let day {
                        dayButton(day)
                    } else {
                        Color.clear.frame(height: 38)
                    }
                }
            }

            HStack(spacing: Spacing.md) {
                Label("Selected", systemImage: "circle.fill").foregroundStyle(AppColor.primary)
                Label("Unavailable", systemImage: "xmark.circle.fill").foregroundStyle(AppColor.secondary)
            }
            .font(AppFont.labelSm)
            .frame(maxWidth: .infinity, alignment: .leading)
        }
        .padding(.vertical, Spacing.sm)
    }

    private func dayButton(_ day: Date) -> some View {
        let key = CalendarDateFormatting.key(for: day)
        let unavailable = unavailableDates.contains(key)
        let past = calendar.startOfDay(for: day) < calendar.startOfDay(for: Date())
        let selected = calendar.isDate(day, inSameDayAs: selection)
        let disabled = (!allowsPastDates && past) || (unavailable && !allowsUnavailableSelection)

        return Button {
            selection = day
        } label: {
            ZStack {
                Circle().fill(selected ? AppColor.primary : Color.clear)
                Text("\(calendar.component(.day, from: day))")
                    .font(AppFont.bodyMd)
                    .foregroundStyle(selected ? .white : disabled ? AppColor.outline : AppColor.onSurface)
                if unavailable {
                    Circle().stroke(AppColor.secondary, lineWidth: 1.5)
                }
            }
            .frame(height: 38)
        }
        .buttonStyle(.plain)
        .disabled(disabled)
        .accessibilityLabel(day.formatted(date: .complete, time: .omitted))
        .accessibilityValue(unavailable ? "Unavailable" : selected ? "Selected" : "Available")
    }

    private var isCurrentMonth: Bool {
        calendar.isDate(monthStart, equalTo: Date(), toGranularity: .month)
    }

    private func moveMonth(_ offset: Int) {
        month = calendar.date(byAdding: .month, value: offset, to: monthStart) ?? month
    }
}

enum CalendarDateFormatting {
    private static let formatter: DateFormatter = {
        let formatter = DateFormatter()
        formatter.calendar = Calendar(identifier: .gregorian)
        formatter.locale = Locale(identifier: "en_US_POSIX")
        formatter.dateFormat = "yyyy-MM-dd"
        return formatter
    }()

    static func key(for date: Date) -> String {
        formatter.string(from: date)
    }

    static func firstAvailableDate(in month: Date, unavailableDates: Set<String>) -> Date? {
        let calendar = Calendar(identifier: .gregorian)
        let start = calendar.date(from: calendar.dateComponents([.year, .month], from: month)) ?? month
        let dayCount = calendar.range(of: .day, in: .month, for: start)?.count ?? 30
        return (0..<dayCount)
            .compactMap { calendar.date(byAdding: .day, value: $0, to: start) }
            .first {
                calendar.startOfDay(for: $0) >= calendar.startOfDay(for: Date())
                    && !unavailableDates.contains(key(for: $0))
            }
    }
}

#Preview {
    NavigationStack { AppointmentsView().appDestinations() }
        .environment(AppState())
}
