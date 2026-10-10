/* Filadelfia Noom – saját változat
   Intro (háttér-fade + szlogen), közös carousel (hero + galéria), modalok, menü. */
(function () {
  'use strict';

  /* Időzítések ezredmásodpercben – itt lehet finomhangolni. */
  var TIMING = {
    bgFade: 2400,        // két háttérkép közti áttűnés
    bgHold: 3200,        // egy háttérkép kitartása az intro alatt
    sloganDelay: 500,    // a szlogen indulása az oldal betöltése után
    sloganIn: 1400,      // be-csúszás / fade
    sloganHold: 3600,    // a teljes szlogen kitartása
    sloganOut: 1000,     // ki-csúszás
    photoFade: 600,      // hero képváltó áttűnése (korábban 1200; Geza, 2026-10-10)
    photoInterval: 4900, // hero képváltó képenként: 600 ms áttűnés + 4300 ms teljesen látható (a látható idő az eredeti; korábban 5500 = 1200 + 4300)
    galleryInterval: 6500
  };

  var root = document.documentElement;
  var body = document.body;
  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  root.style.setProperty('--t-bg-fade', TIMING.bgFade + 'ms');
  root.style.setProperty('--t-photo-fade', TIMING.photoFade + 'ms');
  root.style.setProperty('--t-slogan-in', TIMING.sloganIn + 'ms');
  root.style.setProperty('--t-slogan-out', TIMING.sloganOut + 'ms');

  function $(sel, ctx) { return (ctx || document).querySelector(sel); }
  function $$(sel, ctx) { return Array.prototype.slice.call((ctx || document).querySelectorAll(sel)); }

  /* ---------------- Közös carousel ---------------- */
  function Carousel(el, opts) {
    this.el = el;
    this.slides = $$('.carousel__slide', el);
    this.thumbs = $$('[data-goto]', el);
    this.interval = opts.interval;
    this.index = 0;
    this.timer = null;
    this.paused = !!opts.startPaused;
    this.hold = 0; // hover/fókusz miatti ideiglenes szünet
    this.toggleBtn = $('[data-toggle]', el);
    var self = this;

    $$('[data-prev]', el).forEach(function (b) { b.addEventListener('click', function () { self.go(self.index - 1, true); }); });
    $$('[data-next]', el).forEach(function (b) { b.addEventListener('click', function () { self.go(self.index + 1, true); }); });
    this.thumbs.forEach(function (b) {
      b.addEventListener('click', function () { self.go(+b.getAttribute('data-goto'), true); });
    });
    if (this.toggleBtn) {
      this.toggleBtn.addEventListener('click', function () { self.setPaused(!self.paused); });
    }
    el.addEventListener('keydown', function (ev) {
      if (ev.target.closest('input, textarea')) return;
      if (ev.key === 'ArrowLeft') { self.go(self.index - 1, true); ev.preventDefault(); }
      if (ev.key === 'ArrowRight') { self.go(self.index + 1, true); ev.preventDefault(); }
    });
    el.addEventListener('mouseenter', function () { self.hold++; self.stop(); });
    el.addEventListener('mouseleave', function () { self.hold = Math.max(0, self.hold - 1); self.start(); });
    el.addEventListener('focusin', function () { self.hold++; self.stop(); });
    el.addEventListener('focusout', function () { self.hold = Math.max(0, self.hold - 1); self.start(); });

    // érintéses lapozás
    var x0 = null;
    el.addEventListener('touchstart', function (ev) { x0 = ev.touches[0].clientX; }, { passive: true });
    el.addEventListener('touchend', function (ev) {
      if (x0 === null) return;
      var dx = ev.changedTouches[0].clientX - x0;
      if (Math.abs(dx) > 40) self.go(self.index + (dx < 0 ? 1 : -1), true);
      x0 = null;
    });
  }
  Carousel.prototype.go = function (i, user) {
    var n = this.slides.length;
    i = (i + n) % n;
    var prev = this.index;
    this.index = i;
    this.slides.forEach(function (s, k) {
      var on = k === i;
      s.classList.toggle('is-active', on);
      if (on) s.removeAttribute('aria-hidden'); else s.setAttribute('aria-hidden', 'true');
      $$('button, a', s).forEach(function (b) { b.tabIndex = on ? 0 : -1; });
    });
    this.thumbs.forEach(function (b, k) {
      if (k === i) {
        b.setAttribute('aria-current', 'true');
        var list = b.closest('ol');
        if (list && prev !== i) {
          var li = b.parentNode;
          list.scrollTo({ left: li.offsetLeft - list.clientWidth / 2 + li.clientWidth / 2, behavior: reduceMotion ? 'auto' : 'smooth' });
        }
      } else b.removeAttribute('aria-current');
    });
    if (user) this.start();
  };
  Carousel.prototype.start = function () {
    this.stop();
    if (this.paused || this.hold || reduceMotion) return;
    var self = this;
    this.timer = setInterval(function () { self.go(self.index + 1); }, this.interval);
  };
  Carousel.prototype.stop = function () { clearInterval(this.timer); this.timer = null; };
  Carousel.prototype.setPaused = function (p) {
    this.paused = p;
    if (this.toggleBtn) {
      this.toggleBtn.classList.toggle('is-paused', p);
      this.toggleBtn.setAttribute('aria-label', this.toggleBtn.getAttribute(p ? 'data-label-play' : 'data-label-pause'));
    }
    if (p) this.stop(); else this.start();
  };

  var heroEl = $('[data-carousel="hero"]');
  var galleryEl = $('[data-carousel="gallery"]');
  var hero = heroEl ? new Carousel(heroEl, { interval: TIMING.photoInterval, startPaused: reduceMotion }) : null;
  var gallery = galleryEl ? new Carousel(galleryEl, { interval: TIMING.galleryInterval }) : null;
  if (hero && reduceMotion) hero.setPaused(true);

  // a galéria csak akkor lapoz magától, ha látszik
  if (gallery && 'IntersectionObserver' in window) {
    gallery.hold++;
    new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (en.isIntersecting) { gallery.hold = Math.max(0, gallery.hold - 1); gallery.start(); }
        else { gallery.hold++; gallery.stop(); }
      });
    }, { threshold: 0.35 }).observe(galleryEl);
  } else if (gallery) gallery.start();

  /* ---------------- Intro: háttér-fade + szlogen ---------------- */
  var bgs = $$('.hero__bg');
  var bgTimer = null;
  var introTimers = [];
  var introFinished = false;

  function cycleBackgrounds() {
    var i = 0;
    bgs[0] && bgs[0].classList.add('is-on');
    if (bgs.length < 2) return;
    bgTimer = setInterval(function () {
      bgs[i].classList.remove('is-on');
      i = (i + 1) % bgs.length;
      bgs[i].classList.add('is-on');
    }, TIMING.bgHold + TIMING.bgFade);
  }

  function finishIntro() {
    if (introFinished) return;
    introFinished = true;
    introTimers.forEach(clearTimeout);
    clearInterval(bgTimer);
    body.classList.remove('is-intro', 'intro-in');
    body.classList.add('intro-out', 'intro-done');
    if (hero) hero.start();
  }

  function playIntro() {
    if (reduceMotion) {
      bgs[0] && bgs[0].classList.add('is-on');
      finishIntro();
      return;
    }
    cycleBackgrounds();
    var t = TIMING.sloganDelay;
    introTimers.push(setTimeout(function () { body.classList.add('intro-in'); }, t));
    t += TIMING.sloganIn * 1.45 + TIMING.sloganHold;
    introTimers.push(setTimeout(function () {
      body.classList.remove('intro-in');
      body.classList.add('intro-out');
    }, t));
    t += TIMING.sloganOut * 0.6;
    introTimers.push(setTimeout(finishIntro, t));
  }

  var skip = $('[data-skip-intro]');
  if (skip) skip.addEventListener('click', finishIntro);
  // görgetés, billentyű vagy horgony-link is átugorja
  ['wheel', 'touchmove'].forEach(function (ev) {
    window.addEventListener(ev, function () { if (!introFinished) finishIntro(); }, { passive: true, once: true });
  });
  window.addEventListener('keydown', function (ev) {
    if (!introFinished && ['Tab', 'Escape', ' ', 'ArrowDown', 'PageDown', 'End', 'Home'].indexOf(ev.key) !== -1) finishIntro();
  });
  if (location.hash && location.hash.length > 1) finishIntro(); else playIntro();

  /* ---------------- Fejléc: tömör háttér görgetéskor ---------------- */
  var header = $('.site-header');
  function onScroll() { header.classList.toggle('is-solid', window.scrollY > 40); }
  window.addEventListener('scroll', onScroll, { passive: true });
  onScroll();

  /* ---------------- Beúszó menü (menu.js) ---------------- */
  // csak zárni kell innen: menüpontra kattintáskor görgetés előtt, és modal nyitásakor
  function setNav(open) {
    if (!open && window.FilaMenu) window.FilaMenu.close(false);
  }

  /* ---------------- Horgonyok: görgetés + fókusz ---------------- */
  $$('a[href^="#"]').forEach(function (a) {
    a.addEventListener('click', function (ev) {
      var id = a.getAttribute('href').slice(1);
      var target = id === 'top' ? document.body : document.getElementById(id);
      if (!target) return;
      ev.preventDefault();
      finishIntro();
      setNav(false);
      if (id === 'top') window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
      else {
        target.scrollIntoView({ behavior: reduceMotion ? 'auto' : 'smooth', block: 'start' });
        target.focus({ preventScroll: true });
      }
      history.replaceState(null, '', id === 'top' ? location.pathname + location.search : '#' + id);
    });
  });

  // aktív menüpont
  var navLinks = $$('[data-nav]');
  if ('IntersectionObserver' in window) {
    var spy = new IntersectionObserver(function (entries) {
      entries.forEach(function (en) {
        if (!en.isIntersecting) return;
        navLinks.forEach(function (l) {
          if (l.getAttribute('href') === '#' + en.target.id) l.setAttribute('aria-current', 'true');
          else l.removeAttribute('aria-current');
        });
      });
    }, { rootMargin: '-45% 0px -50% 0px' });
    $$('main > section[id]').forEach(function (s) { spy.observe(s); });
  }

  /* ---------------- Modalok ---------------- */
  var lastTrigger = null;
  function openDialog(dlg, trigger) {
    if (!dlg) return;
    lastTrigger = trigger || document.activeElement;
    // a beúszó menüből nyitva a fókusz bezáráskor a hamburger gombra térjen vissza (a menü addigra rejtett)
    if (lastTrigger && lastTrigger.closest && lastTrigger.closest('#site-menu')) lastTrigger = $('.menu-toggle');
    setNav(false);
    finishIntro();
    if (typeof dlg.showModal === 'function') dlg.showModal(); else dlg.setAttribute('open', '');
    body.style.overflow = 'hidden';
    var first = $('[data-close]', dlg);
    if (dlg.id !== 'dlg-lightbox' && first) first.focus();
  }
  function closeDialog(dlg) {
    if (!dlg || !dlg.open) return;
    if (typeof dlg.close === 'function') dlg.close(); else { dlg.removeAttribute('open'); dlg.dispatchEvent(new Event('close')); }
  }
  $$('dialog').forEach(function (dlg) {
    dlg.addEventListener('close', function () {
      body.style.overflow = '';
      if (lastTrigger && document.contains(lastTrigger)) lastTrigger.focus();
    });
    // kattintás a háttérre = bezárás
    dlg.addEventListener('click', function (ev) {
      if (ev.target === dlg) closeDialog(dlg);
    });
    $$('[data-close]', dlg).forEach(function (b) { b.addEventListener('click', function () { closeDialog(dlg); }); });
  });
  $$('[data-open]').forEach(function (b) {
    b.addEventListener('click', function () {
      var dlg = document.getElementById(b.getAttribute('data-open'));
      if (b.getAttribute('data-tab')) selectTab(b.getAttribute('data-tab'));
      else if (dlg && dlg.id === 'dlg-apply') selectTab('steps');
      openDialog(dlg, b);
    });
  });

  /* fülek a Jelentkezés modalban */
  var tabs = $$('#dlg-apply [role="tab"]');
  function selectTab(name, focus) {
    tabs.forEach(function (t) {
      var on = t.id === 'tab-' + name;
      t.setAttribute('aria-selected', on ? 'true' : 'false');
      t.tabIndex = on ? 0 : -1;
      document.getElementById(t.getAttribute('aria-controls')).hidden = !on;
      if (on && focus) t.focus();
    });
    var bodyEl = $('#dlg-apply .modal__body');
    if (bodyEl) bodyEl.scrollTop = 0;
  }
  tabs.forEach(function (t, i) {
    t.addEventListener('click', function () { selectTab(t.id.slice(4)); });
    t.addEventListener('keydown', function (ev) {
      var d = ev.key === 'ArrowRight' ? 1 : ev.key === 'ArrowLeft' ? -1 : 0;
      if (!d) return;
      ev.preventDefault();
      selectTab(tabs[(i + d + tabs.length) % tabs.length].id.slice(4), true);
    });
  });

  $$('[data-tab-go]').forEach(function (b) {
    b.addEventListener('click', function () { selectTab(b.getAttribute('data-tab-go'), true); });
  });

  /* próba-űrlap: nem küld semmit */
  $$('[data-demo-form]').forEach(function (f) {
    f.addEventListener('submit', function (ev) {
      ev.preventDefault();
      var st = $('.apply-form__status', f);
      st.textContent = st.getAttribute('data-done');
    });
  });

  /* ---------------- Galériakép modalban ---------------- */
  var lb = document.getElementById('dlg-lightbox');
  var items = window.FILA_GALLERY || [];
  var lbIndex = 0;
  function showLb(i) {
    lbIndex = (i + items.length) % items.length;
    var it = items[lbIndex];
    var img = $('img', lb);
    img.src = it.src;
    img.alt = it.caption;
    img.lang = it.lang;
    var cap = $('figcaption', lb);
    cap.textContent = (lbIndex + 1) + ' / ' + items.length + ' · ' + it.caption;
    cap.lang = it.lang;
  }
  if (lb && items.length) {
    $$('[data-lightbox]').forEach(function (b) {
      b.addEventListener('click', function () {
        showLb(+b.getAttribute('data-lightbox'));
        openDialog(lb, b);
        $('[data-lb-next]', lb).focus();
      });
    });
    $('[data-lb-prev]', lb).addEventListener('click', function () { showLb(lbIndex - 1); });
    $('[data-lb-next]', lb).addEventListener('click', function () { showLb(lbIndex + 1); });
    lb.addEventListener('keydown', function (ev) {
      if (ev.key === 'ArrowLeft') showLb(lbIndex - 1);
      if (ev.key === 'ArrowRight') showLb(lbIndex + 1);
    });
    lb.addEventListener('close', function () { if (gallery) gallery.go(lbIndex); });
  }

  /* ---------------- IBAN másolás ---------------- */
  $$('[data-copy]').forEach(function (b) {
    var label = b.textContent;
    b.addEventListener('click', function () {
      var done = function () {
        b.textContent = b.getAttribute('data-done');
        b.classList.add('is-done');
        setTimeout(function () { b.textContent = label; b.classList.remove('is-done'); }, 1800);
      };
      if (navigator.clipboard && window.isSecureContext) navigator.clipboard.writeText(b.getAttribute('data-copy')).then(done);
      else {
        var ta = document.createElement('textarea');
        ta.value = b.getAttribute('data-copy');
        ta.style.position = 'fixed'; ta.style.opacity = '0';
        document.body.appendChild(ta); ta.select();
        try { document.execCommand('copy'); done(); } catch (e) {}
        ta.remove();
      }
    });
  });
})();
