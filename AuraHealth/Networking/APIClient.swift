import Foundation
import Security
import UIKit

enum APIError: LocalizedError {
    case invalidResponse
    case server(status: Int, message: String)
    case transport(String)

    var errorDescription: String? {
        switch self {
        case .invalidResponse:
            return "The server returned an invalid response."
        case let .server(_, message):
            return message
        case let .transport(message):
            return message
        }
    }
}

private struct APIErrorPayload: Decodable {
    let message: String?
    let errors: [String: [String]]?

    var bestMessage: String? {
        errors?.values.first?.first ?? message
    }
}

final class SecureSessionStore: @unchecked Sendable {
    static let shared = SecureSessionStore()

    private let service = "ug.afyalink.ios"
    private let tokenAccount = "api-access-token"
    private let deviceAccount = "installation-device-id"

    var accessToken: String? {
        get { read(account: tokenAccount) }
        set {
            if let newValue, !newValue.isEmpty {
                save(newValue, account: tokenAccount)
            } else {
                delete(account: tokenAccount)
            }
        }
    }

    var deviceID: String {
        if let stored = read(account: deviceAccount) { return stored }
        let generated = UUID().uuidString.lowercased()
        save(generated, account: deviceAccount)
        return generated
    }

    var deviceName: String {
        UIDevice.current.name.isEmpty ? "AfyaLink iOS device" : UIDevice.current.name
    }

    private func read(account: String) -> String? {
        let query: [String: Any] = [
            kSecClass as String: kSecClassGenericPassword,
            kSecAttrService as String: service,
            kSecAttrAccount as String: account,
            kSecReturnData as String: true,
            kSecMatchLimit as String: kSecMatchLimitOne
        ]
        var item: CFTypeRef?
        guard SecItemCopyMatching(query as CFDictionary, &item) == errSecSuccess,
              let data = item as? Data else { return nil }
        return String(data: data, encoding: .utf8)
    }

    private func save(_ value: String, account: String) {
        delete(account: account)
        let query: [String: Any] = [
            kSecClass as String: kSecClassGenericPassword,
            kSecAttrService as String: service,
            kSecAttrAccount as String: account,
            kSecValueData as String: Data(value.utf8),
            kSecAttrAccessible as String: kSecAttrAccessibleAfterFirstUnlockThisDeviceOnly
        ]
        SecItemAdd(query as CFDictionary, nil)
    }

    private func delete(account: String) {
        let query: [String: Any] = [
            kSecClass as String: kSecClassGenericPassword,
            kSecAttrService as String: service,
            kSecAttrAccount as String: account
        ]
        SecItemDelete(query as CFDictionary)
    }
}

final class APIClient: @unchecked Sendable {
    static let shared = APIClient()

    let sessionStore: SecureSessionStore
    private let session: URLSession
    private let decoder: JSONDecoder
    private let encoder: JSONEncoder

    init(session: URLSession = .shared, sessionStore: SecureSessionStore = .shared) {
        self.session = session
        self.sessionStore = sessionStore
        decoder = JSONDecoder()
        decoder.keyDecodingStrategy = .convertFromSnakeCase
        encoder = JSONEncoder()
        encoder.keyEncodingStrategy = .convertToSnakeCase
    }

    func send<Response: Decodable, Body: Encodable>(
        _ path: String,
        method: String = "GET",
        body: Body,
        authenticated: Bool = false
    ) async throws -> Response {
        var request = try makeRequest(path: path, method: method, authenticated: authenticated)
        request.httpBody = try encoder.encode(body)
        request.setValue("application/json", forHTTPHeaderField: "Content-Type")
        return try await execute(request)
    }

    func send<Response: Decodable>(
        _ path: String,
        method: String = "GET",
        authenticated: Bool = false
    ) async throws -> Response {
        let request = try makeRequest(path: path, method: method, authenticated: authenticated)
        return try await execute(request)
    }

    func data(_ path: String, authenticated: Bool = false) async throws -> Data {
        let request = try makeRequest(path: path, method: "GET", authenticated: authenticated)
        do {
            let (data, response) = try await session.data(for: request)
            guard let http = response as? HTTPURLResponse else { throw APIError.invalidResponse }
            guard (200...299).contains(http.statusCode) else {
                throw APIError.server(status: http.statusCode, message: "The requested image is unavailable.")
            }
            return data
        } catch let error as APIError {
            throw error
        } catch {
            throw APIError.transport("Could not load the profile image.")
        }
    }

