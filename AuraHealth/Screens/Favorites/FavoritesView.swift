import SwiftUI

// SCREEN 9 — Favorites (design: favorites)
// Saved providers with swipe-to-remove and a sort menu.
struct FavoritesView: View {
    @Environment(AppState.self) private var appState
    @State private var sort: SortOption = .distance

    enum SortOption: String, CaseIterable, Identifiable {
        case distance = "Distance", rating = "Rating", name = "Name"
        var id: String { rawValue }
    }

    private var items: [Provider] {
        let list = appState.favorites
        switch sort {
        case .distance: return list.sorted { $0.distanceKm < $1.distanceKm }
        case .rating:   return list.sorted { $0.rating > $1.rating }
        case .name:     return list.sorted { $0.name < $1.name }
        }
    }

    var body: some View {
        VStack(spacing: 0) {
            AppHeader(title: "Favorites", leadingSymbol: "heart.fill")

            if items.isEmpty {
                emptyState
            } else {
                // LEARN: `List` gives us swipe actions for free. We hide its
                // default look (separators, grey background) so our cards show.
                List {
                    HStack {
                        Text("\(items.count) Saved Providers")
                            .font(AppFont.labelLg).foregroundStyle(AppColor.onSurfaceVariant)
                        Spacer()
                        Menu {
                            Picker("Sort by", selection: $sort) {
                                ForEach(SortOption.allCases) { Text($0.rawValue).tag($0) }
                            }
                        } label: {
                            Label("Sort", systemImage: "line.3.horizontal.decrease")
                                .font(AppFont.labelLg).foregroundStyle(AppColor.primary)
                                .frame(minHeight: 44)
                        }
                    }
                    .listRowBackground(Color.clear)
                    .listRowSeparator(.hidden)

                    ForEach(items) { provider in
                        FavoriteRow(provider: provider) {
                            withAnimation { appState.toggleFavorite(provider) }
                        }
                        .listRowInsets(EdgeInsets(top: 6, leading: Spacing.md, bottom: 6, trailing: Spacing.md))
                        .listRowBackground(Color.clear)
                        .listRowSeparator(.hidden)
                        .swipeActions(edge: .trailing) {
                            Button(role: .destructive) {
                                withAnimation { appState.toggleFavorite(provider) }
                            } label: {
                                Label("Remove", systemImage: "heart.slash")
                            }
                        }
                    }

                    Label("Swipe left to remove from favorites", systemImage: "hand.draw")
                        .font(AppFont.bodyMd).italic()
                        .foregroundStyle(AppColor.onSurfaceVariant)
                        .frame(maxWidth: .infinity)
                        .listRowBackground(Color.clear)
                        .listRowSeparator(.hidden)
                }
                .listStyle(.plain)
                .scrollContentBackground(.hidden)
                .frame(maxWidth: 640)
            }
        }
        .frame(maxWidth: .infinity)
        .background(AppColor.background.ignoresSafeArea())
        .toolbar(.hidden, for: .navigationBar)
    }

    private var emptyState: some View {
        VStack(spacing: Spacing.sm) {
            Spacer()
            Image(systemName: "heart").font(.system(size: 48)).foregroundStyle(AppColor.outline)
            Text("No favorites yet").font(AppFont.headlineMd)
            Text("Tap the heart on any provider to save it here.")
                .font(AppFont.bodyMd).foregroundStyle(AppColor.onSurfaceVariant)
            Spacer()
        }
    }
}

/// One card in the favourites list.
struct FavoriteRow: View {
    let provider: Provider
    let onToggleFavorite: () -> Void

    var body: some View {
        // The hidden NavigationLink trick: the whole row is tappable without
        // List drawing its own ">" chevron.
        ZStack {
            NavigationLink(value: Route.provider(provider)) { EmptyView() }.opacity(0)

            HStack(alignment: .top, spacing: Spacing.md) {
                ProviderThumbnail(provider: provider).frame(width: 84, height: 84)

                VStack(alignment: .leading, spacing: 6) {
                    HStack(alignment: .top) {
                        Text(provider.name).font(AppFont.labelLg).foregroundStyle(AppColor.onSurface)
                            .multilineTextAlignment(.leading).lineLimit(2)
                        Spacer(minLength: 4)
                        Button(action: onToggleFavorite) {
                            Image(systemName: "heart.fill").foregroundStyle(AppColor.primary)
                                .frame(width: 32, height: 32)
                        }
                        .buttonStyle(.plain)
                        .accessibilityLabel("Remove from favorites")
                    }

                    HStack(spacing: Spacing.sm) {
                        Text(provider.shortStatus)
                            .font(AppFont.labelSm)
                            .foregroundStyle(provider.isOpenNow ? AppColor.primary : AppColor.secondary)
                            .padding(.horizontal, 10).padding(.vertical, 3)
                            .background(provider.isOpenNow ? AppColor.primaryTint : AppColor.surfaceHigh)
                            .clipShape(Capsule())
                        Text("\(provider.distanceText) away")
                            .font(AppFont.bodyMd).foregroundStyle(AppColor.secondary)
                    }

                    HStack {
                        Image(systemName: "star").foregroundStyle(AppColor.star)
                        Text(String(format: "%.1f", provider.rating)).font(AppFont.labelLg)
                        Text("(\(provider.reviewCountText) reviews)")
                            .font(AppFont.bodyMd).foregroundStyle(AppColor.secondary)
                        Spacer()
                        CallCircleButton(phone: provider.phone, filled: true)
                    }
                }
            }
            .padding(Spacing.md)
            .cardStyle(radius: Radius.lg)
        }
    }
}

#Preview {
    NavigationStack { FavoritesView().appDestinations() }
        .environment(AppState())
}
