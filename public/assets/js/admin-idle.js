/* Admin: automatikus kijelentkezés tétlenség után, előtte figyelmeztetéssel (Geza, 2026-10-09) */
(function () {
  'use strict';
  var d = document.body.dataset;
  var LIMIT = (parseInt(d.idleMinutes, 10) || 30) * 60 * 1000;
  var WARN = 2 * 60 * 1000;        // ennyivel a kilépés előtt jön a figyelmeztetés
  var PING_EVERY = 45 * 1000;      // tevékenység közben ennyi időnként jelzünk a szervernek
  var KEY = 'filadelfiaAdminActivity';
  var meta = document.querySelector('meta[name="csrf-token"]');
  var csrf = meta ? meta.content : '';
  var dlg = document.getElementById('idle-dialog');
  var out = document.getElementById('idle-countdown');
  var last = Date.now(), lastPing = last, lastStored = 0, done = false;

  function store(t) { try { localStorage.setItem(KEY, String(t)); } catch (e) {} }
  function shared() { try { return parseInt(localStorage.getItem(KEY), 10) || 0; } catch (e) { return 0; } }
  function post(url) {
    return fetch(url, {
      method: 'POST', credentials: 'same-origin', keepalive: true,
      headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
    });
  }
  function toLogin() { done = true; location.href = d.idleLogin; }
  function leave() {
    if (done) return;
    done = true;
    post(d.idleLogout).catch(function () {}).then(function () { location.href = d.idleLogin; });
  }
  function ping() {
    lastPing = Date.now();
    post(d.idlePing).then(function (r) { if (r.status === 401 || r.status === 419) toLogin(); }).catch(function () {});
  }
  function activity() {
    if (done || (dlg && dlg.open)) return;
    last = Date.now();
    if (last - lastStored > 1000) { lastStored = last; store(last); }
    if (last - lastPing > PING_EVERY) ping();
  }
  function stay() {
    last = Date.now(); lastStored = last; store(last);
    if (dlg && dlg.open) dlg.close();
    ping();
  }
  function show(secs) {
    if (dlg && !dlg.open) { if (dlg.showModal) dlg.showModal(); else dlg.setAttribute('open', ''); }
    if (out) out.textContent = Math.floor(secs / 60) + ':' + ('0' + (secs % 60)).slice(-2);
  }

  ['mousemove', 'mousedown', 'keydown', 'scroll', 'wheel', 'touchstart'].forEach(function (ev) {
    window.addEventListener(ev, activity, { passive: true, capture: true });
  });
  var bs = document.getElementById('idle-stay'), bl = document.getElementById('idle-leave');
  if (bs) bs.addEventListener('click', stay);
  if (bl) bl.addEventListener('click', leave);
  if (dlg) dlg.addEventListener('cancel', function (e) { e.preventDefault(); stay(); });
  store(last);

  setInterval(function () {
    if (done) return;
    var s = shared();
    if (s > last) last = s;                       // egy másik admin lapon volt tevékenység
    var idle = Date.now() - last;
    if (idle >= LIMIT) { leave(); return; }
    if (idle >= LIMIT - WARN) show(Math.ceil((LIMIT - idle) / 1000));
    else if (dlg && dlg.open) dlg.close();        // másik lapon megerősítették
  }, 1000);
})();
