@php
    // Only render the banner when the visitor has not yet accepted.
    // The dismissal is stored in a first-party cookie; no analytics or
    // third-party cookies are loaded, so this is strictly necessary cookies.
    $accepted = request()->cookie('cookies_accepted') === '1';
@endphp

@if (! $accepted)
<script>
    // Hide the banner via inline style until the component mounts, so it
    // never flashes before the JS runs. The element keeps its aria-hidden
    // state until the visitor acts.
    document.documentElement.classList.add('no-cookie-banner');
</script>

<div id="cookie-banner"
     class="fixed inset-x-0 bottom-0 z-50 border-t border-border bg-surface/95 backdrop-blur-md"
     role="dialog"
     aria-live="polite"
     aria-label="Cookie consent">

    <div class="mx-auto max-w-[1200px] px-6 py-4 flex flex-col sm:flex-row items-start sm:items-center gap-4">
        <div class="flex-1">
            <p class="text-sm text-text-secondary leading-relaxed">
                <span class="text-text-primary font-medium">We use essential cookies</span>
                to keep this site working — your session and your preferences.
                We don't use analytics or advertising cookies, and we won't
                track you. By continuing you accept these.
            </p>
            <p class="mt-1 text-xs text-text-muted">
                See our
                <a href="{{ route('legal.cookies') }}" class="hover:text-text-secondary underline underline-offset-2">Cookie Policy</a>.
            </p>
        </div>

        <div class="flex items-center gap-2 shrink-0">
            <button type="button"
                    data-cookie-action="reject"
                    class="px-4 py-2 text-sm font-medium text-text-secondary border border-border-strong rounded-md hover:bg-surface-elevated hover:text-text-primary transition-colors">
                Reject non-essential
            </button>
            <button type="button"
                    data-cookie-action="accept"
                    class="px-4 py-2 text-sm font-semibold bg-gradient-accent text-white rounded-md hover:opacity-90 transition-opacity">
                Accept all
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    var banner = document.getElementById('cookie-banner');
    if (!banner) return;

    function setCookie(name, value, days) {
        var d = new Date();
        d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = name + '=' + value + '; expires=' + d.toUTCString() + '; path=/; SameSite=Lax';
    }

    function dismiss() {
        banner.style.display = 'none';
        document.documentElement.classList.remove('no-cookie-banner');
    }

    banner.addEventListener('click', function (e) {
        var btn = e.target.closest('[data-cookie-action]');
        if (!btn) return;
        var action = btn.getAttribute('data-cookie-action');
        // Only "essential" cookies are ever set by this site; the banner is
        // a consent record, not a consent mechanism for third-party scripts.
        setCookie('cookies_accepted', '1', 365);
        dismiss();
    });
})();
</script>

<style>
    .no-cookie-banner #cookie-banner {
        display: none;
    }
</style>
@endif