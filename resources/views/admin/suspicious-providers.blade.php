@extends('layouts.app')

@section('content')
    <div class="py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">Opvallende aanbieders</h1>
                <p class="text-muted mb-0">Lijst van aanbieders met signalen die handmatige review verdienen.</p>
            </div>
            <a href="{{ route('cars.my-offers') }}" class="btn btn-outline-secondary">Terug naar mijn aanbod</a>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm stats-card h-100">
                    <div class="card-body">
                        <p class="text-uppercase small text-muted mb-1">Opvallende aanbieders</p>
                        <p class="h4 mb-0">{{ $summary['providers'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm stats-card h-100">
                    <div class="card-body">
                        <p class="text-uppercase small text-muted mb-1">Signalen</p>
                        <p class="h4 mb-0 text-warning">{{ $summary['flags'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="card border-0 shadow-sm stats-card h-100">
                    <div class="card-body">
                        <p class="text-uppercase small text-muted mb-1">Auto’s betrokken</p>
                        <p class="h4 mb-0 text-secondary">{{ $summary['cars'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm table-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Aanbieder</th>
                            <th>Contact</th>
                            <th class="text-center">Auto’s</th>
                            <th>Signalen</th>
                            <th>Laatste aanbod</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($providers as $provider)
                            <tr class="{{ $provider['flag_count'] >= 3 ? 'table-danger' : ($provider['flag_count'] >= 2 ? 'table-warning' : '') }}">
                                <td>
                                    <strong>{{ $provider['user']->name }}</strong><br>
                                    <small class="text-muted">{{ $provider['user']->email }}</small>
                                </td>
                                <td>
                                    @if ($provider['user']->phone_number)
                                        <div>{{ $provider['user']->phone_number }}</div>
                                    @else
                                        <span class="badge text-bg-warning">Geen telefoonnummer</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div>{{ $provider['cars_count'] }} totaal</div>
                                    <small class="text-success">{{ $provider['active_count'] }} actief</small><br>
                                    <small class="text-secondary">{{ $provider['sold_count'] }} verkocht</small>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-2 mb-2">
                                        @foreach ($provider['flags'] as $flag)
                                            <span class="badge text-bg-{{ $flag['type'] }}">{{ $flag['label'] }}</span>
                                        @endforeach
                                    </div>
                                    <small class="text-muted">
                                        @foreach ($provider['flags'] as $flag)
                                            <div>{{ $flag['description'] }}</div>
                                        @endforeach
                                    </small>
                                </td>
                                <td>
                                    @if ($provider['latest_offer_at'])
                                        {{ $provider['latest_offer_at']->format('d-m-Y') }}
                                    @else
                                        <span class="text-muted">Geen aanbod</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">Er zijn nog geen opvallende aanbieders gevonden.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection