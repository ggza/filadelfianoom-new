/* Beúszó menü (hamburger) minden oldalon: nyitás/zárás gombbal, X-szel, Escape-pel és a sötétített háttérre kattintva;
   nyitva a fókusz a panelen belül marad, záráskor visszakerül a gombra; az oldal közben nem görgethető. */
(function () {
  'use strict';
  var menu = document.getElementById('site-menu');
  var toggle = document.querySelector('.menu-toggle');
  if (!menu || !toggle) return;

  var panel = menu.querySelector('.offcanvas__panel');
  var body = document.body;
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var closeTimer = null;
  var FOCUSABLE = 'a[href], button:not([disabled]), input:not([disabled]), [tabindex]:not([tabindex="-1"])';

  function isOpen() { return body.classList.contains('menu-open'); }

  function open() {
    if (isOpen()) return;
    clearTimeout(closeTimer);
    menu.hidden = false;
    void panel.offsetWidth; // újrarajzolás kikényszerítése, hogy a beúszó átmenet látsszon
    body.classList.add('menu-open');
    body.style.overflow = 'hidden';
    toggle.setAttribute('aria-expanded', 'true');
    var first = panel.querySelector('.offcanvas__nav a, .offcanvas__nav button') || panel;
    setTimeout(function () { first.focus({ preventScroll: true }); }, reduceMotion ? 0 : 60);
  }

  function close(returnFocus) {
    if (!isOpen()) return;
    body.classList.remove('menu-open');
    body.style.overflow = '';
    toggle.setAttribute('aria-expanded', 'false');
    closeTimer = setTimeout(function () { menu.hidden = true; }, reduceMotion ? 0 : 320);
    if (returnFocus) toggle.focus({ preventScroll: true });
  }

  toggle.addEventListener('click', function () { if (isOpen()) close(true); else open(); });
  menu.querySelectorAll('[data-menu-close]').forEach(function (el) {
    el.addEventListener('click', function () { close(true); });
  });

  document.addEventListener('keydown', function (ev) {
    if (!isOpen()) return;
    if (ev.key === 'Escape') { ev.preventDefault(); close(true); return; }
    if (ev.key !== 'Tab') return;
    var items = Array.prototype.filter.call(panel.querySelectorAll(FOCUSABLE), function (el) { return el.offsetParent !== null; });
    if (!items.length) return;
    var first = items[0], last = items[items.length - 1];
    if (ev.shiftKey && (document.activeElement === first || document.activeElement === panel)) { ev.preventDefault(); last.focus(); }
    else if (!ev.shiftKey && document.activeElement === last) { ev.preventDefault(); first.focus(); }
  });

  // menüpontra kattintva zár (a görgetést a site.js horgonykezelője végzi)
  menu.addEventListener('click', function (ev) {
    var link = ev.target.closest('.offcanvas__nav a, .offcanvas__foot a');
    if (link) close(false);
  });

  window.FilaMenu = { open: open, close: close, isOpen: isOpen };
})();
