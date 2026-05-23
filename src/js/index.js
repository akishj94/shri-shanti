import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Lenis from 'lenis';
import { initShriCatalogScroll } from './shri-catalog';
gsap.registerPlugin(ScrollTrigger);

// ─── Smooth scroll (Lenis) ─────────────────────────────────

// const lenis = new Lenis({
//   duration: 1.2,
//   easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
// });

// gsap.ticker.add((time) => lenis.raf(time * 1000));
// gsap.ticker.lagSmoothing(0);

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

// function stickyPanels() {
//   const section = document.querySelector('.trusted_leader');
//   if (!section) return;

//   const panels    = Array.from(section.querySelectorAll('.accordion_panel'));
//   const separator = section.querySelector('.bordered_separator span');

//   if (!panels.length) return;

//   // Targets only the text/name wrapper — the sibling <figure> (logo) is unaffected
//   const getContent = (panel) => panel.querySelector('.wp-block-group__inner-container > .wp-block-group');

//   // ── Initial states ──────────────────────────────────────

//   gsap.set(separator, { width: '0%' });

//   panels.forEach((panel, i) => {
//     const content = getContent(panel);
//     if (!content) return;
//     gsap.set(content, {
//       opacity:  i === 0 ? 1 : 0,
//       y:        i === 0 ? 0 : 14,
//       height:   i === 0 ? 'auto' : 0,
//       overflow: 'hidden',
//     });
//   });

//   // ── Scroll budget ───────────────────────────────────────
//   const totalScroll =
//     window.innerHeight * 0.5 * (panels.length - 1) + window.innerHeight * 0.3;

//   // ── Master timeline ─────────────────────────────────────
//   const tl = gsap.timeline({
//     scrollTrigger: {
//       trigger:      section,
//       start:        'top top',
//       end:          `+=${totalScroll * 1.5}`,
//       pin:          true,
//       pinSpacing:   true,
//       scrub:        1,
//     },
//   });

//   // Separator grows across the full scroll
//   tl.to(separator, { width: '100%', ease: 'none', duration: 1 }, 0);

//   // Panel content transitions
//   const segDuration = 1 / (panels.length - 1 || 1);

//   for (let i = 0; i < panels.length - 1; i++) {
//     const segStart       = i * segDuration;
//     const currentContent = getContent(panels[i]);
//     const nextContent    = getContent(panels[i + 1]);

//     if (!currentContent || !nextContent) continue;

//     // Measure next height upfront
//     const nextHeight = nextContent.scrollHeight;

//     // Ensure next starts from 0 height
//     gsap.set(nextContent, { height: 0 });

//     // Run BOTH animations at the SAME TIME
//     tl.to(currentContent, {
//       opacity: 0,
//       y: -10,
//       height: 0,
//       ease: 'power2.inOut',
//       duration: segDuration
//     }, segStart);

//     tl.to(nextContent, {
//       opacity: 1,
//       y: 0,
//       height: nextHeight,
//       ease: 'power2.inOut',
//       duration: segDuration
//     }, segStart);
//     tl.set(nextContent, {
//       height: 'auto'
//     }, segStart + segDuration);
//   }
// }

