/* Sütikezelés (Geza, 2026-10-11): első látogatáskor tájékoztató sáv; a döntést a saját „cookie_consent” süti tárolja 12 hónapig.
   Jelenleg csak szükséges sütik vannak. Ha később választható kategória (pl. statisztika) kerül az oldalra:
   vedd fel a CATEGORIES listába, adj hozzá kapcsolót a cookie-banner.blade.php-ben, és a szkriptet csak
   FilaConsent.allowed('stats') igaz esetén töltsd be. A VERSION emelése újra megjeleníti a sávot mindenkinek. */
(function () {
  'use strict';
  var NAME = 'cookie_consent';
  var VERSION = '1';
  var CATEGORIES = []; // választható kategóriák, pl. ['stats']
  var MAX_AGE = 60 * 60 * 24 * 365;

  function read() {
    var m = document.cookie.match(new RegExp('(?:^|; )' + NAME + '=([^;]*)'));
    if (!m) return null;
    var parts = decodeURIComponent(m[1]).split('|');
    if (parts[0] !== VERSION) return null;
    return parts[1] ? parts[1].split(',') : [];
  }

  function write(allowed) {
    var value = VERSION + '|' + allowed.join(',');
    document.cookie = NAME + '=' + encodeURIComponent(value) + '; Max-Age=' + MAX_AGE + '; Path=/; SameSite=Lax' +
      (location.protocol === 'https:' ? '; Secure' : '');
  }

  var bar = document.getElementById('cookie-bar');
  var dialog = document.getElementById('cookie-dialog');
  var lastFocus = null;

  function showBar(show) { if (bar) bar.hidden = !show; }

  function openSettings() {
    if (!dialog) return;
    lastFocus = document.activeElement;
    if (typeof dialog.showModal === 'function') dialog.showModal(); else dialog.setAttribute('open', '');
    var first = dialog.querySelector('[data-cookie-close]');
    if (first) first.focus();
  }

  function closeSettings() {
    if (!dialog || !dialog.open) return;
    if (typeof dialog.close === 'function') dialog.close(); else dialog.removeAttribute('open');
    if (lastFocus && lastFocus.focus) lastFocus.focus();
  }

  function accept() {
    var allowed = [];
    CATEGORIES.forEach(function (c) {
      var box = dialog && dialog.querySelector('[data-cookie-cat="' + c + '"]');
      if (box && box.checked) allowed.push(c);
    });
    write(allowed);
    showBar(false);
    closeSettings();
    document.dispatchEvent(new CustomEvent('consentchange', { detail: { allowed: allowed } }));
  }

  document.addEventListener('click', function (ev) {
    var t = ev.target.closest('[data-cookie-accept], [data-cookie-settings], [data-cookie-close]');
    if (!t) return;
    ev.preventDefault();
    if (t.hasAttribute('data-cookie-accept')) accept();
    else if (t.hasAttribute('data-cookie-settings')) openSettings();
    else closeSettings();
  });
  if (dialog) {
    dialog.addEventListener('cancel', function (ev) { ev.preventDefault(); closeSettings(); });
    dialog.addEventListener('click', function (ev) { if (ev.target === dialog) closeSettings(); });
  }

  // a sávot a szerver már kirajzolta, ha nincs döntés; itt csak igazítjuk (pl. ha közben máshol döntött)
  showBar(read() === null);

  window.FilaConsent = {
    allowed: function (cat) { var a = read(); return !!a && a.indexOf(cat) !== -1; },
    open: openSettings
  };
})();
