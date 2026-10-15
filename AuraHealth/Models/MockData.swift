import Foundation

// =============================================================================
// MOCK DATA — fake data so every screen has something to show.
//
// Later you'd replace this with network calls (URLSession) to your Laravel API.
// Because the screens only talk to AppState, swapping fake → real data means
// changing ONE place, not 18 screens.
// =============================================================================
enum MockData {

    // LEARN: `static let` = created once, shared. Stable ids matter: favourites
    // are stored by id, so the providers must not be re-created on every access.
    static let providers: [Provider] = [
        Provider(name: "Mulago National Hospital", address: "Kampala Hill, Bypass Rd",
                 type: .hospital, rating: 4.8, reviewCount: 1248, distanceKm: 4.5,
                 isOpenNow: true, is24Hours: true, closesAt: nil, phone: "+256700000001",
                 latitude: 0.3376, longitude: 32.5763,
                 services: ["24/7 ER", "Radiology", "Laboratory tests", "Pharmacy", "Child health & pediatrics", "Maternity & delivery", "Malaria testing & treatment", "Antenatal care", "Postnatal care", "HIV testing & care", "Tuberculosis care", "Diabetes care", "Hypertension care"]),

        Provider(name: "IHK International Hospital", address: "Namuwongo, Kampala",
                 type: .hospital, rating: 4.7, reviewCount: 980, distanceKm: 5.2,
                 isOpenNow: true, is24Hours: true, closesAt: nil, phone: "+256700000002",
                 latitude: 0.2990, longitude: 32.6030,
                 services: ["24/7 ER", "Surgery", "Laboratory tests", "Pharmacy", "Malaria testing & treatment", "Typhoid testing & treatment", "Family planning", "Antenatal care", "Diabetes care"]),

        Provider(name: "Case Medical Centre", address: "Buganda Road, Kampala",
                 type: .hospital, rating: 4.5, reviewCount: 1500, distanceKm: 3.8,
                 isOpenNow: true, is24Hours: true, closesAt: nil, phone: "+256700000003",
                 latitude: 0.3163, longitude: 32.5822,
                 services: ["24/7 ER", "Radiology", "Maternity & delivery", "Dental care", "Antenatal care", "Postnatal care", "Family planning", "Malaria testing & treatment"]),

        Provider(name: "Ecopharm Pharmacy", address: "Acacia Mall, Kisementi",
                 type: .pharmacy, rating: 4.9, reviewCount: 320, distanceKm: 1.2,
                 isOpenNow: true, is24Hours: false, closesAt: "10PM", phone: "+256700000004",
                 latitude: 0.3347, longitude: 32.5918,
                 services: ["Prescriptions", "Immunisation", "Health Checks", "Malaria testing & treatment", "Diabetes care", "Hypertension care"]),

        Provider(name: "Sunshine Pediatrics Center", address: "Bukoto, Kampala",
                 type: .clinic, rating: 4.9, reviewCount: 450, distanceKm: 0.8,
                 isOpenNow: true, is24Hours: false, closesAt: "6PM", phone: "+256700000005",
                 latitude: 0.3500, longitude: 32.5900,
                 services: ["Child health & pediatrics", "Immunisation", "Child Wellness", "Malaria testing & treatment", "Cough, flu & respiratory care"]),

        Provider(name: "Lancet Laboratories Uganda", address: "Kololo, Kampala",
                 type: .clinic, rating: 4.7, reviewCount: 2300, distanceKm: 5.2,
                 isOpenNow: true, is24Hours: true, closesAt: nil, phone: "+256700000006",
                 latitude: 0.3330, longitude: 32.5880,
                 services: ["Laboratory tests", "Pathology", "Health Screening", "Malaria testing & treatment", "Typhoid testing & treatment", "HIV testing & care", "Tuberculosis care", "Diabetes care"]),

        Provider(name: "Pearl Dental Clinic", address: "Ntinda, Kampala",
                 type: .clinic, rating: 4.6, reviewCount: 180, distanceKm: 1.2,
                 isOpenNow: false, is24Hours: false, closesAt: nil, phone: "+256700000007",
                 latitude: 0.3590, longitude: 32.6120,
                 services: ["Dental care", "Cleaning", "Orthodontics"]),

        Provider(name: "Abacus Opticians", address: "Garden City, Kampala",
                 type: .clinic, rating: 4.5, reviewCount: 520, distanceKm: 3.1,
                 isOpenNow: true, is24Hours: false, closesAt: "8PM", phone: "+256700000008",
                 latitude: 0.3260, longitude: 32.5990,
                 services: ["Eye care", "Eye Tests", "Glasses", "Contact Lenses"]),

        Provider(name: "Nakasero Hospital", address: "Nakasero Hill, Kampala",
                 type: .hospital, rating: 4.6, reviewCount: 870, distanceKm: 2.1,
                 isOpenNow: true, is24Hours: true, closesAt: nil, phone: "+256700000009",
                 latitude: 0.3253, longitude: 32.5800,
                 services: ["24/7 ER", "Cardiology", "Radiology", "Pharmacy", "Malaria testing & treatment", "Antenatal care", "Postnatal care", "Mental health", "Hypertension care"]),

        Provider(name: "Kampala Rapid Ambulance", address: "Kololo, Kampala",
                 type: .ambulance, rating: 4.4, reviewCount: 210, distanceKm: 2.9,
                 isOpenNow: true, is24Hours: true, closesAt: nil, phone: "+256700000010",
                 latitude: 0.3370, longitude: 32.5930,
                 services: ["Emergency Transport", "Paramedics", "Oxygen"])
    ]

