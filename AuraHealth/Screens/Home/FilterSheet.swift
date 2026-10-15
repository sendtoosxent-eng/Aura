import SwiftUI

// SCREEN 6 — Filter sheet (design: filter_filters)
// Slides up over Home/Map. Edits a DRAFT copy; "Apply" saves it to AppState.
struct FilterSheet: View {
    @Environment(AppState.self) private var appState
    @Environment(\.dismiss) private var dismiss

    // LEARN: We edit a private copy (`draft`) instead of the real filters, so
    // closing the sheet with ✕ cancels the changes. Only "Apply" commits them.
    @State private var draft = ProviderFilters()

    // RESPONSIVE: two flexible columns = two equal tiles at any screen width.
    private let columns = [
        GridItem(.flexible(), spacing: Spacing.md),
        GridItem(.flexible(), spacing: Spacing.md)
    ]

    var body: some View {
        VStack(spacing: 0) {
            header
            Divider()

            ScrollView {
                VStack(alignment: .leading, spacing: Spacing.lg) {
                    Text("Service Type").font(AppFont.bodyLg).foregroundStyle(AppColor.onSurfaceVariant)

                    LazyVGrid(columns: columns, spacing: Spacing.md) {
                        ForEach(ProviderType.allCases) { type in typeTile(type) }
                    }

                    VStack(alignment: .leading, spacing: Spacing.md) {
                        Text("Condition or care needed")
                            .font(AppFont.bodyLg)
                            .foregroundStyle(AppColor.onSurfaceVariant)

                        Text("Choose what you need and we’ll show providers offering that care.")
                            .font(AppFont.bodyMd)
                            .foregroundStyle(AppColor.onSurfaceVariant)

                        ForEach(CareServiceGroup.allCases) { group in
                            VStack(alignment: .leading, spacing: Spacing.sm) {
                                Text(group.rawValue)
                                    .font(AppFont.labelLg)
                                    .foregroundStyle(AppColor.onSurface)

                                LazyVGrid(columns: columns, spacing: Spacing.sm) {
                                    ForEach(CareService.allCases.filter { $0.group == group }) { service in
                                        careServiceTile(service)
                                    }
                                }
                            }
                        }
                    }

                    VStack(spacing: Spacing.sm) {
                        Toggle(isOn: $draft.openNow) {
                            Label("Open Now", systemImage: "clock")
                        }
                        Toggle(isOn: $draft.open24h) {
                            Label("24-Hour Access", systemImage: "clock.badge.checkmark")
                        }
                    }
                    .font(AppFont.bodyLg)
                    .tint(AppColor.primary)

                    sliderBlock(
                        title: "Minimum Rating",
                        valueText: String(format: "%.1f+", draft.minRating),
                        value: $draft.minRating, range: 1...5, step: 0.5,
                        marks: ["1.0", "3.0", "5.0"]
                    )

                    sliderBlock(
                        title: "Distance Radius",
                        valueText: "\(Int(draft.radiusKm)) km",
                        value: $draft.radiusKm, range: 1...50, step: 1,
                        marks: ["0km", "25km", "50km"]
                    )
                }
                .padding(Spacing.md)
            }

            PrimaryButton(title: "Apply Filters") {
                appState.filters = draft
                dismiss()
            }
            .padding(Spacing.md)
        }
        .background(AppColor.surface)
        .onAppear { draft = appState.filters }          // start from current filters
        .presentationDetents([.large])
        .presentationDragIndicator(.visible)
        .presentationCornerRadius(Radius.xl)
    }

    // MARK: Pieces

    private var header: some View {
        HStack {
            Text("Filter Services").font(AppFont.headlineLg)
            Spacer()
            Button("Reset") { draft = ProviderFilters() }
                .font(AppFont.labelLg).foregroundStyle(AppColor.primary)
                .frame(minHeight: 44)
            Button { dismiss() } label: {
                Image(systemName: "xmark").font(.headline).foregroundStyle(AppColor.onSurface)
                    .frame(width: 44, height: 44)
            }
            .accessibilityLabel("Close")
        }
        .padding(.horizontal, Spacing.md)
        .padding(.top, Spacing.sm)
    }

    /// One checkbox tile (Hospital / Clinic / Pharmacy / Ambulance).
    private func typeTile(_ type: ProviderType) -> some View {
        let selected = draft.types.contains(type)
        return Button {
            draft.toggle(type)
        } label: {
            HStack(alignment: .top, spacing: Spacing.sm) {
                Image(systemName: selected ? "checkmark.square.fill" : "square")
                    .font(.title3)
                    .foregroundStyle(selected ? AppColor.primary : AppColor.outline)
                VStack(alignment: .leading, spacing: 6) {
                    Image(systemName: type.icon).foregroundStyle(AppColor.primary)
                    Text(type.rawValue).font(AppFont.bodyLg).foregroundStyle(AppColor.onSurface)
                }
                Spacer(minLength: 0)
            }
            .padding(Spacing.md)
            .frame(maxWidth: .infinity, minHeight: 80, alignment: .leading)
            .background(selected ? AppColor.primaryTint : AppColor.surfaceLow)
            .clipShape(RoundedRectangle(cornerRadius: Radius.md))
            .overlay(
                RoundedRectangle(cornerRadius: Radius.md)
                    .stroke(selected ? AppColor.primary : Color.clear, lineWidth: 1)
            )
        }
        .buttonStyle(.plain)
    }

    private func careServiceTile(_ service: CareService) -> some View {
        let selected = draft.careServices.contains(service)
        return Button {
            draft.toggle(service)
        } label: {
            HStack(spacing: Spacing.sm) {
                Image(systemName: service.icon)
                    .foregroundStyle(selected ? .white : AppColor.primary)
                    .frame(width: 24)
                Text(service.rawValue)
                    .font(AppFont.labelSm)
                    .foregroundStyle(selected ? .white : AppColor.onSurface)
                    .multilineTextAlignment(.leading)
                Spacer(minLength: 0)
                if selected {
                    Image(systemName: "checkmark.circle.fill")
                        .font(.caption)
                }
            }
            .padding(Spacing.sm)
            .frame(maxWidth: .infinity, minHeight: 58, alignment: .leading)
            .background(selected ? AppColor.primary : AppColor.surfaceLow)
            .clipShape(RoundedRectangle(cornerRadius: Radius.sm))
            .overlay(
                RoundedRectangle(cornerRadius: Radius.sm)
                    .stroke(selected ? AppColor.primary : AppColor.outlineVariant, lineWidth: 1)
            )
        }
        .buttonStyle(.plain)
    }

    private func sliderBlock(title: String, valueText: String, value: Binding<Double>,
                             range: ClosedRange<Double>, step: Double, marks: [String]) -> some View {
        VStack(spacing: Spacing.sm) {
            HStack {
                Text(title).font(AppFont.bodyLg).foregroundStyle(AppColor.onSurfaceVariant)
                Spacer()
                Text(valueText).font(AppFont.labelLg).foregroundStyle(AppColor.primary)
            }
            Slider(value: value, in: range, step: step).tint(AppColor.primary)
            HStack {
                ForEach(marks.indices, id: \.self) { i in
                    Text(marks[i])
                    if i < marks.count - 1 { Spacer() }
                }
            }
            .font(AppFont.labelSm).foregroundStyle(AppColor.secondary)
        }
    }
}

#Preview {
    Text("Home").sheet(isPresented: .constant(true)) { FilterSheet() }
        .environment(AppState())
}
