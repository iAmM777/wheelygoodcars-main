@extends('layouts.app')

@section('content')
    <div class="py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Bewerk aanbieding</h1>
            <a href="{{ route('cars.my-offers') }}" class="btn btn-outline-secondary">Terug</a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('cars.update', $car) }}">
                    @csrf
                    @method('PUT')

                    @php
                        $selectedTags = array_map('intval', old('tags', $car->tags->pluck('id')->all()));
                    @endphp

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Kenteken</label>
                            <input class="form-control" value="{{ $car->license_plate }}" readonly>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">Auto</label>
                            <input class="form-control" value="{{ $car->brand }} {{ $car->model }}" readonly>
                        </div>

                        <div class="col-md-4">
                            <label for="price" class="form-label">Vraagprijs (EUR)</label>
                            <input id="price" name="price" type="number" min="0" step="0.01" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $car->price) }}" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 d-flex align-items-center">
                            <div class="form-check mt-3">
                                <input class="form-check-input" type="checkbox" value="1" id="sold" name="sold" {{ $car->sold_at ? 'checked' : '' }}>
                                <label class="form-check-label" for="sold">Verkocht</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Tags</label>
                                <span class="text-muted small">Pas hier de tags van deze aanbieding aan</span>
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                @foreach ($tags as $tag)
                                    <div class="tag-filter-pill">
                                        <input
                                            type="checkbox"
                                            class="btn-check"
                                            id="edit-tag-{{ $tag->id }}"
                                            name="tags[]"
                                            value="{{ $tag->id }}"
                                            @checked(in_array((int) $tag->id, $selectedTags, true))
                                        >
                                        <label class="btn btn-outline-secondary btn-sm rounded-pill tag-filter-pill__label" for="edit-tag-{{ $tag->id }}">
                                            {{ $tag->name }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>

                            @error('tags')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                            @error('tags.*')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">Opslaan</button>
                        <a href="{{ route('cars.my-offers') }}" class="btn btn-outline-secondary ms-2">Annuleren</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
