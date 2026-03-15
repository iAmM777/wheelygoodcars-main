@extends('layouts.app')

@section('content')
    <div class="py-4">
        <h1 class="h3">Auto aanbieden - stap 2 van 2</h1>
        <p class="text-muted">Kenteken gecontroleerd: <strong>{{ $licensePlate }}</strong></p>

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('cars.create.step2.store') }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="brand" class="form-label">Merk</label>
                            <input id="brand" name="brand" type="text" class="form-control @error('brand') is-invalid @enderror" value="{{ old('brand') }}" required>
                            @error('brand')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="model" class="form-label">Model</label>
                            <input id="model" name="model" type="text" class="form-control @error('model') is-invalid @enderror" value="{{ old('model') }}" required>
                            @error('model')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="price" class="form-label">Vraagprijs (EUR)</label>
                            <input id="price" name="price" type="number" min="0" step="0.01" class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="mileage" class="form-label">Kilometerstand</label>
                            <input id="mileage" name="mileage" type="number" min="0" class="form-control @error('mileage') is-invalid @enderror" value="{{ old('mileage') }}" required>
                            @error('mileage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="seats" class="form-label">Zitplaatsen</label>
                            <input id="seats" name="seats" type="number" min="1" class="form-control @error('seats') is-invalid @enderror" value="{{ old('seats') }}">
                            @error('seats')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="doors" class="form-label">Deuren</label>
                            <input id="doors" name="doors" type="number" min="1" class="form-control @error('doors') is-invalid @enderror" value="{{ old('doors') }}">
                            @error('doors')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="production_year" class="form-label">Bouwjaar</label>
                            <input id="production_year" name="production_year" type="number" min="1900" max="2100" class="form-control @error('production_year') is-invalid @enderror" value="{{ old('production_year') }}">
                            @error('production_year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="weight" class="form-label">Gewicht (kg)</label>
                            <input id="weight" name="weight" type="number" min="0" class="form-control @error('weight') is-invalid @enderror" value="{{ old('weight') }}">
                            @error('weight')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="color" class="form-label">Kleur</label>
                            <input id="color" name="color" type="text" class="form-control @error('color') is-invalid @enderror" value="{{ old('color') }}">
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <a href="{{ route('cars.create.step1') }}" class="btn btn-outline-secondary">Terug</a>
                        <button type="submit" class="btn btn-primary">Auto aanbieden</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
