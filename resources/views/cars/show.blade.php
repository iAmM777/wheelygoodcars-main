@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <!-- Back Link -->
            <a href="{{ route('cars.index') }}" class="btn btn-outline-secondary btn-sm mb-4">
                ← Terug naar overzicht
            </a>

            <!-- Car Detail Card -->
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <!-- Image Section -->
                    <div class="car-detail-image-wrapper bg-light d-flex align-items-center justify-content-center" style="height: 400px; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);">
                    @php
                        $imageSrc = null;
                        if ($car->image) {
                            $basename = basename($car->image);
                            $publicCopy = public_path('cars/' . $basename);
                            if (file_exists($publicCopy)) {
                                $imageSrc = asset('cars/' . $basename);
                            } else {
                                $imageSrc = asset('storage/' . $car->image);
                            }
                        }
                    @endphp
                    @if($imageSrc)
                        <a href="#" id="show-image-link" aria-label="Bekijk volledige afbeelding">
                            <img src="{{ $imageSrc }}" alt="{{ $car->brand }} {{ $car->model }}" id="show-image-preview" class="img-fluid" style="height: 100%; object-fit: cover; cursor:zoom-in;">
                        </a>
                    @else
                        <div class="text-center text-muted">
                            <svg width="80" height="80" fill="currentColor" viewBox="0 0 16 16" style="margin-bottom: 1rem;">
                                <path d="M.54 3.87.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7.138A2 2 0 0 1 13.174 14H2.826a2 2 0 0 1-1.991-1.881L.54 5.005a2 2 0 0 1 .001-.998zM5 12a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zm7 0a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zM1.39 6.41A1 1 0 0 0 1 7a1 1 0 1 0 1-1 1 1 0 0 0-.61.41z"/>
                            </svg>
                            <p class="mt-3">Geen afbeelding beschikbaar</p>
                        </div>
                    @endif
                </div>

                <!-- Details Section -->
                <div class="card-body p-5">
                    <!-- Title -->
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <h1 class="card-title mb-0">{{ $car->brand }} {{ $car->model }}</h1>
                            <small class="text-muted">Bouwjaar {{ $car->production_year ?? 'onbekend' }}</small>
                            @if($car->tags->isNotEmpty())
                                <div class="mt-3">
                                    @foreach($car->tags as $tag)
                                        <span class="badge tag-badge bg-light text-dark border">{{ $tag->name }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success p-3" style="font-size: 1.25rem;">
                                €{{ number_format($car->price, 2, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- License Plate -->
                    <div class="mb-4 text-center">
                        <span class="license-plate-badge-detail">{{ $car->license_plate }}</span>
                    </div>

                    <!-- Key Specs Row -->
                    <div class="row mb-5 text-center">
                        <div class="col-md-3">
                            <div class="spec-box p-3 border-bottom border-lg-0">
                                <div class="spec-value">{{ $car->mileage ?? '0' }} km</div>
                                <div class="spec-label text-muted small">Kilometerstand</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="spec-box p-3 border-bottom border-lg-0">
                                <div class="spec-value">{{ $car->doors ?? '-' }}</div>
                                <div class="spec-label text-muted small">Deuren</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="spec-box p-3 border-bottom border-lg-0">
                                <div class="spec-value">{{ $car->seats ?? '-' }}</div>
                                <div class="spec-label text-muted small">Zitplaatsen</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="spec-box p-3">
                                <div class="spec-value">{{ $car->weight ?? '-' }} kg</div>
                                <div class="spec-label text-muted small">Gewicht</div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <div class="card border-0 bg-light rounded-4 h-100">
                                <div class="card-body text-center py-4">
                                    <div class="text-uppercase small text-muted mb-1">Totale views</div>
                                    <div class="display-6 fw-bold mb-0">{{ number_format($car->views, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="card border-0 bg-light rounded-4 h-100">
                                <div class="card-body text-center py-4">
                                    <div class="text-uppercase small text-muted mb-1">Views vandaag</div>
                                    <div class="display-6 fw-bold mb-0">{{ number_format($car->views_today, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Details -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <h6 class="fw-bold text-uppercase text-muted mb-2" style="font-size: 0.85rem; letter-spacing: 0.05em;">
                                🎨 Kleur
                            </h6>
                            <p class="mb-0">{{ $car->color ?? 'Niet gespecificeerd' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="fw-bold text-uppercase text-muted mb-2" style="font-size: 0.85rem; letter-spacing: 0.05em;">
                                👤 Verkoper
                            </h6>
                            <p class="mb-0">{{ $car->user->name }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                            <h6 class="fw-bold text-uppercase text-muted mb-0" style="font-size: 0.85rem; letter-spacing: 0.05em;">
                                📄 PDF voor printen
                            </h6>
                            <a href="{{ route('cars.pdf', $car) }}" class="btn btn-outline-primary btn-sm" target="_blank" rel="noopener">Open PDF</a>
                        </div>
                        <div class="card border-0 bg-light rounded-4 overflow-hidden shadow-sm">
                            <div class="card-body">
                                <p class="text-muted mb-3">Deze PDF bevat de auto-gegevens in een printvriendelijk format voor in de voorruit of showroom.</p>
                                <div class="ratio ratio-16x9 bg-white border rounded-3">
                                    <iframe src="{{ route('cars.pdf', $car) }}" title="PDF van {{ $car->brand }} {{ $car->model }}" loading="lazy"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Created At -->
                    <div class="text-muted small border-top pt-3">
                        📅 Aangeboden op {{ $car->created_at->format('d F Y') }}
                    </div>
                </div>
            </div>

            <!-- Contact Section -->
            <div class="card mt-4 shadow-sm border-0 rounded-4 p-4">
                <h5 class="mb-3">
                    💬 Interesse in deze auto?
                </h5>
                <p class="text-muted mb-4">
                    Helaas kunnen we momenteel geen interesse uitdrukken via deze website. 
                    Neem contact op met de verkoper voor meer informatie.
                </p>
                @auth
                    @if(auth()->user()->id === $car->user_id)
                        <div class="alert alert-info mb-0">
                            <strong>Je auto:</strong> Dit is je eigen aanbieding.
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
    <div id="viewsToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true" style="min-width: 380px;">
        <div class="toast-header bg-info text-white">
            <strong class="me-auto" style="font-size: 1.15rem;">👀 Druk!</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body" style="font-size: 1.2rem; padding: 1.5rem; font-weight: 500;">
            {{ $car->views }} klanten bekeken deze auto vandaag! 🚗💨
        </div>
    </div>
</div>

<script>
    function showViewsToast() {
        const toastElement = document.getElementById('viewsToast');
        
        if (toastElement) {
            // Use simple display instead of Bootstrap Toast API
            toastElement.classList.add('show');
            toastElement.style.display = 'block';
        }
    }

    // Wait for DOM to be fully ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                showViewsToast();
                }, 10000);
        });
    } else {
        setTimeout(function() {
            showViewsToast();
        }, 10000);
    }
</script>
@endsection

@push('scripts')
<script>
    (function(){
        const preview = document.getElementById('show-image-preview');
        const link = document.getElementById('show-image-link');
        if (!preview || !link) return;

        const lightbox = document.createElement('div');
        lightbox.id = 'imageLightboxShow';
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
