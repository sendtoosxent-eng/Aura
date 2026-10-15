//
//  NextOfKinStepView.swift
//  AuraHealth
//
//  Created by Osxent on 12/10/2026.
//

import SwiftUI
import Observation

struct NextOfKinStepView: View {
    @Bindable var vm: RegistrationViewModel

    var body: some View {
        VStack(alignment: .leading, spacing: Spacing.lg) {
            InfoNote(text: "This person may be contacted during an emergency when you cannot speak for yourself.")

            LabeledField("Full name", error: vm.showErrors ? vm.kinNameError : nil) {
                TextField("First and last name", text: $vm.kinName)
                    .textContentType(.name)
                    .inputBox(hasError: vm.showErrors && vm.kinNameError != nil)
            }

            LabeledField("Relationship", error: vm.showErrors ? vm.kinRelationshipError : nil) {
                Picker("Relationship", selection: $vm.kinRelationship) {
                    Text("Select relationship").tag(nil as Relationship?)
                    ForEach(Relationship.allCases) { relationship in
                        Text(relationship.rawValue).tag(relationship as Relationship?)
                    }
                }
                .pickerStyle(.menu)
                .tint(AppColor.onSurface)
                .inputBox(hasError: vm.showErrors && vm.kinRelationshipError != nil)
            }

            LabeledField("Phone number", error: vm.showErrors ? vm.kinPhoneError : nil) {
                HStack(spacing: Spacing.sm) {
                    Text("+256")
                        .foregroundStyle(AppColor.onSurfaceVariant)
                    TextField("770 000 000", text: $vm.kinPhoneDigits)
                        .keyboardType(.phonePad)
                        .textContentType(.telephoneNumber)
                }
                .inputBox(hasError: vm.showErrors && vm.kinPhoneError != nil)
            }

            Toggle(isOn: $vm.shareWithProviders) {
                VStack(alignment: .leading, spacing: Spacing.xs) {
                    Text("Emergency access")
                        .font(AppFont.labelLg)
                        .foregroundStyle(AppColor.onSurface)
                    Text("Allow healthcare providers to view this contact in an emergency.")
                        .font(AppFont.bodyMd)
                        .foregroundStyle(AppColor.onSurfaceVariant)
                }
            }
            .tint(AppColor.primary)
            .padding(Spacing.md)
            .cardStyle()

            if vm.showErrors, let error = vm.consentError {
                Label(error, systemImage: "exclamationmark.circle.fill")
                    .font(AppFont.labelSm)
                    .foregroundStyle(AppColor.primary)
            }
        }
    }
}

#Preview {
    ScrollView {
        NextOfKinStepView(vm: RegistrationViewModel())
            .padding()
    }
    .background(AppColor.background)
}
