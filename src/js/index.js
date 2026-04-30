import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Lenis from 'lenis';
gsap.registerPlugin(ScrollTrigger);

// ─── Smooth scroll (Lenis) ─────────────────────────────────

const lenis = new Lenis({
  duration: 1.2,
  easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
});

// Sync Lenis with GSAP ticker
gsap.ticker.add((time) => lenis.raf(time * 1000));
gsap.ticker.lagSmoothing(0);

// ─── Page enter animation ──────────────────────────────────

function pageEnter() {
  gsap.from('[data-animate]', {
    opacity: 0,
    y: 24,
    duration: 0.7,
    ease: 'power3.out',
    stagger: 0.08,
    clearProps: 'all',
  });
  document.querySelectorAll("video").forEach(video => {
    video.removeAttribute("controls");
  });
}

// ─── Init ─────────────────────────────────────────────────

function init() {
  pageEnter();
}

// Run on first load
document.addEventListener('DOMContentLoaded', init);