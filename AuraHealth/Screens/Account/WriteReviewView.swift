import SwiftUI
import PhotosUI

// SCREEN 12 — Write a Review (design: write_a_review)
// Star rating, text box, optional photo, submit.
struct WriteReviewView: View {
    let provider: Provider

    @Environment(\.dismiss) private var dismiss

    @State private var rating = 0
    @State private var text = ""
    @State private var photoItem: PhotosPickerItem?
    @State private var photoImage: Image?
    @State private var showThanks = false

    // Words shown under the stars, indexed by rating (0 = nothing picked yet).
    private let ratingWords = ["Tap to rate", "Poor", "Fair", "Good", "Very good", "Excellent"]

    var body: some View {
        ScrollView {
            VStack(alignment: .leading, spacing: Spacing.lg) {
                providerHeader
                ratingCard

                VStack(alignment: .leading, spacing: Spacing.sm) {
                    Text("Your Review").font(AppFont.labelLg).foregroundStyle(AppColor.onSurface)
                    reviewEditor
                }

                VStack(alignment: .leading, spacing: Spacing.sm) {
                    Text("Photos (Optional)").font(AppFont.labelLg).foregroundStyle(AppColor.onSurface)
                    photoPicker
                }

                Label("Reviews are public and include your name. Please follow our community standards and avoid sharing sensitive medical history.",
                      systemImage: "info.circle")
                    .font(AppFont.bodyMd).foregroundStyle(AppColor.onSurfaceVariant)
                    .padding(Spacing.md)
                    .frame(maxWidth: .infinity, alignment: .leading)
                    .background(AppColor.surfaceLow)
                    .clipShape(RoundedRectangle(cornerRadius: Radius.sm))
            }
            .padding(Spacing.md)
            .readableWidth()
        }
        .scrollDismissesKeyboard(.interactively)
        .background(AppColor.background.ignoresSafeArea())
        .redInlineTitle("Write a Review")
        // Submit button stays pinned to the bottom, above the keyboard/home bar.
        .safeAreaInset(edge: .bottom) {
            PrimaryButton(title: "SUBMIT REVIEW", icon: "paperplane", isEnabled: rating > 0) {
                showThanks = true
            }
            .padding(Spacing.md)
            .background(AppColor.background)
        }
        .alert("Thank you!", isPresented: $showThanks) {
            Button("Done") { dismiss() }
        } message: {
            Text("Your \(rating)-star review of \(provider.name) was submitted (demo — nothing is saved yet).")
        }
        // LEARN: PhotosPicker gives a photo WITHOUT asking for photo-library
        // permission (iOS shows its own secure picker). We load the chosen item
        // asynchronously and turn it into an Image to display.
        .onChange(of: photoItem) { _, item in
            Task {
                if let data = try? await item?.loadTransferable(type: Data.self),
                   let uiImage = UIImage(data: data) {
                    photoImage = Image(uiImage: uiImage)
                } else {
                    photoImage = nil
                }
            }
        }
    }

    // MARK: Pieces

    private var providerHeader: some View {
        HStack(spacing: Spacing.md) {
            ProviderThumbnail(provider: provider).frame(width: 64, height: 64)
            VStack(alignment: .leading, spacing: 2) {
                Text(provider.name).font(AppFont.headlineMd).foregroundStyle(AppColor.onSurface)
                Text("\(provider.type.rawValue) • \(provider.address)")
                    .font(AppFont.bodyMd).foregroundStyle(AppColor.onSurfaceVariant)
            }
        }
    }

    private var ratingCard: some View {
        VStack(spacing: Spacing.md) {
            Text("HOW WAS YOUR EXPERIENCE?")
                .font(AppFont.labelLg).tracking(1).foregroundStyle(AppColor.onSurface)
            InteractiveStars(rating: $rating)
            Text(ratingWords[rating]).font(AppFont.bodyLg).foregroundStyle(AppColor.onSurfaceVariant)
        }
        .frame(maxWidth: .infinity)
        .padding(Spacing.lg)
        .cardStyle(radius: Radius.lg)
    }

    /// TextEditor has no built-in placeholder, so we draw one on top while empty.
    private var reviewEditor: some View {
        TextEditor(text: $text)
            .font(AppFont.bodyLg)
            .scrollContentBackground(.hidden)
            .padding(Spacing.sm)
            .frame(minHeight: 140)
            .background(AppColor.surface)
            .clipShape(RoundedRectangle(cornerRadius: Radius.sm))
            .overlay(RoundedRectangle(cornerRadius: Radius.sm).stroke(AppColor.outline, lineWidth: 1))
            .overlay(alignment: .topLeading) {
                if text.isEmpty {
                    Text("Share details of your experience to help others…")
                        .font(AppFont.bodyLg).foregroundStyle(AppColor.outlineVariant)
                        .padding(.horizontal, Spacing.md).padding(.vertical, Spacing.md)
                        .allowsHitTesting(false)   // taps pass through to the editor
                }
            }
    }

    private var photoPicker: some View {
        PhotosPicker(selection: $photoItem, matching: .images) {
            Group {
                if let photoImage {
                    photoImage.resizable().scaledToFill()
                } else {
                    VStack(spacing: 6) {
                        Image(systemName: "camera.badge.ellipsis").font(.title)
                        Text("Add Photo").font(AppFont.bodyMd)
                    }
                    .foregroundStyle(AppColor.onSurfaceVariant)
                }
            }
            .frame(width: 110, height: 110)
            .background(AppColor.surface)
            .clipShape(RoundedRectangle(cornerRadius: Radius.md))
            .overlay(
                RoundedRectangle(cornerRadius: Radius.md)
                    .stroke(AppColor.outlineVariant, style: StrokeStyle(lineWidth: 2, dash: [6, 4]))
            )
        }
    }
}

#Preview {
    NavigationStack { WriteReviewView(provider: MockData.providers[0]) }
        .environment(AppState())
}
