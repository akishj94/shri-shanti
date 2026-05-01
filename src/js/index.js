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
  document.querySelectorAll('video').forEach((video) => {
    video.removeAttribute('controls');
  });
}

// ─── Trusted Leader ───────────────────────────────────────

function stickyPanels() {
  const section = document.querySelector('.trusted_leader');
  if (!section) return;

  const panels    = Array.from(section.querySelectorAll('.accordion_panel'));
  const separator = section.querySelector('.bordered_separator span');

  if (!panels.length) return;

  // Targets only the text/name wrapper — the sibling <figure> (logo) is unaffected
  const getContent = (panel) => panel.querySelector('.wp-block-group__inner-container > .wp-block-group');

  // ── Initial states ──────────────────────────────────────

  gsap.set(separator, { width: '0%' });

  panels.forEach((panel, i) => {
    const content = getContent(panel);
    if (!content) return;
    gsap.set(content, {
      opacity:  i === 0 ? 1 : 0,
      y:        i === 0 ? 0 : 14,
      height:   i === 0 ? 'auto' : 0,
      overflow: 'hidden',
    });
  });

  // ── Scroll budget ───────────────────────────────────────
  const totalScroll =
    window.innerHeight * 0.5 * (panels.length - 1) + window.innerHeight * 0.3;

  // ── Master timeline ─────────────────────────────────────
  const tl = gsap.timeline({
    scrollTrigger: {
      trigger:      section,
      start:        'top top',
      end:          `+=${totalScroll * 1.5}`,
      pin:          true,
      pinSpacing:   true,
      scrub:        1,
    },
  });

  // Separator grows across the full scroll
  tl.to(separator, { width: '100%', ease: 'none', duration: 1 }, 0);

  // Panel content transitions
  const segDuration = 1 / (panels.length - 1 || 1);

  for (let i = 0; i < panels.length - 1; i++) {
  const segStart       = i * segDuration;
  const currentContent = getContent(panels[i]);
  const nextContent    = getContent(panels[i + 1]);

  if (!currentContent || !nextContent) continue;

  // Measure next height upfront
  const nextHeight = nextContent.scrollHeight;

  // Ensure next starts from 0 height
  gsap.set(nextContent, { height: 0 });

  // Run BOTH animations at the SAME TIME
  tl.to(currentContent, {
    opacity: 0,
    y: -10,
    height: 0,
    ease: 'power2.inOut',
    duration: segDuration
  }, segStart);

  tl.to(nextContent, {
    opacity: 1,
    y: 0,
    height: nextHeight,
    ease: 'power2.inOut',
    duration: segDuration
  }, segStart);
}
}

// ─── Init ─────────────────────────────────────────────────

function init() {
  pageEnter();
  stickyPanels();
  ScrollTrigger.refresh();
}

document.addEventListener('DOMContentLoaded', init);