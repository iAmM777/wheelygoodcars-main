@extends('layouts.app')

@section('content')
    <div class="py-4 public-listing-page">
        @php
            $featuredCarIds = $cars
                ->pluck('id')
                ->shuffle()
                ->take(max(1, min(2, $cars->count())))
                ->all();
        @endphp

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">Alle beschikbare auto's</h1>
                <p class="text-muted mb-0"><span id="carsVisibleCount">{{ $cars->count() }}</span> auto('s) beschikbaar voor jouw keuze</p>
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
                <label for="carSearch" class="form-label fw-semibold mb-2">Zoek op merk of model</label>
                <div class="input-group input-group-lg">
                    <span class="input-group-text bg-white border-end-0">🔎</span>
                    <input
                        type="search"
                        class="form-control border-start-0"
                        id="carSearch"
                        placeholder="Bijv. BMW of Golf"
                        autocomplete="off"
                    >
                    <button class="btn btn-outline-secondary" type="button" id="clearCarSearch">Wis</button>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <small class="text-muted">Direct filteren zonder pagina te herladen.</small>
                    <small class="text-muted" id="searchResultHint">Typ om te zoeken</small>
                </div>
            </div>
        </div>

        @if (session('status'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('status') }}</div>
        @endif

        @if ($cars->count() === 0)
            <div class="card border-0 shadow-sm empty-state">
                <div class="card-body py-5 text-center">
                    <p class="h5 fw-semibold mb-2">Geen auto's beschikbaar</p>
                    <p class="text-muted mb-3">Kom later terug — er worden voortdurend nieuwe auto's toegevoegd!</p>
                </div>
            </div>
        @else
            <div class="row g-4 align-items-stretch">
                @foreach ($cars as $car)
                    @php($isFeatured = in_array($car->id, $featuredCarIds, true))
                    <div
                        class="col-12 col-md-6 {{ $isFeatured ? 'col-lg-6' : 'col-lg-4' }} car-result"
                        data-car-card
                        data-search-value="{{ strtolower($car->brand . ' ' . $car->model) }}"
                    >
                        <a href="{{ route('cars.show', $car) }}" class="text-decoration-none d-block h-100">
                            <div class="card h-100 car-card shadow-sm border-0 {{ $isFeatured ? 'car-card--featured' : '' }}">
                                <div class="card-body d-flex flex-column position-relative">
                                    @if ($isFeatured)
                                        <span class="car-card-ribbon">Uitgelicht</span>
                                    @endif

                                    <div class="mb-3">
                                        <span class="badge text-bg-success">Te koop</span>
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
            <div class="card border-0 shadow-sm d-none empty-state mt-4" id="searchEmptyState">
                <div class="card-body py-5 text-center">
                    <p class="h5 fw-semibold mb-2">Geen auto's gevonden</p>
                    <p class="text-muted mb-3">Probeer een andere merk- of modelnaam.</p>
                    <button type="button" class="btn btn-outline-primary" id="resetSearchButton">Zoekopdracht wissen</button>
                </div>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('carSearch');
            const clearButton = document.getElementById('clearCarSearch');
            const resetButton = document.getElementById('resetSearchButton');
            const visibleCount = document.getElementById('carsVisibleCount');
            const resultHint = document.getElementById('searchResultHint');
            const emptyState = document.getElementById('searchEmptyState');
            const cards = Array.from(document.querySelectorAll('[data-car-card]'));

            const updateVisibleCount = (count) => {
                if (visibleCount) {
                    visibleCount.textContent = count;
                }
            };

            const filterCars = () => {
                const query = (searchInput?.value || '').trim().toLowerCase();
                let visible = 0;

                cards.forEach((card) => {
                    const searchValue = card.getAttribute('data-search-value') || '';
                    const matches = query === '' || searchValue.includes(query);
                    card.classList.toggle('d-none', !matches);
                    if (matches) {
                        visible += 1;
                    }
                });

                updateVisibleCount(visible);

                if (resultHint) {
                    resultHint.textContent = query === ''
                        ? 'Typ om te zoeken'
                        : `${visible} resultaat${visible === 1 ? '' : 'en'} gevonden`;
                }

                if (emptyState) {
                    emptyState.classList.toggle('d-none', visible !== 0);
                }
            };

            if (searchInput) {
                searchInput.addEventListener('input', filterCars);
            }

            if (clearButton) {
                clearButton.addEventListener('click', function() {
                    if (searchInput) {
                        searchInput.value = '';
                        searchInput.focus();
                    }
                    filterCars();
                });
            }

            if (resetButton) {
                resetButton.addEventListener('click', function() {
                    if (searchInput) {
                        searchInput.value = '';
                        searchInput.focus();
                    }
                    filterCars();
                });
            }

            filterCars();
        });
    </script>
@endsection
