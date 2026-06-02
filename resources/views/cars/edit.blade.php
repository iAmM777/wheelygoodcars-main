@extends('layouts.app')

@section('content')
    <div class="py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h3 mb-0">Bewerk aanbieding</h1>
            <div class="d-flex gap-2">
                <a href="{{ route('cars.pdf', $car) }}" target="_blank" rel="noopener" class="btn btn-outline-primary">Genereer PDF</a>
                <a href="{{ route('cars.my-offers') }}" class="btn btn-outline-secondary">Terug</a>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form method="POST" action="{{ route('cars.update', $car) }}" enctype="multipart/form-data">
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

                        <div class="col-12 mt-3">
                            <label for="image" class="form-label">Foto (vervangen)</label>
                            @php
                                $editImageSrc = null;
                                if ($car->image) {
                                    $basename = basename($car->image);
                                    $publicCopy = public_path('cars/' . $basename);
                                    if (file_exists($publicCopy)) {
                                        $editImageSrc = asset('cars/' . $basename);
                                    } else {
                                        $editImageSrc = asset('storage/' . $car->image);
                                    }
                                }
                            @endphp
                            @if($editImageSrc)
                                <div class="mb-2">
                                    <a href="#" id="edit-image-link" aria-label="Bekijk volledige afbeelding">
                                        <img src="{{ $editImageSrc }}" alt="{{ $car->brand }} {{ $car->model }}" id="edit-image-preview" class="img-thumbnail" style="width:100%; max-height:360px; height:360px; object-fit:cover; cursor:zoom-in;">
                                    </a>
                                </div>
                            @endif
                            <input id="image" name="image" type="file" accept="image/*" class="form-control @error('image') is-invalid @enderror">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
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

@push('scripts')
<script>
    (function(){
        const preview = document.getElementById('edit-image-preview');
        const link = document.getElementById('edit-image-link');
        if (!preview || !link) return;

        // Create lightbox elements
        const lightbox = document.createElement('div');
        lightbox.id = 'imageLightbox';
        Object.assign(lightbox.style, {
            position: 'fixed',
            inset: '0',
            background: 'rgba(0,0,0,0.8)',
            display: 'none',
            alignItems: 'center',
            justifyContent: 'center',
            zIndex: 2000,
            padding: '2rem'
        });

        const closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.innerHTML = '\u2715';
        Object.assign(closeBtn.style, {
            position: 'absolute',
            top: '1rem',
            right: '1rem',
            background: 'rgba(0,0,0,0.6)',
            color: '#fff',
            border: 'none',
            width: '36px',
            height: '36px',
            borderRadius: '18px',
            fontSize: '18px',
            cursor: 'pointer'
        });

        const img = document.createElement('img');
        img.alt = preview.alt || '';
        Object.assign(img.style, {
            maxWidth: '100%',
            maxHeight: '100%',
            borderRadius: '6px',
            boxShadow: '0 6px 30px rgba(0,0,0,0.6)'
        });

        lightbox.appendChild(closeBtn);
        lightbox.appendChild(img);
        document.body.appendChild(lightbox);

        function openLightbox(e){
            e.preventDefault();
            const src = preview.getAttribute('src');
            img.src = src;
            lightbox.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeLightbox(){
            lightbox.style.display = 'none';
            img.src = '';
            document.body.style.overflow = '';
        }

        link.addEventListener('click', openLightbox);
        closeBtn.addEventListener('click', closeLightbox);
        lightbox.addEventListener('click', function(e){ if (e.target === lightbox) closeLightbox(); });
        document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeLightbox(); });
    })();
</script>
@endpush
