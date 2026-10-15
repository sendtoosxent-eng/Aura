import SwiftUI
import PhotosUI
import Observation

struct BioStepView: View {
    @Bindable var vm: RegistrationViewModel
    @State private var profilePhotoItem: PhotosPickerItem?
    @State private var coverPhotoItem: PhotosPickerItem?

    var body: some View {
        VStack(alignment: .leading, spacing: Spacing.lg) {
            photoSection

            LabeledField("Full name", error: shown(vm.nameError)) {
                TextField("First and last name", text: $vm.fullName)
                    .textContentType(.name)
                    .textInputAutocapitalization(.words)
                    .inputBox(hasError: hasVisibleError(vm.nameError))
            }

            LabeledField("Phone number", error: shown(vm.phoneError), hint: "Use a Ugandan mobile number") {
                HStack(spacing: Spacing.sm) {
                    Text("+256")
                        .foregroundStyle(AppColor.onSurfaceVariant)

                    TextField("770 000 000", text: phoneBinding)
                        .keyboardType(.phonePad)
                        .textContentType(.telephoneNumber)
                }
                .inputBox(hasError: hasVisibleError(vm.phoneError))
            }

            LabeledField("Email address", error: shown(vm.emailError)) {
                TextField("name@example.com", text: $vm.email)
                    .keyboardType(.emailAddress)
                    .textContentType(.emailAddress)
                    .textInputAutocapitalization(.never)
                    .autocorrectionDisabled()
                    .inputBox(hasError: hasVisibleError(vm.emailError))
            }

            LabeledField("National ID number (NIN)", error: shown(vm.ninError), hint: "14 characters, for example CM12345678ABCD") {
                TextField("CM12345678ABCD", text: ninBinding)
                    .textInputAutocapitalization(.characters)
                    .autocorrectionDisabled()
                    .inputBox(hasError: hasVisibleError(vm.ninError))
            }

            LabeledField("Password", error: shown(vm.passwordError)) {
                SecureField("Create a strong password", text: $vm.password)
                    .textContentType(.newPassword)
                    .inputBox(hasError: hasVisibleError(vm.passwordError))
            }

            LabeledField("Confirm password", error: shown(vm.passwordConfirmationError)) {
                SecureField("Repeat your password", text: $vm.passwordConfirmation)
                    .textContentType(.newPassword)
                    .inputBox(hasError: hasVisibleError(vm.passwordConfirmationError))
            }

            LabeledField("Username", hint: "Generated automatically from your name") {
                HStack(spacing: Spacing.sm) {
                    Text(vm.username.isEmpty ? "Enter your full name first" : vm.username)
                        .foregroundStyle(vm.username.isEmpty ? AppColor.onSurfaceVariant : AppColor.onSurface)
                        .frame(maxWidth: .infinity, alignment: .leading)

                    Button {
                        vm.regenerateUsername()
                    } label: {
                        Image(systemName: "arrow.clockwise")
                            .foregroundStyle(AppColor.primary)
                            .frame(width: 44, height: 44)
                    }
                    .disabled(vm.username.isEmpty)
                    .accessibilityLabel("Generate another username")
                }
                .inputBox()
            }
        }
        .onChange(of: profilePhotoItem) { _, item in
            loadImage(from: item) { vm.profileImage = $0 }
        }
        .onChange(of: coverPhotoItem) { _, item in
            loadImage(from: item) { vm.coverImage = $0 }
        }
    }

    private var photoSection: some View {
        VStack(alignment: .leading, spacing: Spacing.sm) {
            Text("Profile photos")
                .font(AppFont.labelLg)
                .foregroundStyle(AppColor.onSurface)

            ZStack(alignment: .bottomLeading) {
                PhotosPicker(selection: $coverPhotoItem, matching: .images) {
                    ZStack {
                        if let image = vm.coverImage {
                            Image(uiImage: image)
                                .resizable()
                                .scaledToFill()
                        } else {
                            AppColor.surfaceContainer
                            VStack(spacing: Spacing.xs) {
                                Image(systemName: "photo.badge.plus")
                                    .font(.title2)
                                Text("Add cover photo")
                                    .font(AppFont.labelLg)
                            }
                            .foregroundStyle(AppColor.onSurfaceVariant)
                        }
                    }
                    .frame(maxWidth: .infinity)
                    .frame(height: 150)
                    .clipped()
                    .clipShape(RoundedRectangle(cornerRadius: Radius.md))
                    .overlay(
                        RoundedRectangle(cornerRadius: Radius.md)
                            .stroke(hasVisibleError(vm.coverError) ? AppColor.primary : AppColor.outlineVariant, lineWidth: hasVisibleError(vm.coverError) ? 2 : 1)
                    )
                }

                PhotosPicker(selection: $profilePhotoItem, matching: .images) {
                    ZStack {
                        Circle().fill(AppColor.surface)

                        if let image = vm.profileImage {
                            Image(uiImage: image)
                                .resizable()
                                .scaledToFill()
                                .frame(width: 92, height: 92)
                                .clipShape(Circle())
                        } else {
                            Image(systemName: "person.crop.circle.badge.plus")
                                .font(.system(size: 42))
                                .foregroundStyle(AppColor.primary)
                        }
                    }
                    .frame(width: 100, height: 100)
                    .clipShape(Circle())
                    .overlay(
                        Circle().stroke(hasVisibleError(vm.photoError) ? AppColor.primary : AppColor.surface, lineWidth: 4)
                    )
                    .shadow(color: .black.opacity(0.12), radius: 4, y: 2)
                }
                .offset(x: Spacing.md, y: 42)
            }
            .padding(.bottom, 42)

            if vm.showErrors, let error = vm.coverError ?? vm.photoError {
                Label(error, systemImage: "exclamationmark.circle.fill")
                    .font(AppFont.labelSm)
                    .foregroundStyle(AppColor.primary)
            } else {
                Text("Tap the cover or profile image to choose a photo.")
                    .font(AppFont.labelSm)
                    .foregroundStyle(AppColor.onSurfaceVariant)
            }
        }
    }

    private var phoneBinding: Binding<String> {
        Binding(
            get: { vm.phoneDigits },
            set: { vm.phoneDigits = String($0.filter(\.isNumber).prefix(9)) }
        )
    }

    private var ninBinding: Binding<String> {
        Binding(
            get: { vm.nin },
            set: { vm.nin = String(Validators.normalizeNIN($0).prefix(14)) }
        )
    }

    private func shown(_ error: String?) -> String? {
        vm.showErrors ? error : nil
    }

    private func hasVisibleError(_ error: String?) -> Bool {
        vm.showErrors && error != nil
    }

    private func loadImage(from item: PhotosPickerItem?, assign: @escaping (UIImage) -> Void) {
        guard let item else { return }

        Task {
            guard let data = try? await item.loadTransferable(type: Data.self),
                  let image = UIImage(data: data) else { return }
            await MainActor.run { assign(image) }
        }
    }
}

#Preview {
    ScrollView {
        BioStepView(vm: RegistrationViewModel())
            .padding()
    }
    .background(AppColor.background)
}
