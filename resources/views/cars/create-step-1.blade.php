@extends('layouts.app')

@section('content')
    <div class="py-4">
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
