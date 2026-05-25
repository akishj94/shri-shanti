// shri-catalog.js
// Call catalogScroll(lenis) inside your init() and pass your lenis instance.
// lenis param is optional — falls back to native scroll if omitted.
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

export function initShriCatalogScroll() {
  const sections = document.querySelectorAll(".shri-catalog-section");
  if (!sections.length) return;

  const isDesktop = () => window.innerWidth > 768;

  sections.forEach((section) => {
    const stickyOuter = section.querySelector(".shri-catalog-sticky-outer");
    const stickyInner = section.querySelector(".shri-catalog-sticky-inner");
    const track       = section.querySelector(".shri-catalog-track");

    if (!stickyOuter || !stickyInner || !track) return;

    const trackScroll = track.scrollWidth - stickyInner.clientWidth;
    if (trackScroll <= 0) return;

    const tween = gsap.to(track, {
      x:    -trackScroll,
      ease: "none",
    });

    ScrollTrigger.create({
      animation: tween,
      trigger:   section,
      start:     "bottom bottom",
      end:       () => `+=${trackScroll}`,
      pin:       true,
      scrub:     true,
      invalidateOnRefresh: true,
      markers:   true,
      onRefresh: (self) => {
        if (!isDesktop()) {
          gsap.set(track, { clearProps: "x" });
          self.disable();
        } else {
          self.enable();
        }
      },
    });
  });
}