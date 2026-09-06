import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import { initHeaderTheme } from './index.js';

export function initShriCatalogScroll() {
  const sections = document.querySelectorAll('.shri-catalog-section');

  if (!sections.length) return;

  const header = document.querySelector('.site-header');

  sections.forEach((section) => {
    const stickyOuter = section.querySelector('.shri-catalog-sticky-outer');
    const stickyInner = section.querySelector('.shri-catalog-sticky-inner');
    const track = section.querySelector('.shri-catalog-track');

    if (!stickyOuter || !stickyInner || !track) return;

    const hasTheme = section.hasAttribute('data-header-theme');
    const theme = section.getAttribute('data-header-theme');

    ScrollTrigger.matchMedia({
      '(min-width: 768px)': () => {
        const st = ScrollTrigger.create({
          trigger: section,
          start: 'bottom bottom',
          end: () =>
            `+=${track.scrollWidth - stickyInner.clientWidth}`,
          pin: true,
          scrub: true,
          invalidateOnRefresh: true,
          refreshPriority: 1,
          onUpdate: (self) => {
            gsap.set(track, {
              x:
                -(track.scrollWidth - stickyInner.clientWidth) *
                self.progress,
            });
          },
          onRefresh: () => initHeaderTheme(),
        });
        // Cleanup when leaving desktop breakpoint
        return () => {
          st.kill();
          gsap.set([stickyOuter, stickyInner, track], {
            clearProps: 'all',
          });
          initHeaderTheme();
        };
      },

      '(max-width: 767px)': () => {
        gsap.set(track, {
          clearProps: 'x',
        });
      },
    });
  });

}