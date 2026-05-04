// shri-catalog.js
// Call catalogScroll(lenis) inside your init() and pass your lenis instance.
// lenis param is optional — falls back to native scroll if omitted.
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
export function catalogScroll(lenis = null) {
  const MQ = window.matchMedia('(min-width: 1024px)');

  // Wire Lenis → ScrollTrigger if an instance is provided
  if (lenis) {
    lenis.on('scroll', ScrollTrigger.update);
    ScrollTrigger.scrollerProxy(document.body, {
      scrollTop(value) {
        if (arguments.length) lenis.scrollTo(value, { immediate: true });
        return lenis.scroll;
      },
      getBoundingClientRect() {
        return { top: 0, left: 0, width: window.innerWidth, height: window.innerHeight };
      },
      pinType: document.body.style.transform ? 'transform' : 'fixed',
    });
  }

  function initSection(section) {
    const outer  = section.querySelector('.shri-catalog-sticky-outer');
    const track  = section.querySelector('.shri-catalog-track');
    const header = section.querySelector('.shri-catalog-header');

    let st = null;

    function build() {
      if (st) { st.kill(); st = null; }

      if (!MQ.matches) {
        gsap.set(track, { clearProps: 'x,transform' });
        outer.style.height = '';
        return;
      }

      const headerH    = header ? header.offsetHeight : 0;
      const available  = window.innerHeight - headerH - 80;
      const travelDist = Math.max(0, track.scrollWidth - available);

      outer.style.height = `${window.innerHeight + travelDist}px`;

      st = gsap.to(track, {
        x: -travelDist,
        ease: 'none',
        scrollTrigger: {
          trigger:             outer,
          scroller:            lenis ? document.body : window,
          start:               'top top',
          end:                 'bottom bottom',
          scrub:               lenis ? 1 : true,
          pin:                 '.shri-catalog-sticky-inner',
          pinSpacing:          false,
          invalidateOnRefresh: true,
        },
      });
    }

    MQ.addEventListener('change', build);

    let resizeTimer;
    window.addEventListener('resize', () => {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(() => { ScrollTrigger.refresh(); build(); }, 150);
    }, { passive: true });

    build();
  }

  document.querySelectorAll('.shri-catalog-section').forEach(initSection);
}