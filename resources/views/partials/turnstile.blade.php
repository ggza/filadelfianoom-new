{{-- Cloudflare Turnstile widget; $bag: az űrlap hibagyűjteménye (vagy null) --}}
@if (\App\Rules\Turnstile::enabled())
  @php($bag = $bag ?? null)
  <div class="field {{ $class ?? '' }} @if($bag && $bag->has('cf-turnstile-response')) field--err @endif">
    <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.site_key') }}" data-language="{{ app()->getLocale() }}"></div>
    @if ($bag && $bag->has('cf-turnstile-response'))<small class="field__err">{{ $bag->first('cf-turnstile-response') }}</small>@endif
  </div>
  @once
    @push('scripts')
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endpush
  @endonce
@endif
