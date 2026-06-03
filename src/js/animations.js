// animations.js
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { SplitText } from 'gsap/SplitText';

gsap.registerPlugin(ScrollTrigger, SplitText);

// ─────────────────────────────────────────────────────────────
// 1. SET INITIAL STATES
//    Call once on DOMContentLoaded — before any animation runs.
//    Ensures no flash of un-animated content.
// ─────────────────────────────────────────────────────────────

export function setInitialStates() {

  // Fade up
  gsap.set('.fade-up', { opacity: 0, y: 40 });

  // Fade in
  gsap.set('.fade-in', { opacity: 0 });

  // Staggered children — fade up
  document.querySelectorAll('.fade-up-group').forEach((section) => {
    const items = section.querySelectorAll('.fade-up-item');
    if (items.length) gsap.set(items, { opacity: 0, y: 40 });
  });

  // Staggered children — fade in
  document.querySelectorAll('.fade-in-group').forEach((section) => {
    const items = section.querySelectorAll('.fade-in-item');
    if (items.length) gsap.set(items, { opacity: 0 });
  });

  // Mask up — clip words from below
  // document.querySelectorAll('.mask-up').forEach((el) => {
  //   const split = new SplitText(el, { type: 'lines,words', linesClass: 'mask-line',autoSplit:true,  });
  //   el._split = split;
  //   gsap.set(split.words, { yPercent: 110 });
  // });
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
  // Char opacity — chars start at 0.2
  document.querySelectorAll('.fade-text').forEach((el) => {
    const split = new SplitText(el, { type: 'chars', autoSplit: true, });
    el._split = split;
    gsap.set(split.chars, { opacity: 0.2 }); // ← was split.words
  });

  // Scale Down
  gsap.set('.scale-down', { transformOrigin: 'top center' });
}

// ─────────────────────────────────────────────────────────────
// 2. SCROLL ANIMATIONS
//    Call after setInitialStates().
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
  // document.querySelectorAll('.mask-up').forEach((el) => {
  //   if (!el._split) return;

  //   ScrollTrigger.create({
  //     trigger: el,
  //     start: 'top 99%',
  //     once: true,
  //     onEnter: () => gsap.to(el._split.words, {
  //       yPercent: 0,
  //       duration: 0.8,
  //       ease: 'power3.out',
  //       stagger: 0.04,
  //     }),
  //   });
  // });

  // ── Char opacity ───────────────────────────────────────
  document.querySelectorAll('.fade-text').forEach((el) => {
    if (!el._split) return;

    ScrollTrigger.create({
      trigger: el,
      start: 'top 99%',
      once: true,
      onEnter: () => gsap.to(el._split.chars, {
        opacity: 1,
        duration: 0,
        ease: 'none',
        stagger: 0.02,
      }),
    });
  });
  // ── Scale away (mobile only) ───────────────────────────
  document.querySelectorAll('.scale-down').forEach((el) => {
    const target = el.firstElementChild;

    ScrollTrigger.matchMedia({
      '(min-width: 768px)': () => {
        gsap.to(target, {
          scale: 0.85,
          ease: 'none',
          y: '60%',
          scrollTrigger: {
            trigger: el,
            start: 'top top',
            end: 'bottom+=50% top',
            scrub: true,
          },
        });
      },
    });
  });
}