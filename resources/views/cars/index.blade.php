@extends('layouts.app')

@section('content')
    <div class="py-4 public-listing-page">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">Alle beschikbare auto's</h1>
                <p class="text-muted mb-0">{{ $cars->total() }} auto('s) beschikbaar voor jouw keuze</p>
            </div>
            @guest
                <a href="{{ route('register') }}" class="btn btn-outline-primary">Aanbieden? Registreer hier</a>
            @endguest
            @auth
                <a href="{{ route('cars.create.step1') }}" class="btn btn-primary px-4">Mijn auto aanbieden</a>
            @endauth
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
            <div class="row g-3">
                @foreach ($cars as $car)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100 car-card shadow-sm border-0">
                            <div class="card-body d-flex flex-column">
                                <div class="mb-3">
                                    <span class="badge text-bg-success">Te koop</span>
                                </div>
                                <h2 class="h5 fw-bold mb-1">{{ $car->brand }} {{ $car->model }}</h2>
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
                    </div>
                @endforeach
            </div>

            @if ($cars->hasPages())
                <div class="mt-4 d-flex justify-content-center">
                    {{ $cars->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection
