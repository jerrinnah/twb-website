/* ============================================================
   THE WALKING BILLBOARD — Shared behaviour
   ============================================================ */
(function () {
  'use strict';

  /* ── Scroll reveal ──────────────────────────────── */
  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (e) {
      if (e.isIntersecting) {
        e.target.classList.add('visible');
        io.unobserve(e.target);
      }
    });
  }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });

  document.querySelectorAll('.fade-in').forEach(function (el) {
    // stagger siblings sharing a parent for a cascading reveal
    var siblings = el.parentElement.querySelectorAll(':scope > .fade-in');
    siblings.forEach(function (s, si) { s.style.transitionDelay = (si * 0.08) + 's'; });
    io.observe(el);
  });

  /* ── Mobile nav toggle ──────────────────────────── */
  var toggle = document.querySelector('.nav-toggle');
  var navLinks = document.querySelector('.nav-links');
  if (toggle && navLinks) {
    toggle.addEventListener('click', function () {
      navLinks.classList.toggle('open');
    });
    navLinks.querySelectorAll('a').forEach(function (a) {
      a.addEventListener('click', function () { navLinks.classList.remove('open'); });
    });
  }

  /* ── Service accordion rows ─────────────────────── */
  document.querySelectorAll('.service-row').forEach(function (row) {
    row.addEventListener('click', function () { row.classList.toggle('open'); });
  });

  /* ── Testimonial carousel arrows (mobile scroll) ── */
  var testiGrid = document.querySelector('.testi-grid');
  if (testiGrid) {
    var btns = document.querySelectorAll('.testi-nav-btn');
    if (btns.length === 2) {
      btns[0].addEventListener('click', function () { testiGrid.scrollBy({ left: -320, behavior: 'smooth' }); });
      btns[1].addEventListener('click', function () { testiGrid.scrollBy({ left: 320, behavior: 'smooth' }); });
    }
  }

  /* ── Form handling (no backend → mailto handoff) ── */
  var BRAND_EMAIL = 'hello@thewalkingbillboard.com';

  function flashNote(form, msg) {
    var note = form.querySelector('.form-note');
    if (!note) {
      note = document.createElement('p');
      note.className = 'form-note';
      form.appendChild(note);
    }
    note.textContent = msg;
    note.classList.add('success');
  }

  // Quick email-capture forms (hero / homepage contact)
  document.querySelectorAll('form[data-form="lead"]').forEach(function (form) {
    form.addEventListener('submit', function (ev) {
      ev.preventDefault();
      var email = (form.querySelector('input[type="email"]') || {}).value || '';
      if (!email) return;
      var subject = encodeURIComponent('Free Consultation Request — The Walking Billboard');
      var body = encodeURIComponent('Hi TWB team,\n\nI’d like to book a free brand consultation.\n\nMy email: ' + email + '\n\nThanks!');
      window.location.href = 'mailto:' + BRAND_EMAIL + '?subject=' + subject + '&body=' + body;
      flashNote(form, 'Opening your email app… we’ll be in touch shortly.');
    });
  });

  // Full contact form (contact page)
  document.querySelectorAll('form[data-form="contact"]').forEach(function (form) {
    form.addEventListener('submit', function (ev) {
      ev.preventDefault();
      var get = function (id) { var el = form.querySelector('#' + id); return el ? el.value : ''; };
      var name = get('name'), email = get('email'), company = get('company'), message = get('message');
      var subject = encodeURIComponent('New Inquiry from ' + (name || 'Website') + (company ? ' (' + company + ')' : ''));
      var body = encodeURIComponent(
        'Name: ' + name + '\n' +
        'Email: ' + email + '\n' +
        'Brand / Company: ' + company + '\n\n' +
        'Message:\n' + message
      );
      window.location.href = 'mailto:' + BRAND_EMAIL + '?subject=' + subject + '&body=' + body;
      flashNote(form, 'Thanks ' + (name || '') + '! Opening your email app to send your inquiry.');
    });
  });

  /* ── Dynamic year ───────────────────────────────── */
  document.querySelectorAll('#year').forEach(function (el) {
    el.textContent = new Date().getFullYear();
  });
})();
