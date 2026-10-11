{{-- Cloudflare Turnstile widget; $bag: az űrlap hibagyűjteménye (vagy null) --}}
@if (\App\Rules\Turnstile::enabled())
  @php($bag = $bag ?? null)
  <div class="field {{ $class ?? '' }} @if($bag && $bag->has('cf-turnstile-response')) field--err @endif">
    <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}" data-language="{{ app()->getLocale() }}" data-appearance="interaction-only"></div>
    @if ($bag && $bag->has('cf-turnstile-response'))<small class="field__err">{{ $bag->first('cf-turnstile-response') }}</small>@endif
  </div>
  @once
    @push('scripts')
    {{-- a Cloudflare szkript csak akkor töltődik be, amikor egy Turnstile-os űrlap láthatóvá válik:
         felugró ablakban az ablak megnyitásakor, oldalon lévő űrlapnál (pl. regisztráció) azonnal (Geza, 2026-10-11) --}}
    <script>
    (function () {
      var loaded = false;
      function load() {
        if (loaded) return;
        loaded = true;
        var s = document.createElement('script');
        s.src = 'https://challenges.cloudflare.com/turnstile/v0/api.js';
        s.async = true;
        document.head.appendChild(s);
      }
      var widgets = document.querySelectorAll('.cf-turnstile');
      for (var i = 0; i < widgets.length; i++) {
        var dlg = widgets[i].closest('dialog');
        if (!dlg || dlg.open) { load(); return; }
        new MutationObserver(function (m, obs) {
          if (m[0].target.open) { obs.disconnect(); load(); }
        }).observe(dlg, { attributes: true, attributeFilter: ['open'] });
      }
    })();
    </script>
    @endpush
  @endonce
@endif
