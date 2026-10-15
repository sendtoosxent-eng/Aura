//
//  VitalsStepView.swift
//  AuraHealth
//
//  Created by Osxent on 12/10/2026.
//

import SwiftUI
import Observation

struct VitalsStepView: View {
    @Bindable var vm: RegistrationViewModel

    var body: some View {
        VStack(alignment: .leading, spacing: Spacing.lg) {
            InfoNote(text: "All fields on this step are optional. You can add or update them later from your profile.")

            LabeledField("Blood group", isOptional: true) {
                Picker("Blood group", selection: $vm.bloodGroup) {
                    Text("Select blood group").tag(nil as BloodGroup?)
                    ForEach(BloodGroup.allCases) { group in
                        Text(group.rawValue).tag(group as BloodGroup?)
                    }
                }
                .pickerStyle(.menu)
                .tint(AppColor.onSurface)
                .inputBox()
            }

            LabeledField(
                "Blood pressure",
                isOptional: true,
                error: vm.showErrors ? vm.bpError : nil,
                hint: "Systolic / diastolic, for example 120 / 80"
            ) {
                HStack(spacing: Spacing.sm) {
                    numberField("120", text: $vm.systolic, hasError: vm.bpError != nil)
                    Text("/")
                        .foregroundStyle(AppColor.onSurfaceVariant)
                    numberField("80", text: $vm.diastolic, hasError: vm.bpError != nil)
                }
            }

            LabeledField(
                "Heart rate",
                isOptional: true,
                error: vm.showErrors ? vm.heartRateError : nil,
                hint: "Beats per minute"
            ) {
                numberField("72 bpm", text: $vm.heartRate, hasError: vm.heartRateError != nil)
            }

            HStack(alignment: .top, spacing: Spacing.md) {
                LabeledField(
                    "Height",
                    isOptional: true,
                    error: vm.showErrors ? vm.heightError : nil
                ) {
                    numberField("cm", text: $vm.heightCm, hasError: vm.heightError != nil)
                }

                LabeledField(
                    "Weight",
                    isOptional: true,
                    error: vm.showErrors ? vm.weightError : nil
                ) {
                    numberField("kg", text: $vm.weightKg, hasError: vm.weightError != nil)
                }
            }

            LabeledField("Allergies", isOptional: true, hint: "Separate multiple allergies with commas") {
                TextField("e.g. Penicillin, peanuts", text: $vm.allergies, axis: .vertical)
                    .lineLimit(2...4)
                    .inputBox()
            }

            LabeledField("Existing conditions", isOptional: true) {
                TextField("e.g. Asthma, diabetes", text: $vm.conditions, axis: .vertical)
                    .lineLimit(2...4)
                    .inputBox()
            }
        }
    }

    private func numberField(_ prompt: String, text: Binding<String>, hasError: Bool) -> some View {
        TextField(prompt, text: text)
            .keyboardType(.numberPad)
            .inputBox(hasError: vm.showErrors && hasError)
    }
}

#Preview {
    ScrollView {
        VitalsStepView(vm: RegistrationViewModel())
            .padding()
    }
    .background(AppColor.background)
}
