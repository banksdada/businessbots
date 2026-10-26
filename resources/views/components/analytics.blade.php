@if(config('services.analytics.ga4_measurement_id') || config('services.analytics.google_site_verification'))
    {{-- Google Site Verification (for Search Console) --}}
    @if(config('services.analytics.google_site_verification'))
        <meta name="google-site-verification" content="{{ config('services.analytics.google_site_verification') }}">
    @endif

    {{-- GA4 --}}
    @if(config('services.analytics.ga4_measurement_id'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ config('services.analytics.ga4_measurement_id') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '{{ config('services.analytics.ga4_measurement_id') }}');
        </script>
    @endif
@endif