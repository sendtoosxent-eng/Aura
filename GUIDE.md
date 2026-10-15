# AfyaLink Uganda — SwiftUI practice project

Built from your Stitch export (`stitch_uganda_health_locator_emergency`): 17 design screens → 17 SwiftUI screens (onboarding is one screen with 3 swipeable pages).

Requirements: **Xcode 15+**, **iOS 17+** target. Works on iPhone and iPad (layouts resize to the width).

---

## 1. Set up in Xcode (5 minutes)

1. Xcode → **File → New → Project → iOS → App**. Product Name: `AuraHealth`, Interface: **SwiftUI**, Language: **Swift**.
2. In the new project, **delete** the generated `ContentView.swift` and `AuraHealthApp.swift`.
3. Drag the **inner `AuraHealth` folder** from this download (the one containing `App`, `Theme`, `Models`, … folders) into the Xcode project navigator. Tick **"Copy items if needed"** and **"Create groups"**, and make sure the app target is ticked.
4. Project settings → **General → Minimum Deployments → iOS 17.0**.
5. Press **⌘R**. Open any file and use the **Canvas (⌥⌘↩)** to see its `#Preview` live — this is the fastest way to practice.

> If Xcode complains about concurrency/actor isolation: Build Settings → search "Swift Language Version" → set **Swift 5**.
> The Simulator cannot place phone calls or send SMS — "Call" buttons only work on a real iPhone.

---

## 2. How the app is wired (read this first)

```
AuraHealthApp  (@main)  creates ONE AppState and shares it
   └─ RootView  switches on appState.stage
        ├─ .splash          SplashView
        ├─ .onboarding      OnboardingView
        ├─ .auth            AuthFlowView → LoginView → OTPView
        ├─ .patient         MainTabView  (5 tabs, each with its own NavigationStack)
        │                     Home · Map · (Emergency → full-screen cover) · Favorites · Account
        └─ .providerPortal  ProviderPortalView (Dashboard + placeholder tabs)
```

The 4 big ideas you'll see over and over:

| Idea | Where | In one line |
|---|---|---|
| **State drives UI** | `AppState`, `@State` everywhere | Change a variable → SwiftUI redraws the screen for you. |
| **Reusable components** | `Components/` | Style a button once, use it 20 times. |
| **Value-based navigation** | `Route.swift` | `NavigationLink(value: Route.provider(p))` pushes the matching screen. |
| **Design tokens** | `Theme/Theme.swift` | Colours/spacing/fonts from `DESIGN.md` live in one place. |

### Property wrappers cheat-sheet
| Wrapper | Meaning |
|---|---|
| `@State` | Data **this view owns**. Private. Changing it redraws the view. |
| `@Binding` | A two-way link to a parent's `@State` (`$value`). |
| `@Environment(AppState.self)` | Read the shared object injected at the top of the app. |
| `@Bindable` | Lets you make `$bindings` to properties of an `@Observable` object. |
| `@AppStorage` | A value saved in UserDefaults (survives app restarts). |
| `@FocusState` | Tracks which text field has the keyboard. |

---

## 3. Screen map (design → file → what it teaches)

Suggested practice order is top to bottom (easy → harder).