function stickyPanels() {
  const section = document.querySelector('.trusted_leader');
  if (!section) return;
 
  const panels    = Array.from(section.querySelectorAll('.accordion_panel'));
  const separator = section.querySelector('.bordered_separator span');
 
  if (!panels.length) return;
 
  const getContent = (panel) => panel.querySelector('.wp-block-group__inner-container > .wp-block-group');
 
  // Grab the section's actual bottom padding so we can preserve it
  // as a min-height on the accordion column once panels collapse.
  const sectionStyle  = getComputedStyle(section);
  const paddingBottom = parseFloat(sectionStyle.paddingBlockEnd || sectionStyle.paddingBottom) || 0;
  const accordionCol  = section.querySelector('.accordion_col');
 
  // Initial states
 
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
 
  // Scroll budget
  const totalScroll =
    window.innerHeight * 0.5 * (panels.length - 1) + window.innerHeight * 0.3;
 
  // Master timeline
  const tl = gsap.timeline({
    scrollTrigger: {
      trigger:    section,
      start:      'top top',
      end:        `+=${totalScroll * 1.5}`,
      pin:        true,
      pinSpacing: true,
      scrub:      1,
      onUpdate: (self) => {
        // Once all transitions are done (progress ~1), restore the
        // section's bottom padding by ensuring the column has it.
        // This prevents the pinned height from swallowing the padding.
        if (accordionCol) {
          accordionCol.style.paddingBlockEnd = `${paddingBottom * self.progress}px`;
        }
      },
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
 
    const nextHeight = nextContent.scrollHeight;
 
    gsap.set(nextContent, { height: 0 });
 
    tl.to(currentContent, {
      opacity:  0,
      y:        -10,
      height:   0,
      ease:     'power2.inOut',
      duration: segDuration,
    }, segStart);
 
    tl.to(nextContent, {
      opacity:  1,
      y:        0,
      height:   nextHeight,
      ease:     'power2.inOut',
      duration: segDuration,
    }, segStart);
 
    tl.set(nextContent, { height: 'auto' }, segStart + segDuration);
  }
}
function initPatternBg() {
  const section = document.querySelector(".learn_solutions");
  if (!section) return;

  let pattern = section.querySelector(".learn-solutions-pattern");

  if (!pattern) {
    pattern = document.createElement("div");
    pattern.className = "learn-solutions-pattern";
    section.appendChild(pattern);
  }

  let posX = 0;
  let posY = 0;

  // Default direction = ↘
  let targetDir = -1;
  let currentDir = -1;

  const speed = 0.35;

  // Smooth direction interpolation
  function updatePattern() {
    currentDir += (targetDir - currentDir) * 0.08;

    posX += speed * currentDir;
    posY += speed * currentDir;

    gsap.set(pattern, {
      backgroundPosition: `${posX}px ${posY}px`,
    });
  }

  gsap.ticker.add(updatePattern);

  let lastScroll =
    window.pageYOffset || document.documentElement.scrollTop;

  function handleScroll() {
    const currentScroll =
      window.pageYOffset || document.documentElement.scrollTop;

    // Reverse direction based on scroll
    targetDir = currentScroll > lastScroll ? 1 : -1;

    lastScroll = Math.max(currentScroll, 0);
  }

  // Works with native scroll + Lenis
  window.addEventListener("scroll", handleScroll, {
    passive: true,
  });

  // Cleanup
  return () => {
    gsap.ticker.remove(updatePattern);

    window.removeEventListener("scroll", handleScroll);
  };
}

function initNav() {
    if (typeof gsap === 'undefined') { console.warn('initNav: GSAP not found.'); return; }

    const header   = document.querySelector('.site-header');
    const toggler  = document.getElementById('nav-toggler');
    const navList  = document.querySelector('.nav-list');
    const mobileBg = document.querySelector('.navbar_background-mobile');
    const logo     = document.querySelector('.site_branding img');
    const siteCta  = document.querySelector('.site_cta');
    const BREAK    = 768;

    if (!header || !toggler || !navList) return;

    let mobileOpen = false;
    let wasMobile  = window.innerWidth < BREAK;

    // ── Helpers ───────────────────────────────────────────────────────────────

    function isMobile() { return window.innerWidth < BREAK; }

    function getStaggerItems() {
        return [...navList.querySelectorAll(':scope > .nav-item'), siteCta].filter(Boolean);
    }

    function resetMobileState() {
        mobileOpen = false;
        toggler.setAttribute('aria-expanded', 'false');
        header.setAttribute('data-expanded', 'false');

        gsap.set([...toggler.children], { clearProps: 'all' });
        gsap.set(header,   { clearProps: 'all' });
        gsap.set(logo,     { clearProps: 'all' });
        gsap.set(mobileBg, { clearProps: 'all', opacity: 0 });

        navList.style.visibility    = 'hidden';
        navList.style.pointerEvents = 'none';
        gsap.set(getStaggerItems(), { clearProps: 'all', opacity: 0 });

        navList.querySelectorAll('.nav-item.is-open').forEach(closeMobileSubmenu);
    }

    function resetDesktopState() {
        navList.style.visibility    = '';
        navList.style.pointerEvents = '';
        gsap.set(navList, { clearProps: 'all' });
        gsap.set(getStaggerItems(), { clearProps: 'all' });
        navList.querySelectorAll('.nav-dropdown--default .nav-dropdown-item').forEach(item => {
            gsap.set(item, { opacity: 0, y: 6 });
        });
    }

    // ── Mobile ────────────────────────────────────────────────────────────────

    function openMobileMenu() {
        mobileOpen = true;
        toggler.setAttribute('aria-expanded', 'true');
        header.setAttribute('data-expanded', 'true');

        const items = getStaggerItems();
        gsap.set(items, { opacity: 0, y: 16 });

        navList.style.visibility    = 'visible';
        navList.style.pointerEvents = 'auto';

        gsap.to(toggler.children[0], { y: 4,  rotation:  45, duration: 0.25, ease: 'power2.inOut' });
        gsap.to(toggler.children[1], { y: -3, rotation: -45, duration: 0.25, ease: 'power2.inOut' });

        gsap.to(header, { height: '100lvh', duration: 0.45, ease: 'expo.inOut' });
        gsap.to(logo,   { filter: 'brightness(0) invert(1)', duration: 0.3 });

        gsap.fromTo(mobileBg,
            { opacity: 0, scaleY: 0.92, transformOrigin: 'top center' },
            {
                opacity: 1, scaleY: 1,
                duration: 0.5, ease: 'expo.out',
                onComplete: () => {
                    gsap.to(items, {
                        opacity: 1, y: 0,
                        duration: 0.4, ease: 'power3.out',
                        stagger: 0.07
                    });
                }
            }
        );
    }

    function closeMobileMenu() {
        mobileOpen = false;
        toggler.setAttribute('aria-expanded', 'false');
        header.setAttribute('data-expanded', 'false');

        navList.querySelectorAll('.nav-item.is-open').forEach(closeMobileSubmenu);

        gsap.to(toggler.children[0], { y: 0, rotation: 0, duration: 0.22, ease: 'power2.inOut' });
        gsap.to(toggler.children[1], { y: 0, rotation: 0, duration: 0.22, ease: 'power2.inOut' });

        const tl = gsap.timeline();

        tl.to(getStaggerItems(), {
            opacity: 0, y: 10,
            duration: 0.2, ease: 'power2.in', stagger: 0.04,
        })
        .to(mobileBg, { opacity: 0, duration: 0.3, ease: 'power2.in' }, '<')
        .to(logo, { filter: 'brightness(1) invert(0)', duration: 0.4, ease: 'power1.inOut' }, '<0.1')
        .to(header, {
            height: '', duration: 0.4, ease: 'expo.inOut',
            onComplete: () => {
                navList.style.visibility    = 'hidden';
                navList.style.pointerEvents = 'none';
            }
        });
    }

    function openMobileSubmenu(parentItem) {
        const dropdown = parentItem.querySelector('.nav-dropdown--default');
        if (!dropdown) return;
        parentItem.classList.add('is-open');
        parentItem.querySelector('.nav-link')?.setAttribute('aria-expanded', 'true');
        const subItems = dropdown.querySelectorAll('.nav-dropdown-item');
        gsap.set(subItems, { opacity: 0, y: 8 });
        gsap.to(subItems, { opacity: 1, y: 0, duration: 0.28, ease: 'power2.out', stagger: 0.06 });
    }

    function closeMobileSubmenu(parentItem) {
        const dropdown = parentItem.querySelector('.nav-dropdown--default');
        if (!dropdown) return;
        parentItem.classList.remove('is-open');
        parentItem.querySelector('.nav-link')?.setAttribute('aria-expanded', 'false');
        gsap.to(dropdown.querySelectorAll('.nav-dropdown-item'), {
            opacity: 0, y: 6, duration: 0.16, ease: 'power2.in'
        });
    }

    toggler.addEventListener('click', () => mobileOpen ? closeMobileMenu() : openMobileMenu());

    navList.querySelectorAll('.nav-item.has-dropdown').forEach(parentItem => {
        parentItem.addEventListener('click', e => {
            if (!isMobile()) return;
            if (e.target.closest('.nav-dropdown--default')) return;
            e.preventDefault();
            parentItem.classList.contains('is-open')
                ? closeMobileSubmenu(parentItem)
                : openMobileSubmenu(parentItem);
        });
    });

    // ── Desktop ───────────────────────────────────────────────────────────────

    navList.querySelectorAll('.nav-item.has-dropdown').forEach(parentItem => {
        const dropdown = parentItem.querySelector('.nav-dropdown--default');
        if (!dropdown) return;

        let leaveTimer = null;
        const cancelLeave = () => { clearTimeout(leaveTimer); leaveTimer = null; };

        parentItem.addEventListener('mouseenter', () => {
            if (isMobile()) return;
            cancelLeave();
            const items = dropdown.querySelectorAll('.nav-dropdown-item');
            gsap.killTweensOf(items);
            gsap.fromTo(items,
                { opacity: 0, y: 8 },
                { opacity: 1, y: 0, duration: 0.25, ease: 'power2.out', stagger: 0.055 }
            );
        });

        parentItem.addEventListener('mouseleave', () => {
            if (isMobile()) return;
            leaveTimer = setTimeout(() => {
                const items = dropdown.querySelectorAll('.nav-dropdown-item');
                gsap.to(items, {
                    opacity: 0, y: 6, duration: 0.16, ease: 'power2.in',
                    onComplete: () => gsap.set(items, { clearProps: 'all' })
                });
            }, 120);
        });
    });

    // ── Resize ────────────────────────────────────────────────────────────────

    let resizeTimer = null;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            const nowMobile = isMobile();
            if (wasMobile === nowMobile) return;
            wasMobile = nowMobile;
            nowMobile ? resetMobileState() : resetDesktopState();
        }, 80);
    });

    // ── Init ──────────────────────────────────────────────────────────────────

    isMobile() ? resetMobileState() : resetDesktopState();
}



// ─── Init ─────────────────────────────────────────────────

function init() {
  pageEnter();
  stickyPanels();

  initNav();
//   initShriCatalogScroll();

  initPatternBg();
  ScrollTrigger.refresh();
}

document.addEventListener('DOMContentLoaded', init);