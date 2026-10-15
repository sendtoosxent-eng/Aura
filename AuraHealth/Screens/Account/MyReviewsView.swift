import SwiftUI

// SCREEN 11 — My Reviews (design: my_reviews)
// Summary card, filter chips, list of the user's reviews with edit / delete.
struct MyReviewsView: View {
    // LEARN: @State is private, view-owned data. When it changes, the view
    // redraws. Here the reviews are local fake data; deleting one removes it.
    @State private var reviews = MockData.myReviews
    @State private var filter: ReviewFilter = .all
    @State private var pendingDelete: Review?
    @State private var showEditAlert = false

    private var visible: [Review] {
        filter == .all ? reviews : reviews.filter { $0.category == filter }
    }

    private var average: Double {
        reviews.isEmpty ? 0 : Double(reviews.map(\.rating).reduce(0, +)) / Double(reviews.count)
    }

    var body: some View {
        ScrollView {
            VStack(alignment: .leading, spacing: Spacing.md) {
                summaryCard
                chips
                if visible.isEmpty {
                    Text("No reviews here yet.")
                        .font(AppFont.bodyLg).foregroundStyle(AppColor.onSurfaceVariant)
                        .frame(maxWidth: .infinity).padding(.top, Spacing.xl)
                }
                ForEach(visible) { review in reviewCard(review) }
            }
            .padding(Spacing.md)
            .readableWidth()
        }
        .background(AppColor.background.ignoresSafeArea())
        .redInlineTitle("My Reviews")
        // LEARN: Binding(get:set:) builds a custom two-way link. The dialog is
        // "shown" while pendingDelete has a value, and tapping outside it
        // (which sets the binding to false) clears pendingDelete.
        .confirmationDialog("Delete this review?",
                            isPresented: Binding(get: { pendingDelete != nil },
                                                 set: { if !$0 { pendingDelete = nil } }),
                            titleVisibility: .visible, presenting: pendingDelete) { review in
            Button("Delete", role: .destructive) {
                withAnimation { reviews.removeAll { $0.id == review.id } }
                pendingDelete = nil
            }
            Button("Cancel", role: .cancel) { pendingDelete = nil }
        } message: { review in
            Text("Your review of \(review.providerName) will be removed.")
        }
        .alert("Edit review", isPresented: $showEditAlert) {
            Button("OK", role: .cancel) {}
        } message: {
            Text("Editing reuses the Write a Review screen — try wiring it up yourself!")
        }
    }

    // MARK: Pieces

    private var summaryCard: some View {
        VStack(spacing: Spacing.sm) {
            HStack(alignment: .bottom) {
                VStack(alignment: .leading, spacing: 2) {
                    Text("Your overall impact").font(AppFont.bodyLg).foregroundStyle(AppColor.onSurfaceVariant)
                    Text(String(format: "%.1f", average))
                        .font(.system(size: 36, weight: .bold)).foregroundStyle(AppColor.primary)
                }
                Spacer()
                VStack(alignment: .trailing, spacing: 2) {
                    Text("Total Reviews").font(AppFont.labelLg).foregroundStyle(AppColor.onSurfaceVariant)
                    Text("\(reviews.count)").font(.system(size: 28, weight: .bold))
                }
            }
            // Progress bar: grey track + red fill sized by average/5.
            GeometryReader { geo in
                ZStack(alignment: .leading) {
                    Capsule().fill(AppColor.surfaceHigh)
                    Capsule().fill(AppColor.primary).frame(width: geo.size.width * average / 5)
                }
            }
            .frame(height: 8)
        }
        .padding(Spacing.md)
        .cardStyle(radius: Radius.lg)
    }

    private var chips: some View {
        ScrollView(.horizontal, showsIndicators: false) {
            HStack(spacing: Spacing.sm) {
                ForEach(ReviewFilter.allCases) { f in
                    Button { filter = f } label: {
                        Text(f.rawValue)
                            .font(AppFont.labelLg)
                            .foregroundStyle(filter == f ? Color.white : AppColor.primary)
                            .padding(.horizontal, Spacing.md).frame(minHeight: 44)
                            .background(filter == f ? AppColor.primary : AppColor.surface)
                            .clipShape(Capsule())
                            .overlay(Capsule().stroke(AppColor.outlineVariant, lineWidth: filter == f ? 0 : 1))
                    }
                }
            }
        }
        .scrollClipDisabled()
    }

    private func reviewCard(_ review: Review) -> some View {
        VStack(alignment: .leading, spacing: Spacing.sm) {
            HStack(alignment: .top, spacing: Spacing.md) {
                Image(systemName: review.category == .pharmacies ? "pills.fill" : "cross.case.fill")
                    .foregroundStyle(AppColor.primary)
                    .frame(width: 48, height: 48)
                    .background(AppColor.primaryTint).clipShape(RoundedRectangle(cornerRadius: Radius.sm))

                VStack(alignment: .leading, spacing: 2) {
                    Text(review.providerName).font(AppFont.labelLg).foregroundStyle(AppColor.onSurface)
                    Text(review.dateText).font(AppFont.bodyMd).foregroundStyle(AppColor.onSurfaceVariant)
                }
                Spacer()
                Button { showEditAlert = true } label: {
                    Image(systemName: "pencil").foregroundStyle(AppColor.onSurfaceVariant)
                        .frame(width: 40, height: 40)
                }
                .accessibilityLabel("Edit review")
                Button { pendingDelete = review } label: {
                    Image(systemName: "trash").foregroundStyle(AppColor.primary)
                        .frame(width: 40, height: 40)
                }
                .accessibilityLabel("Delete review")
            }

            StarRow(rating: Double(review.rating), size: 18)

            Text(review.text).font(AppFont.bodyLg).foregroundStyle(AppColor.onSurface)

            Label("Helpful to \(review.helpfulCount) \(review.helpfulCount == 1 ? "person" : "people")",
                  systemImage: "hand.thumbsup")
                .font(AppFont.bodyMd).foregroundStyle(AppColor.secondary)
        }
        .padding(Spacing.md)
        .frame(maxWidth: .infinity, alignment: .leading)
        .cardStyle(radius: Radius.lg)
    }
}

#Preview {
    NavigationStack { MyReviewsView() }.environment(AppState())
}
