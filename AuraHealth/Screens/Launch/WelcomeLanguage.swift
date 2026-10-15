import SwiftUI

/// Launch translations should receive native-speaker review before release.
enum WelcomeLanguage: String, CaseIterable, Identifiable {
    case english = "en", luganda = "lg", swahili = "sw"
    var id: String { rawValue }
    var name: String {
        switch self {
        case .english: return "English"
        case .luganda: return "Luganda"
        case .swahili: return "Kiswahili"
        }
    }
    static var deviceDefault: String {
        let code = Locale.preferredLanguages.first?.split(separator: "-").first.map(String.init) ?? "en"
        return WelcomeLanguage(rawValue: code)?.rawValue ?? "en"
    }
    func text(_ en: String, _ lg: String, _ sw: String) -> String {
        switch self {
        case .english: return en
        case .luganda: return lg
        case .swahili: return sw
        }
    }
    var tagline: String { text("Closer to care.", "Obujjanjabi kumpi naawe.", "Huduma za afya karibu nawe.") }
    var skip: String { text("Skip", "Buuka", "Ruka") }
    var next: String { text("Continue", "Weeyongereyo", "Endelea") }
    var start: String { text("Get started", "Tandika", "Anza") }
    var back: String { text("Back", "Ddayo", "Rudi") }
    var choose: String { text("Choose your language", "Londa olulimi lwo", "Chagua lugha yako") }
    var done: String { text("Done", "Kiwedde", "Imekamilika") }
    var footnote: String { text("Healthcare discovery, made for Uganda", "Okunoonya obujjanjabi mu Uganda", "Tafuta huduma za afya nchini Uganda") }
    func progress(_ page: Int) -> String {
        text("Page \(page) of 3", "Omuko \(page) ku 3", "Ukurasa wa \(page) kati ya 3")
    }
    var pages: [WelcomePage] {
        [
            WelcomePage(image: "WelcomeCare", symbol: "magnifyingglass", accent: AppColor.success,
                eyebrow: text("CARE STARTS HERE", "OBUJJANJABI BUTANDIKIRA WANO", "HUDUMA ZINAANZIA HAPA"),
                title: text("Your next step to\nbetter care.", "Tandika okunoonya\nobujjanjabi obulungi.", "Hatua yako kuelekea\nhuduma bora."),
                detail: text("Discover hospitals, clinics and pharmacies in Uganda. Find a place to start, for you and the people you love.", "Noonya amalwaliro, kiliniki n’amaduuka g’eddagala mu Uganda. Funa w’otandikira, ku lulwo n’abantu bo abaagalwa.", "Gundua hospitali, kliniki na maduka ya dawa nchini Uganda. Pata pa kuanzia kwa ajili yako na wapendwa wako."),
                badge: text("Care for your everyday", "Obujjanjabi bwa buli lunaku", "Huduma za kila siku")),
            WelcomePage(image: "WelcomeFacility", symbol: "map", accent: AppColor.primary,
                eyebrow: text("EXPLORE YOUR OPTIONS", "LABA BY’OSOBOLA OKULONDA", "CHUNGUZA CHAGUZI ZAKO"),
                title: text("Find the right place.\nKnow your options.", "Funa ekifo ekikusaanira.\nManya by’oyinza okulonda.", "Pata mahali panapofaa.\nFahamu chaguzi zako."),
                detail: text("Explore the map, compare services and view facility details. Keep contact information close when you need it.", "Kebera mmaapu, geraageranya empeereza era olabe ebikwata ku malwaliro. Kuuma ennamba z’essimu kumpi nga ozeetaaga.", "Chunguza ramani, linganisha huduma na uone taarifa za vituo. Weka mawasiliano karibu unapoyahitaji."),
                badge: text("Explore. Compare. Connect.", "Noonya. Geraageranya. Tuukirira.", "Chunguza. Linganisha. Wasiliana.")),
            WelcomePage(image: "WelcomePharmacy", symbol: "heart", accent: AppColor.success,
                eyebrow: text("KEEP CARE CLOSE", "OBUJJANJABI BUBE KUMPI", "WEKA HUDUMA KARIBU"),
                title: text("Your favourites.\nA little closer.", "Ebifo by’oyagala.\nKumpi naawe.", "Vituo unavyopenda.\nKaribu zaidi."),
                detail: text("Add facilities to your favourites for quick access as you explore. Start with the places that matter to you.", "Teeka ebifo by’oyagala ku lukalala osobole okubifuna amangu ng’onoonya. Tandika n’ebifo by’otwala nga bikulu.", "Ongeza vituo kwenye vipendwa ili kuvipata haraka unapovinjari. Anza na maeneo muhimu kwako."),
                badge: text("The places that matter to you", "Ebifo ebikulu gy’oli", "Maeneo muhimu kwako"))
        ]
    }
}

struct WelcomePage {
    let image: String
    let symbol: String
    let accent: Color
    let eyebrow: String
    let title: String
    let detail: String
    let badge: String
}
