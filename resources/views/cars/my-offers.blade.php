@extends('layouts.app')

@section('content')
    <div class="py-4 my-offers-page">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">Mijn aanbod</h1>
                <p class="text-muted mb-0">Overzicht van je aangeboden auto's en snelle beheeracties.</p>
            </div>
            <a href="{{ route('cars.create.step1') }}" class="btn btn-primary">Nieuwe auto aanbieden</a>
        </div>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm stats-card">
                    <div class="card-body">
                        <p class="text-uppercase small text-muted mb-1">Totaal</p>
                        <p class="h4 mb-0">{{ $stats['total'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm stats-card">
                    <div class="card-body">
                        <p class="text-uppercase small text-muted mb-1">Actief</p>
                        <p class="h4 mb-0 text-success">{{ $stats['active'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm stats-card">
                    <div class="card-body">
                        <p class="text-uppercase small text-muted mb-1">Verkocht</p>
                        <p class="h4 mb-0 text-secondary">{{ $stats['sold'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if ($cars->count() === 0)
            <div class="alert alert-light border">Je hebt nog geen auto's aangeboden.</div>
        @else
            <div class="card border-0 shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover align-middle my-offers-table mb-0">
                    <thead>
                        <tr>
                            <th>Kenteken</th>
                            <th>Auto</th>
                            <th>Kilometerstand</th>
                            <th>Prijs</th>
                            <th>Status</th>
                            <th class="text-end">Beheer</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cars as $car)
                            <tr>
                                <td>
                                    <span class="license-pill">{{ $car->license_plate }}</span>
                                </td>
                                <td>
                                    <strong>{{ $car->brand }} {{ $car->model }}</strong><br>
                                    <small class="text-muted">Bouwjaar {{ $car->production_year ?? '-' }}</small>
                                </td>
                                <td>{{ number_format($car->mileage, 0, ',', '.') }} km</td>
                                <td>EUR {{ number_format((float) $car->price, 2, ',', '.') }}</td>
                                <td>
                                    @if ($car->sold_at)
                                        <span class="badge text-bg-secondary">Verkocht</span>
                                    @else
                                        <span class="badge text-bg-success">Te koop</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-2">
                                        @if ($car->sold_at)
                                            <form method="POST" action="{{ route('cars.mark-active', $car) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-success">Activeren</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('cars.mark-sold', $car) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-secondary">Markeer verkocht</button>
                                            </form>
                                        @endif

                                        <form method="POST" action="{{ route('cars.destroy', $car) }}" onsubmit="return confirm('Weet je zeker dat je deze auto wilt verwijderen?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Verwijderen</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>

            <div class="mt-3">
                {{ $cars->links() }}
            </div>
        @endif
    </div>
@endsection
