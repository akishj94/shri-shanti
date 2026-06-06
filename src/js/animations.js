// animations.js
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { SplitText } from 'gsap/SplitText';

gsap.registerPlugin(ScrollTrigger, SplitText);

// ─────────────────────────────────────────────────────────────
// 1. SET INITIAL STATES
// ─────────────────────────────────────────────────────────────

export function setInitialStates() {

  gsap.set('.fade-up', { opacity: 0, y: 40 });
  gsap.set('.fade-in', { opacity: 0 });
  gsap.set('.scale-down', { transformOrigin: 'top center' });

  document.querySelectorAll('.fade-up-group').forEach((section) => {
    const items = section.querySelectorAll('.fade-up-item');
    if (items.length) gsap.set(items, { opacity: 0, y: 40 });
  });

  document.querySelectorAll('.fade-in-group').forEach((section) => {
    const items = section.querySelectorAll('.fade-in-item');
    if (items.length) gsap.set(items, { opacity: 0 });
  });
}

// ─────────────────────────────────────────────────────────────
// 2. SCROLL ANIMATIONS
// ─────────────────────────────────────────────────────────────

export function initScrollAnimations() {

  // ── Fade up (standalone) ───────────────────────────────
  ScrollTrigger.batch('.fade-up', {
    start: 'top 99%',
    once: true,
    onEnter: (els) => gsap.to(els, {
      opacity: 1,
      y: 0,
      duration: 0.8,
      ease: 'power3.out',
      stagger: 0.1,
    }),
  });

  // ── Fade in (standalone) ───────────────────────────────
  ScrollTrigger.batch('.fade-in', {
    start: 'top 99%',
    once: true,
    onEnter: (els) => gsap.to(els, {
      opacity: 1,
      duration: 0.8,
      ease: 'power2.out',
      stagger: 0.1,
    }),
  });

  // ── Fade up (staggered group) ──────────────────────────
  document.querySelectorAll('.fade-up-group').forEach((section) => {
    const items = section.querySelectorAll('.fade-up-item');
    if (!items.length) return;

    ScrollTrigger.batch(items, {
      start: 'top 99%',
      once: true,
      onEnter: (els) => gsap.to(els, {
        opacity: 1,
        y: 0,
        duration: 0.8,
        ease: 'power3.out',
        stagger: 0.12,
      }),
    });
  });

  // ── Fade in (staggered group) ──────────────────────────
  document.querySelectorAll('.fade-in-group').forEach((section) => {
    const items = section.querySelectorAll('.fade-in-item');
    if (!items.length) return;

    ScrollTrigger.batch(items, {
      start: 'top 99%',
      once: true,
      onEnter: (els) => gsap.to(els, {
        opacity: 1,
        duration: 0.8,
        ease: 'power2.out',
        stagger: 0.12,
      }),
    });
  });

  // ── Mask up ────────────────────────────────────────────
  gsap.utils.toArray('.mask-up').forEach((el) => {
    SplitText.create(el, {
      type: 'lines,words',
      mask: 'lines',
      autoSplit: true,
      onSplit: (self) => {
        gsap.set(self.words, { yPercent: 110 });

        return gsap.to(self.words, {
          yPercent: 0,
          duration: 0.8,
          ease: 'power3.out',
          stagger: 0.04,
          scrollTrigger: {
            trigger: el,
            start: 'top 99%',
            once: true,
          },
        });
      },
    });
  });

  // ── Fade text (char opacity) ───────────────────────────
  gsap.utils.toArray('.fade-text').forEach((el) => {
    SplitText.create(el, {
      type: 'chars',
      autoSplit: true,
      onSplit: (self) => {
        gsap.set(self.chars, { opacity: 0.2 });

        return gsap.to(self.chars, {
          opacity: 1,
          stagger: 0.02,
          ease: 'none',
          duration: 0,
          scrollTrigger: {
            trigger: el,
            start: 'top 80%',
          },
        });
      },
    });
  });

  // ── Scale down (desktop only) ──────────────────────────
  const mm = gsap.matchMedia();

  document.querySelectorAll('.scale-down').forEach((el) => {
    const target = el.firstElementChild;

    mm.add('(min-width: 768px)', () => {
      gsap.to(target, {
        scale: 0.85,
        y: '60%',
        ease: 'none',
        scrollTrigger: {
          trigger: el,
          start: 'top top',
          end: 'bottom+=50% top',
          scrub: true,
        },
      });
    });
  });
}