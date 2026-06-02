@extends('layouts.app')

@section('content')
    <div class="py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h1 class="h3 mb-1">Tag statistieken</h1>
                <p class="text-muted mb-0">Overzicht van hoe vaak tags gebruikt worden, uitgesplitst naar actieve en verkochte auto's.</p>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-12 col-md-3">
                <div class="card border-0 shadow-sm stats-card h-100">
                    <div class="card-body">
                        <p class="text-uppercase small text-muted mb-1">Tags</p>
                        <p class="h4 mb-0">{{ $totals['tags'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card border-0 shadow-sm stats-card h-100">
                    <div class="card-body">
                        <p class="text-uppercase small text-muted mb-1">Gebruik totaal</p>
                        <p class="h4 mb-0">{{ $totals['usage'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card border-0 shadow-sm stats-card h-100">
                    <div class="card-body">
                        <p class="text-uppercase small text-muted mb-1">Actief</p>
                        <p class="h4 mb-0 text-success">{{ $totals['active'] }}</p>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-3">
                <div class="card border-0 shadow-sm stats-card h-100">
                    <div class="card-body">
                        <p class="text-uppercase small text-muted mb-1">Verkocht</p>
                        <p class="h4 mb-0 text-secondary">{{ $totals['sold'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm table-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tag</th>
                            <th class="text-center">Totaal</th>
                            <th class="text-center text-success">Actief</th>
                            <th class="text-center text-secondary">Verkocht</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tags as $tag)
                            <tr>
                                <td>
                                    <strong>{{ $tag->name }}</strong>
                                </td>
                                <td class="text-center">{{ $tag->cars_count }}</td>
                                <td class="text-center text-success">{{ $tag->active_cars_count }}</td>
                                <td class="text-center text-secondary">{{ $tag->sold_cars_count }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Er zijn nog geen tags beschikbaar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection