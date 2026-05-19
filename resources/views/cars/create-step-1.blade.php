@extends('layouts.app')

@section('content')
    <div class="py-4">
        <div class="offer-progress mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <span class="offer-step-pill offer-step-pill--active">Stap 1</span>
                    <span class="ms-2 text-muted small">Kenteken controleren</span>
                </div>
                <span class="text-muted small">1 van 2</span>
            </div>
            <div class="progress offer-progress-bar" role="progressbar" aria-label="Voortgang auto aanbieden" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar" style="width: 50%"></div>
            </div>
        </div>

        <h1 class="h3">Auto aanbieden - stap 1 van 2</h1>
        <p class="text-muted">Vul alleen het kenteken in.</p>

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('cars.create.step1.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="license_plate" class="form-label">Kenteken</label>
                        <input
                            id="license_plate"
                            name="license_plate"
                            type="text"
                            class="form-control @error('license_plate') is-invalid @enderror"
                            value="{{ old('license_plate') }}"
                            placeholder="Bijv. 12AB34"
                            required
                        >
                        @error('license_plate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">Kenteken controleren</button>
                </form>
            </div>
        </div>
    </div>
@endsection