    /// Clinicians are offset slightly from their facility on the demo map so
    /// both annotations remain tappable. The backend can later return each
    /// doctor's current practice location and availability.
    static let doctors: [CareDoctor] = [
        CareDoctor(name: "Dr. Emmanuel Katunguka",
                   specialty: "Cardiologist & Internal Medicine",
                   availability: "9:00 AM – 4:00 PM", rating: 4.9,
                   imageName: "doctor_1", isAvailableNow: true,
                   provider: providers[8], latitude: 0.3265, longitude: 32.5811),
        CareDoctor(name: "Dr. Sarah Namubiru",
                   specialty: "Pediatrician & Child Health Specialist",
                   availability: "10:30 AM – 6:00 PM", rating: 4.8,
                   imageName: "doctor_2", isAvailableNow: true,
                   provider: providers[0], latitude: 0.3387, longitude: 32.5774),
        CareDoctor(name: "Dr. James Okello",
                   specialty: "General Surgeon & Emergency Care",
                   availability: "On call 24/7", rating: 5.0,
                   imageName: "doctor_3", isAvailableNow: true,
                   provider: providers[1], latitude: 0.3001, longitude: 32.6041),
        CareDoctor(name: "Dr. Ruth Achieng",
                   specialty: "Obstetrician & Gynaecologist",
                   availability: "8:00 AM – 3:30 PM", rating: 4.7,
                   imageName: "doctor_4", isAvailableNow: false,
                   provider: providers[2], latitude: 0.3174, longitude: 32.5833),
        CareDoctor(name: "Dr. David Ssemakula",
                   specialty: "Paediatrician",
                   availability: "9:00 AM – 5:00 PM", rating: 4.9,
                   imageName: "doctor_5", isAvailableNow: true,
                   provider: providers[4], latitude: 0.3511, longitude: 32.5911)
    ]

    /// Names of providers that start as favourites (matches the Favorites design).
    static let initialFavoriteNames = [
        "Mulago National Hospital", "Sunshine Pediatrics Center",
        "Lancet Laboratories Uganda", "Pearl Dental Clinic", "Abacus Opticians"
    ]

    static let myReviews: [Review] = [
        Review(providerName: "Mulago National Referral Hospital", dateText: "October 24, 2023", rating: 5,
               text: "The pediatric department was exceptional during our emergency visit. Dr. Namono was calm and professional, explaining everything clearly. The waiting time was shorter than expected for a referral hospital.",
               helpfulCount: 14, category: .hospitals),
        Review(providerName: "Nsambya Hospital Pharmacy", dateText: "September 12, 2023", rating: 4,
               text: "Very efficient service for prescription refills. They had all the medication in stock. The staff were polite but the queue management system could be improved during peak hours.",
               helpfulCount: 3, category: .pharmacies),
        Review(providerName: "Dr. Kato's Dental Clinic", dateText: "August 05, 2023", rating: 3,
               text: "Standard cleaning service. The facility is clean and well-maintained. A bit more expensive compared to other clinics in Nakasero, but the quality is consistent.",
               helpfulCount: 1, category: .specialists)
    ]