    func sendMultipart<Response: Decodable>(
        _ path: String,
        method: String = "POST",
        form: MultipartFormData,
        authenticated: Bool = false
    ) async throws -> Response {
        var request = try makeRequest(path: path, method: method, authenticated: authenticated)
        request.httpBody = form.encoded()
        request.setValue("multipart/form-data; boundary=\(form.boundary)", forHTTPHeaderField: "Content-Type")
        request.timeoutInterval = 60
        return try await execute(request)
    }

    private func makeRequest(path: String, method: String, authenticated: Bool) throws -> URLRequest {
        let cleanPath = path.hasPrefix("/") ? String(path.dropFirst()) : path
        guard let url = URL(string: cleanPath, relativeTo: AppConfig.apiBaseURL) else {
            throw APIError.transport("The AfyaLink server address is invalid.")
        }
        var request = URLRequest(url: url)
        request.httpMethod = method
        request.timeoutInterval = 25
        request.setValue("application/json", forHTTPHeaderField: "Accept")
        if authenticated {
            guard let token = sessionStore.accessToken else {
                throw APIError.server(status: 401, message: "Please sign in again to continue.")
            }
            request.setValue("Bearer \(token)", forHTTPHeaderField: "Authorization")
        }
        return request
    }

    private func execute<Response: Decodable>(_ request: URLRequest) async throws -> Response {
        do {
            let (data, response) = try await session.data(for: request)
            guard let http = response as? HTTPURLResponse else { throw APIError.invalidResponse }
            guard (200...299).contains(http.statusCode) else {
                let payload = try? decoder.decode(APIErrorPayload.self, from: data)
                throw APIError.server(
                    status: http.statusCode,
                    message: payload?.bestMessage ?? "The request failed (\(http.statusCode))."
                )
            }
            do {
                return try decoder.decode(Response.self, from: data)
            } catch {
                throw APIError.transport("AfyaLink could not read the server response.")
            }
        } catch let error as APIError {
            throw error
        } catch {
            throw APIError.transport("Could not reach the AfyaLink server. Check that the backend is running and try again.")
        }
    }
}

struct MultipartFormData {
    struct FilePart {
        let fieldName: String
        let fileName: String
        let mimeType: String
        let data: Data
    }

    let boundary = "Boundary-\(UUID().uuidString)"
    private(set) var fields: [(name: String, value: String)] = []
    private(set) var files: [FilePart] = []

    mutating func addField(_ name: String, value: String?) {
        guard let value else { return }
        fields.append((name, value))
    }

    mutating func addFile(_ fieldName: String, url: URL) throws {
        let accessed = url.startAccessingSecurityScopedResource()
        defer {
            if accessed { url.stopAccessingSecurityScopedResource() }
        }
        let data = try Data(contentsOf: url)
        let safeFileName = url.lastPathComponent
            .replacingOccurrences(of: "\"", with: "_")
            .replacingOccurrences(of: "\r", with: "_")
            .replacingOccurrences(of: "\n", with: "_")
        files.append(FilePart(
            fieldName: fieldName,
            fileName: safeFileName,
            mimeType: Self.mimeType(for: url.pathExtension),
            data: data
        ))
    }

    mutating func addFile(
        _ fieldName: String,
        fileName: String,
        mimeType: String,
        data: Data
    ) {
        files.append(FilePart(
            fieldName: fieldName,
            fileName: fileName,
            mimeType: mimeType,
            data: data
        ))
    }

    func encoded() -> Data {
        var body = Data()
        for field in fields {
            body.append("--\(boundary)\r\n")
            body.append("Content-Disposition: form-data; name=\"\(field.name)\"\r\n\r\n")
            body.append("\(field.value)\r\n")
        }
        for file in files {
            body.append("--\(boundary)\r\n")
            body.append("Content-Disposition: form-data; name=\"\(file.fieldName)\"; filename=\"\(file.fileName)\"\r\n")
            body.append("Content-Type: \(file.mimeType)\r\n\r\n")
            body.append(file.data)
            body.append("\r\n")
        }
        body.append("--\(boundary)--\r\n")
        return body
    }

    private static func mimeType(for pathExtension: String) -> String {
        switch pathExtension.lowercased() {
        case "jpg", "jpeg": return "image/jpeg"
        case "png": return "image/png"
        case "webp": return "image/webp"
        case "pdf": return "application/pdf"
        default: return "application/octet-stream"
        }
    }
}

private extension Data {
    mutating func append(_ string: String) {
        append(Data(string.utf8))
    }
}

struct EmptyRequest: Encodable {}

struct DataEnvelope<Value: Decodable>: Decodable {
    let data: Value
}

struct PageEnvelope<Value: Decodable>: Decodable {
    let data: [Value]
}

