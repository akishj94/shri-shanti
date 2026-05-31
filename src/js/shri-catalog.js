// shri-catalog.js
// Call catalogScroll(lenis) inside your init() and pass your lenis instance.
// lenis param is optional — falls back to native scroll if omitted.
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

export function initShriCatalogScroll() {
  const sections = document.querySelectorAll(".shri-catalog-section");
  if (!sections.length) return;

  const isDesktop = () => window.innerWidth > 768;
  const header    = document.querySelector('.site-header');

  sections.forEach((section) => {
    const stickyOuter = section.querySelector(".shri-catalog-sticky-outer");
    const stickyInner = section.querySelector(".shri-catalog-sticky-inner");
    const track       = section.querySelector(".shri-catalog-track");

    if (!stickyOuter || !stickyInner || !track) return;

    const hasTheme = section.hasAttribute('data-header-theme');
    const theme    = section.getAttribute('data-header-theme');

    // ── Horizontal scroll ─────────────────────────────────
    ScrollTrigger.create({
      trigger:   section,
      start:     "bottom bottom",
      end:       () => isDesktop() ? `+=${track.scrollWidth - stickyInner.clientWidth}` : "bottom bottom",
      pin:       isDesktop(),
      scrub:     true,
      invalidateOnRefresh: true,
      onUpdate: (self) => {
        if (!isDesktop()) {
          gsap.set(track, { clearProps: "x" });
          return;
        }
        const trackScroll = track.scrollWidth - stickyInner.clientWidth;
        gsap.set(track, { x: -trackScroll * self.progress });
      },
      onRefresh: () => {
        if (!isDesktop()) gsap.set(track, { clearProps: "x" });
      },
    });

    if (header && hasTheme) {
      const headerHeight = header.offsetHeight + 2 + 'px';

      ScrollTrigger.create({
        trigger:          section,
        start:            `top ${headerHeight}`,
        end:              () => `+=${track.scrollWidth - stickyInner.clientWidth + section.offsetHeight + window.innerHeight}`,
        invalidateOnRefresh: true,
        onEnter:          () => header.setAttribute('data-theme', theme),
        onEnterBack:      () => header.setAttribute('data-theme', theme),
        onLeave:          () => header.removeAttribute('data-theme'),
        onLeaveBack:      () => header.removeAttribute('data-theme'),
      });
    }
  });
}