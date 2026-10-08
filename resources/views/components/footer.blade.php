<footer class="border-t border-border mt-16 bg-surface">
    <div class="mx-auto max-w-[1200px] px-6 py-8 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <div class="w-6 h-6 rounded-md bg-gradient-accent flex items-center justify-center text-white font-semibold text-xs">B</div>
            <span class="text-sm text-text-muted">© {{ date('Y') }} {{ config('legal.company_name') }}</span>
        </div>

        <div class="flex items-center gap-5 text-sm text-text-muted">
            <a href="{{ route('legal.terms') }}" class="hover:text-text-primary">Terms</a>
            <a href="{{ route('legal.privacy') }}" class="hover:text-text-primary">Privacy</a>
            <a href="{{ route('legal.cookies') }}" class="hover:text-text-primary">Cookies</a>
            <a href="mailto:{{ config('legal.support_email') }}" class="hover:text-text-primary">Support</a>
        </div>
    </div>
</footer>
