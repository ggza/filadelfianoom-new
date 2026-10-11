{{-- Sütitájékoztató sáv és beállítások. Jelenleg csak szükséges sütik vannak, ezért nincs választható kategória;
     ha később statisztika/marketing kerül az oldalra, a consent.js CATEGORIES listájába kell felvenni. --}}
<div class="cookie-bar" id="cookie-bar" role="region" aria-label="{{ __('site.cookie_title') }}" hidden>
  <div class="cookie-bar__in">
    <p class="cookie-bar__text">
      <strong>{{ __('site.cookie_title') }}.</strong>
      {{ __('site.cookie_text') }}
      <a href="{{ route('legal.cookies') }}">{{ __('site.cookie_more') }}</a>
    </p>
    <div class="cookie-bar__actions">
      <button type="button" class="link-button cookie-bar__settings" data-cookie-settings>{{ __('site.cookie_settings') }}</button>
      <button type="button" class="pill" data-cookie-accept>{{ __('site.cookie_ok') }}</button>
    </div>
  </div>
</div>

<dialog class="modal cookie-dialog" id="cookie-dialog" aria-labelledby="cookie-dialog-title">
  <div class="modal__head">
    <h2 id="cookie-dialog-title">{{ __('site.cookie_settings') }}</h2>
    <button type="button" class="modal__close" data-cookie-close aria-label="{{ __('site.close') }}"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6L6 18"/></svg></button>
  </div>
  <div class="modal__body">
    <p>{{ __('site.cookie_dialog_intro') }}</p>
    <div class="cookie-cat">
      <label class="cookie-cat__head">
        <input type="checkbox" checked disabled>
        <span><strong>{{ __('site.cookie_necessary') }}</strong> <small>({{ __('site.cookie_always_on') }})</small></span>
      </label>
      <p class="cookie-cat__desc">{{ __('site.cookie_necessary_desc') }}</p>
    </div>
    <p class="note">{{ __('site.cookie_no_optional') }}</p>
    <p><a href="{{ route('legal.cookies') }}">{{ __('site.cookie_more') }}</a> · <a href="{{ route('legal.privacy') }}">{{ __('site.privacy_title') }}</a></p>
    <div class="apply-form__foot"><button type="button" class="pill pill--big" data-cookie-accept>{{ __('site.cookie_save') }}</button></div>
  </div>
</dialog>
