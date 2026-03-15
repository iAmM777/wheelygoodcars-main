@extends('layouts.app')

@section('content')
    <div class="py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Mijn aanbod</h1>
            <a href="{{ route('cars.create.step1') }}" class="btn btn-primary">Nieuwe auto aanbieden</a>
        </div>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if ($cars->count() === 0)
            <div class="alert alert-light border">Je hebt nog geen auto's aangeboden.</div>
        @else
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Kenteken</th>
                            <th>Auto</th>
                            <th>Kilometerstand</th>
                            <th>Prijs</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cars as $car)
                            <tr>
                                <td>{{ $car->license_plate }}</td>
                                <td>{{ $car->brand }} {{ $car->model }}</td>
                                <td>{{ number_format($car->mileage, 0, ',', '.') }} km</td>
                                <td>EUR {{ number_format((float) $car->price, 2, ',', '.') }}</td>
                                <td>{{ $car->sold_at ? 'Verkocht' : 'Te koop' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $cars->links() }}
        @endif
    </div>
@endsection
