<div class="public-listing-page">
    @php
        $carCount = $cars->total();
    @endphp

    <div class="py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">Alle beschikbare auto's</h1>
                <p class="text-muted mb-0">
                    <span>{{ $carCount }}</span> auto('s) beschikbaar voor jouw keuze
                </p>
            </div>
            @guest
                <a href="{{ route('register') }}" class="btn btn-outline-primary">Aanbieden? Registreer hier</a>
            @endguest
            @auth
                <a href="{{ route('cars.create.step1') }}" class="btn btn-primary px-4">Mijn auto aanbieden</a>
            @endauth
        </div>

        <div class="card border-0 shadow-sm search-panel mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center gap-3 mb-2">
                    <label for="carSearch" class="form-label fw-semibold mb-0">Zoek op merk, model of kenteken</label>
                    <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="clearFilters">Wis filters</button>
                </div>

                <div class="input-group input-group-lg mb-3">
                    <span class="input-group-text bg-white border-end-0">🔎</span>
                    <input
                        type="search"
                        class="form-control border-start-0"
                        id="carSearch"
                        placeholder="Bijv. BMW, Golf of 53CP55"
                        autocomplete="off"
                        wire:model.live.debounce.300ms="search"
                    >
                </div>

                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="text-muted small me-2">Filter op tags:</span>
                    @foreach ($tags as $tag)
                        <label class="tag-filter-pill">
                            <input
                                type="checkbox"
                                class="btn-check"
                                value="{{ $tag->id }}"
                                wire:model.live="selectedTags"
                            >
                            <span class="tag-filter-pill__label">
                                {{ $tag->name }}
                            </span>
                        </label>
                    @endforeach
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <small class="text-muted" wire:loading.remove>Direct filteren zonder pagina te herladen.</small>
                    <small class="text-muted" wire:loading>Resultaten bijwerken...</small>
                    <small class="text-muted">{{ $carCount }} resultaat{{ $carCount === 1 ? '' : 'en' }} gevonden</small>
                </div>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('status') }}</div>
        @endif

        @if ($carCount === 0)
            <div class="card border-0 shadow-sm empty-state">
                <div class="card-body py-5 text-center">
                    <p class="h5 fw-semibold mb-2">Geen auto's beschikbaar</p>
                    <p class="text-muted mb-3">Kom later terug — er worden voortdurend nieuwe auto's toegevoegd!</p>
                </div>
            </div>
        @else
            @if ($cars->count() === 0)
                <div class="card border-0 shadow-sm empty-state mt-4">
                    <div class="card-body py-5 text-center">
                        <p class="h5 fw-semibold mb-2">Geen auto's gevonden</p>
                        <p class="text-muted mb-3">Probeer een andere combinatie van tags of zoekterm.</p>
                        <button type="button" class="btn btn-outline-primary" wire:click="clearFilters">Zoekopdracht wissen</button>
                    </div>
                </div>
            @else
                <div class="row g-4 align-items-stretch mt-1">
                    @foreach ($cars as $car)
                        @php($isFeatured = in_array($car->id, $featuredCarIds, true))
                        <div class="col-12 col-md-6 {{ $isFeatured ? 'col-lg-6' : 'col-lg-4' }} car-result">
                            <a href="{{ route('cars.show', $car) }}" class="text-decoration-none d-block h-100">
                                <div class="card h-100 car-card shadow-sm border-0 {{ $isFeatured ? 'car-card--featured' : '' }}">
                                    <div class="card-body d-flex flex-column position-relative">
                                        @if ($isFeatured)
                                            <span class="car-card-ribbon">Uitgelicht</span>
                                        @endif

                                        <div class="d-flex flex-wrap gap-2 mb-3">
                                            <span class="badge text-bg-success">Te koop</span>
                                            @foreach ($car->tags->take(3) as $tag)
                                                <span class="badge text-bg-light border text-dark">{{ $tag->name }}</span>
                                            @endforeach
                                        </div>
                                        <h2 class="h5 fw-bold mb-1 text-dark">{{ $car->brand }} {{ $car->model }}</h2>
                                        <p class="text-muted small mb-3">Bouwjaar {{ $car->production_year ?? '—' }}</p>

                                        <div class="mb-3 pb-3 border-bottom small">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="text-muted">Kilometer:</span>
                                                <strong>{{ number_format($car->mileage, 0, ',', '.') }} km</strong>
                                            </div>
                                            @if($car->doors)
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span class="text-muted">Deuren:</span>
                                                    <strong>{{ $car->doors }}</strong>
                                                </div>
                                            @endif
                                            @if($car->seats)
                                                <div class="d-flex justify-content-between">
                                                    <span class="text-muted">Zitplaatsen:</span>
                                                    <strong>{{ $car->seats }}</strong>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="mt-auto">
                                            <p class="mb-2"><span class="text-muted">Kenteken:</span> <span class="license-plate-badge">{{ $car->license_plate }}</span></p>
                                            <p class="h5 fw-bold text-primary mb-0">EUR {{ number_format((float) $car->price, 2, ',', '.') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 d-flex justify-content-center">
                    {{ $cars->links('pagination::bootstrap-5') }}
                </div>
            @endif
        @endif
    </div>
</div>