| # | Design folder | Swift file | You'll learn |
|---|---|---|---|
| 1 | `splash_screen` | `Launch/SplashView` | VStack/ZStack, `Spacer`, `.task`, repeat animations |
| 2 | `onboarding_sequence` | `Launch/OnboardingView` | `TabView` pager, `@State` index, `@AppStorage` |
| 3 | `no_internet_connection` | `Account/NoInternetView` | Closures as parameters, `Text + Text`, reusing buttons |
| 4 | `login_signup` | `Auth/LoginView` | `TextField`, input validation, custom segmented switch, `navigationDestination(isPresented:)` |
| 5 | `otp_verification` | `Auth/OTPView` | `@FocusState`, hidden-TextField trick, `Timer`, `dismiss` |
| 6 | `account` | `Account/AccountView` | Lists of rows, `fullScreenCover`, `confirmationDialog`, alerts |
| 7 | `notifications` | `Account/NotificationsView` | Filtering arrays, grouping by section, mutating `@State` array items |
| 8 | `my_reviews` | `Account/MyReviewsView` | Computed values, delete from array, custom `Binding(get:set:)` |
| 9 | `write_a_review` | `Account/WriteReviewView` | `TextEditor`, `PhotosPicker`, `safeAreaInset`, `@Binding` stars |
| 10 | `favorites` | `Favorites/FavoritesView` | `List`, `swipeActions`, `Menu` + `Picker` sort |
| 11 | `home_search` | `Home/HomeView` | Search, chips, `@ViewBuilder`, skeleton loading, overlays |
| 12 | `filter_filters` | `Home/FilterSheet` | Sheets, "draft vs. commit" editing, `Slider`, `Toggle`, `LazyVGrid` |
| 13 | `map_view` | `Map/ProvidersMapView` | MapKit (`Map`, `Marker`, selection, camera position) |
| 14 | `emergency_mode` | `Emergency/EmergencyView` | `ShareLink`, opening `tel:` / `sms:` URLs, pulse animation |
| 15 | `provider_profile` | `Provider/ProviderProfileView` | Custom tabs, `ViewThatFits`, adaptive grid, opening Apple Maps |
| 16 | `provider_dashboard` | `Provider/ProviderDashboardView` | Second tab bar, dashboard cards |
| 17 | `provider_registration_step_1` | `Provider/ProviderRegistrationStep1View` | Forms, generic helper views `field(_:content:)`, `Menu` dropdown |

Supporting files you should read once: `Theme.swift`, `Models.swift`, `AppState.swift`, `Buttons.swift`, `ProviderCards.swift`.

---

## 4. How to practice (the method)

1. Pick one screen from the table. Open the design PNG next to it.
2. **Read** the Swift file top to bottom (comments marked `LEARN:` explain the new idea).
3. Make a copy of the file, **empty the `body`**, and rebuild the screen from the PNG using the components in `Components/`.
4. Use the **Canvas preview** to compare against the PNG. Stuck? Peek at the original, then retry.
5. Swap your version in. The rest of the app doesn't care — screens only talk to `AppState`.

Good first experiments (5 min each):
- Change `AppColor.primary` in `Theme.swift` and watch the whole app recolour.
- In `HomeView`, change the featured card to appear last instead of first.
- Add a 4th onboarding page.
- Make the Map tab show only hospitals by default.

---

## 5. Where I deviated from the design (and why)

- **App name:** the native app uses **AfyaLink Uganda** to match the existing web platform. Change `AppConfig.brand` / `fullName` to rename it.
- **Emergency number:** design said "911 / 999". 911 isn't used in Uganda, so the button dials **999** and shows **999 / 112**.
- **Photos:** no image files were in the export, so providers show a tinted icon tile. Swap `ProviderThumbnail` for `AsyncImage` when you have image URLs.
- **Font:** design uses Inter; the app uses the system font with text styles so it scales with iOS "Larger Text". Instructions for adding Inter are in `Theme.swift`.
- **Native tab bar:** the design's pill-shaped active tab is replaced by iOS's standard tab bar (tinted red) — better accessibility and less code.
- **Onboarding pages 2–3** and **provider portal tabs other than Dashboard** weren't designed; I wrote simple versions/placeholders.
- **Hamburger menu** in the header was left out (nothing in the design said what it opens).
- Data is **fake** (`MockData.swift`). Auth accepts any 4-digit code.

---

## 6. Where to go next

1. Real location: `CoreLocation` (`CLLocationManager`) → replace the "Mulago Hill" text and compute true distances.
2. Real data: a `ProviderService` using `URLSession` against your Laravel API, called from `AppState`.
3. Real OTP/auth: Sanctum token stored in the Keychain.
4. Offline cache: SwiftData to store providers so the "View Offline Data" button really works.
5. Finish registration steps 2–6, then the portal's Services / Hours / Reviews tabs.
