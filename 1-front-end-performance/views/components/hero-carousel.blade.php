@php
    // Source files live in resources/images-src/ and are generated into
    // public/images/hero/ by scripts/images.mjs (800/1200/1600 px, .jpg + .webp).
    $slides = [
        ['file' => 'hero-ai',      'alt' => 'AI & Machine Learning Solutions'],       // Slide 1: AI Consulting
        ['file' => 'hero-msp',     'alt' => 'Cybersecurity & Data Protection'],       // Slide 2: MSP & Cybersecurity
        ['file' => 'hero-telecom', 'alt' => 'Network Infrastructure & Connectivity'], // Slide 3: Telecommunications & UC
        ['file' => 'hero-cio',     'alt' => 'Team Collaboration & Innovation'],       // Slide 4: Virtual CIO/CTO
        ['file' => 'hero-dev',     'alt' => 'Advanced Technology Infrastructure'],    // Slide 5: Development & Hosting
    ];
    $img = fn ($file, $w, $ext) => asset("images/hero/{$file}-{$w}.{$ext}");
@endphp
@push('preload')
    <link rel="preload" as="image" type="image/webp" fetchpriority="high"
          imagesrcset="{{ $img($slides[0]['file'], 800, 'webp') }} 800w,
                   {{ $img($slides[0]['file'], 1200, 'webp') }} 1200w,
                   {{ $img($slides[0]['file'], 1600, 'webp') }} 1600w"
          imagesizes="100vw">
@endpush
<!-- Hero Carousel Component -->
<div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
    <div class="carousel-indicators">
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
        <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="4" aria-label="Slide 5"></button>
    </div>
    <div class="carousel-inner">
        @foreach ($slides as $slide)
            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                <picture>
                    <source type="image/webp" sizes="100vw"
                            srcset="{{ $img($slide['file'], 800, 'webp') }} 800w,
                                    {{ $img($slide['file'], 1200, 'webp') }} 1200w,
                                    {{ $img($slide['file'], 1600, 'webp') }} 1600w">
                    <img src="{{ $img($slide['file'], 1200, 'jpg') }}"
                         srcset="{{ $img($slide['file'], 800, 'jpg') }} 800w,
                                 {{ $img($slide['file'], 1200, 'jpg') }} 1200w,
                                 {{ $img($slide['file'], 1600, 'jpg') }} 1600w"
                         sizes="100vw"
                         width="1600" height="900"
                         class="d-block w-100 img-fluid"
                         alt="{{ $slide['alt'] }}"
                         fetchpriority="{{ $loop->first ? 'high' : 'low' }}"
                         decoding="async">
                </picture>
            </div>
        @endforeach
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>
