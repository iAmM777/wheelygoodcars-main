@extends('layouts.app')

@section('content')
    <div class="py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Alle auto's</h1>
            @auth
                <a href="{{ route('cars.create.step1') }}" class="btn btn-primary">Auto aanbieden</a>
            @endauth
        </div>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        @if ($cars->count() === 0)
            <div class="alert alert-light border">Er staan nog geen auto's te koop.</div>
        @else
            <div class="row g-3">
                @foreach ($cars as $car)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h2 class="h5">{{ $car->brand }} {{ $car->model }}</h2>
                                <p class="mb-1"><strong>Kenteken:</strong> {{ $car->license_plate }}</p>
                                <p class="mb-1"><strong>Kilometerstand:</strong> {{ number_format($car->mileage, 0, ',', '.') }} km</p>
                                <p class="mb-0"><strong>Vraagprijs:</strong> EUR {{ number_format((float) $car->price, 2, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $cars->links() }}
            </div>
        @endif
    </div>
@endsection
