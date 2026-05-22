// shri-catalog.js
// Call catalogScroll(lenis) inside your init() and pass your lenis instance.
// lenis param is optional — falls back to native scroll if omitted.
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
export function initShriCatalogScroll() {
  const sections = document.querySelectorAll(".shri-catalog-section");
  if (!sections.length) return;

  const isDesktop = () => window.innerWidth > 768;

  const instances = [];

  sections.forEach((section) => {
    const stickyOuter = section.querySelector(".shri-catalog-sticky-outer");
    const stickyInner = section.querySelector(".shri-catalog-sticky-inner");
    const track = section.querySelector(".shri-catalog-track");

    if (!stickyOuter || !stickyInner || !track) return;

    let tween;
    let trigger;

    const setup = () => {
      // Reset mobile
      if (!isDesktop()) {
        gsap.set(track, { clearProps: "all" });
        gsap.set(stickyInner, { clearProps: "all" });

        if (trigger) {
          trigger.kill();
          trigger = null;
        }

        if (tween) {
          tween.kill();
          tween = null;
        }

        return;
      }

      // Wait for layout
      ScrollTrigger.refresh();

      const trackScroll =
        track.scrollWidth - stickyInner.clientWidth;

      // No overflow = no animation
      if (trackScroll <= 0) return;

      tween = gsap.to(track, {
        x: -trackScroll,
        ease: "none",
      });

      trigger = ScrollTrigger.create({
        animation: tween,

        trigger: section,

        start: "bottom bottom",
        end: () => `+=${trackScroll}`,

        pin: sections,
        scrub: true,
        pinSpacing: true,

        invalidateOnRefresh: true,


        onRefresh: () => {
          gsap.set(track, { x: 0 });

          const updatedScroll =
            track.scrollWidth - stickyInner.clientWidth;

          tween.vars.x = -updatedScroll;
          tween.invalidate();
        },
      });

      // Lenis support
      if (window.lenis) {
        window.lenis.resize?.();
      }
    };

    setup();

    instances.push({
      section,
      setup,
      destroy() {
        tween?.kill();
        trigger?.kill();
      },
    });
  });

  //
  // Resize handling
  //

  let resizeTimer;

  const handleResize = () => {
    clearTimeout(resizeTimer);

    resizeTimer = setTimeout(() => {
      instances.forEach((instance) => {
        instance.destroy();
        instance.setup();
      });

      ScrollTrigger.refresh();

      if (window.lenis) {
        window.lenis.resize?.();
      }
    }, 150);
  };

  window.addEventListener("resize", handleResize);

  //
  // Cleanup support
  //

  return () => {
    window.removeEventListener("resize", handleResize);

    instances.forEach((instance) => {
      instance.destroy();
    });
  };
}