    static let publicReviews: [PublicReview] = [
        PublicReview(author: "Kasumba Nicholas", timeAgo: "2 days ago", rating: 5,
                     text: "Excellent emergency response. The nursing staff was very attentive during my stay. Clean facilities and professional environment."),
        PublicReview(author: "Amina Musoke", timeAgo: "1 week ago", rating: 5,
                     text: "Very busy but efficient. The outpatient clinic wait times can be long, but the quality of care is the best in the country.")
    ]

    static let notifications: [NotificationItem] = [
        NotificationItem(title: "New review received",
                         message: "Patient Sarah M. left a 5-star review for your recent pediatric consultation at Mulago Hospital.",
                         timeText: "2m ago", category: .updates, icon: "square.and.pencil", isUnread: true, section: "Today"),
        NotificationItem(title: "Verification status update",
                         message: "Your professional medical credentials have been successfully re-verified for the 2024-2025 period.",
                         timeText: "1h ago", category: .system, icon: "checkmark.shield.fill", isUnread: true, section: "Today"),
        NotificationItem(title: "Favorited provider hours changed",
                         message: "Victoria Hospital Pharmacy has updated their weekend hours. They are now open 24/7 for emergencies.",
                         timeText: "Yesterday", category: .updates, icon: "clock", isUnread: false, section: "Yesterday"),
        NotificationItem(title: "New Emergency Facility Near You",
                         message: "A new specialized cardiac emergency unit has opened at Nakasero Hospital, just 2km from your current location.",
                         timeText: "Yesterday", category: .alerts, icon: "light.beacon.max.fill", isUnread: false, section: "Yesterday"),
        NotificationItem(title: "Security login alert",
                         message: "New login detected from a Chrome browser on Windows in Kampala. Was this you?",
                         timeText: "Yesterday", category: .system, icon: "lock.shield.fill", isUnread: false, section: "Yesterday"),
        NotificationItem(title: "Welcome to AfyaLink",
                         message: "Save your favourite providers and turn on Emergency Mode for one-tap help.",
                         timeText: "Last week", category: .system, icon: "hand.wave.fill", isUnread: false, section: "Earlier")
    ]

    static let messageThreads: [MessageThread] = [
        MessageThread(
            participantName: "AfyaLink Care Assistant",
            subtitle: "Automated health reminders",
            symbol: "heart.text.square.fill",
            isOnline: true,
            isAutomated: true,
            unreadCount: 2,
            messages: [
                ChatMessage(author: .automated, text: "Good morning, Musa. It is time to take your prescribed tablets with food.", timeText: "8:00 AM"),
                ChatMessage(author: .automated, text: "Your follow-up check-up is due on Friday at 10:30 AM. Reply if you need help finding the facility.", timeText: "8:01 AM")
            ]
        ),
        MessageThread(
            participantName: "Dr. Sarah Namubiru",
            subtitle: "Pediatrics · Mulago Hospital",
            symbol: "stethoscope",
            isOnline: true,
            isAutomated: false,
            unreadCount: 1,
            messages: [
                ChatMessage(author: .user, text: "Hello Doctor, the fever reduced after the medicine.", timeText: "Yesterday"),
                ChatMessage(author: .provider, text: "That is encouraging. Please continue checking the temperature and tell me if it rises again.", timeText: "9:42 AM")
            ]
        ),
        MessageThread(
            participantName: "Mulago National Hospital",
            subtitle: "Patient support desk",
            symbol: "building.2.fill",
            isOnline: true,
            isAutomated: false,
            unreadCount: 0,
            messages: [
                ChatMessage(author: .provider, text: "Your laboratory results are ready. A clinician will discuss them with you during your appointment.", timeText: "Monday")
            ]
        ),
        MessageThread(
            participantName: "Ecopharm Pharmacy",
            subtitle: "Pharmacy support",
            symbol: "pills.fill",
            isOnline: false,
            isAutomated: false,
            unreadCount: 0,
            messages: [
                ChatMessage(author: .provider, text: "Your prescription refill is available for collection.", timeText: "Last week")
            ]
        )
    ]
}
