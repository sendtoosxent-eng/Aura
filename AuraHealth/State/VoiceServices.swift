//
//  VoiceServices.swift
//  AuraHealth
//
//  Created by Osxent on 12/10/2026.
//

import AVFoundation
import Observation

// MARK: - Recording

@Observable
final class VoiceRecorder {
    private(set) var isRecording = false
    private(set) var elapsed: TimeInterval = 0
    var permissionDenied = false

    // Internal plumbing: the UI doesn't need to watch these.
    @ObservationIgnored private var recorder: AVAudioRecorder?
    @ObservationIgnored private var timer: Timer?
    @ObservationIgnored private var fileURL: URL?

    /// Asks for the microphone (first time only), then starts recording.
    func start() {
        AVAudioApplication.requestRecordPermission { granted in
            DispatchQueue.main.async {
                if granted { self.beginRecording() } else { self.permissionDenied = true }
            }
        }
    }

    private func beginRecording() {
        do {
            let session = AVAudioSession.sharedInstance()
            try session.setCategory(.playAndRecord, mode: .default, options: [.defaultToSpeaker])
            try session.setActive(true)

            let url = FileManager.default.temporaryDirectory
                .appendingPathComponent("voice-\(UUID().uuidString).m4a")
            let settings: [String: Any] = [
                AVFormatIDKey: Int(kAudioFormatMPEG4AAC),
                AVSampleRateKey: 44_100,
                AVNumberOfChannelsKey: 1,
                AVEncoderAudioQualityKey: AVAudioQuality.high.rawValue
            ]
            let newRecorder = try AVAudioRecorder(url: url, settings: settings)
            newRecorder.record()

            recorder = newRecorder
            fileURL = url
            elapsed = 0
            isRecording = true
            timer = Timer.scheduledTimer(withTimeInterval: 0.25, repeats: true) { [weak self] _ in
                self?.elapsed = self?.recorder?.currentTime ?? 0
            }
        } catch {
            cleanup()
        }
    }

    /// Stops and returns the file + length. Returns nil for accidental taps (< 0.5s).
    func stop() -> (url: URL, duration: TimeInterval)? {
        guard isRecording, let url = fileURL else { return nil }
        let duration = recorder?.currentTime ?? elapsed
        cleanup()
        if duration < 0.5 {
            try? FileManager.default.removeItem(at: url)
            return nil
        }
        return (url, duration)
    }

    func cancel() {
        let url = fileURL
        cleanup()
        if let url { try? FileManager.default.removeItem(at: url) }
    }

    private func cleanup() {
        recorder?.stop()
        timer?.invalidate()
        timer = nil
        recorder = nil
        isRecording = false
        try? AVAudioSession.sharedInstance().setActive(false, options: .notifyOthersOnDeactivation)
    }
}

// MARK: - Playback

@Observable
final class VoicePlayer: NSObject, AVAudioPlayerDelegate {
    private(set) var playingID: AnyHashable?
    private(set) var progress: Double = 0

    @ObservationIgnored private var player: AVAudioPlayer?
    @ObservationIgnored private var timer: Timer?

    /// Tap once to play, tap again to stop.
    func toggle(id: AnyHashable, url: URL) {
        if playingID == id { stop(); return }
        stop()
        do {
            try AVAudioSession.sharedInstance().setCategory(.playback)
            try AVAudioSession.sharedInstance().setActive(true)
            let newPlayer = try AVAudioPlayer(contentsOf: url)
            newPlayer.delegate = self
            newPlayer.play()
            player = newPlayer
            playingID = id
            progress = 0
            timer = Timer.scheduledTimer(withTimeInterval: 0.05, repeats: true) { [weak self] _ in
                guard let self, let p = self.player, p.duration > 0 else { return }
                self.progress = p.currentTime / p.duration
            }
        } catch {
            stop()
        }
    }

    func stop() {
        player?.stop()
        player = nil
        timer?.invalidate()
        timer = nil
        playingID = nil
        progress = 0
    }

    func audioPlayerDidFinishPlaying(_ player: AVAudioPlayer, successfully flag: Bool) {
        DispatchQueue.main.async { self.stop() }
    }
}
