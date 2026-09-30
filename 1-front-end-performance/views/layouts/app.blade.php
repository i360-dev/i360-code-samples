<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'Insight360 provides enterprise IT services, cybersecurity solutions, AI consulting, and virtual CIO/CTO services.')">
    <title>@yield('title', 'Insight360 - Enterprise IT Solutions')</title>

    <!-- Preloads pushed from views/components (e.g. the hero LCP image) - keep this first -->
    @stack('preload')

    <!-- Connection hints -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('android-chrome-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="512x512" href="{{ asset('android-chrome-512x512.png') }}">

    <!-- Bootstrap 5.3.0, trimmed to classes the site uses (npm run vendor-css). Kept render-blocking: layout depends on it -->
    <link href="{{ asset('vendor/bootstrap/css/bootstrap.trim.min.css') }}?v={{ filemtime(public_path('vendor/bootstrap/css/bootstrap.trim.min.css')) }}" rel="stylesheet">

    <!-- Site Styles (consolidated) -->
    <link rel="stylesheet" href="{{ asset('css/site.css') }}">

    <!-- Cookie Styles -->
    <link rel="stylesheet" href="{{ asset('css/cookies.css') }}">

    <!-- Non-blocking: Font Awesome, Google Fonts, AOS (media="print" swap; noscript fallback) -->
    <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/fa.trim.min.css') }}?v={{ filemtime(public_path('vendor/fontawesome/css/fa.trim.min.css')) }}" media="print" onload="this.media='all'">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet" media="print" onload="this.media='all'">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet" media="print" onload="this.media='all'">
    <noscript>
        <link rel="stylesheet" href="{{ asset('vendor/fontawesome/css/fa.trim.min.css') }}?v={{ filemtime(public_path('vendor/fontawesome/css/fa.trim.min.css')) }}">
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800;900&family=Inter:wght@300;400;500;600&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    </noscript>

    <!-- Page-specific styles pushed from individual blade files -->
    @stack('styles')
</head>
<body>

<!-- Particles Background -->
<div id="particles-js"></div>

<!-- Content Wrapper -->
<div class="content-wrapper">
    @include('components.navbar')
    @yield('content')
    @include('components.footer')
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Particles.js -->
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>

<!-- Typed.js -->
<script src="https://cdn.jsdelivr.net/npm/typed.js@2.0.12"></script>

<!-- AOS Animation -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<!-- Site Scripts (consolidated) -->
<script src="{{ asset('js/site.js') }}"></script>

<!-- Page-specific scripts pushed from individual blade files -->
@stack('scripts')

<!-- Cookie Consent -->
@include('components.cookie-consent')
<script src="{{ asset('js/tracking-scripts.js') }}"></script>
<script src="{{ asset('js/cookies.js') }}"></script>

</body>
</html>
