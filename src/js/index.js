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
    // ── Guards ─────────────────────────────────────────────────────────────
    if (typeof gsap === 'undefined') {
        console.warn('initNav: GSAP not found.');
        return;
    }

    const header      = document.querySelector('.site-header');
    const toggler     = document.getElementById('nav-toggler');
    const navList     = document.querySelector('.nav-list');
    const mobileBg    = document.querySelector('.navbar_background-mobile');
    const BREAKPOINT  = 768;

    if (!header || !toggler || !navList) return;

    // ── Measure & stamp header bar height as CSS custom property ──────────
    function setNavBarHeight() {
        const h = header.getBoundingClientRect().height;
        // Approximate bar height: header padding + logo row, not any open submenu
        document.documentElement.style.setProperty('--nav-bar-height', `${h}px`);
    }

    // ── State ──────────────────────────────────────────────────────────────
    let mobileOpen    = false;
    let mobileCtx     = null; // GSAP context for mobile animations
    const openDropdowns = new Set(); // tracks desktop open parents

    // ── MOBILE ─────────────────────────────────────────────────────────────
    function openMobileMenu() {
        if (mobileOpen) return;
        mobileOpen = true;

        toggler.setAttribute('aria-expanded', 'true');
        toggler.classList.add('is-active');

        navList.style.visibility = 'visible';
        navList.style.pointerEvents = 'auto';

        const items = navList.querySelectorAll(':scope > .nav-item');

        mobileCtx = gsap.context(() => {
            // Hamburger → X
            gsap.to(toggler.children[0], { y: 7,  rotation: 45,  duration: 0.25, ease: 'power2.inOut' });
            gsap.to(toggler.children[1], { scaleX: 0, opacity: 0,  duration: 0.2,  ease: 'power2.inOut' });
            gsap.to(toggler.children[2], { y: -7, rotation: -45, duration: 0.25, ease: 'power2.inOut' });

            // Stagger nav items in
            gsap.fromTo(items,
                { opacity: 0, y: 14 },
                {
                    opacity: 1,
                    y: 0,
                    duration: 0.38,
                    ease: 'power3.out',
                    stagger: 0.07,
                    delay: 0.05,
                }
            );
        });
    }

    function closeMobileMenu() {
        if (!mobileOpen) return;
        mobileOpen = false;

        toggler.setAttribute('aria-expanded', 'false');
        toggler.classList.remove('is-active');

        const items = navList.querySelectorAll(':scope > .nav-item');

        // Also collapse any open mobile submenus
        navList.querySelectorAll('.nav-dropdown--default.is-open')
            .forEach(d => closeMobileSubmenu(d.closest('.nav-item')));

        gsap.to(toggler.children[0], { y: 0, rotation: 0, duration: 0.22, ease: 'power2.inOut' });
        gsap.to(toggler.children[1], { scaleX: 1, opacity: 1, duration: 0.18, ease: 'power2.inOut' });
        gsap.to(toggler.children[2], { y: 0, rotation: 0, duration: 0.22, ease: 'power2.inOut' });

        gsap.to(items, {
            opacity: 0,
            y: 10,
            duration: 0.2,
            ease: 'power2.in',
            stagger: 0.04,
            onComplete: () => {
                navList.style.visibility = 'hidden';
                navList.style.pointerEvents = 'none';
                if (mobileCtx) { mobileCtx.revert(); mobileCtx = null; }
            }
        });
    }

    // Mobile submenu accordion
    function openMobileSubmenu(parentItem) {
        const dropdown = parentItem.querySelector('.nav-dropdown--default');
        if (!dropdown) return;

        dropdown.classList.add('is-open');
        parentItem.classList.add('is-open');

        const subItems = dropdown.querySelectorAll('.nav-dropdown-item');
        gsap.fromTo(subItems,
            { opacity: 0, y: 8 },
            { opacity: 1, y: 0, duration: 0.28, ease: 'power2.out', stagger: 0.06, delay: 0.05 }
        );
    }

    function closeMobileSubmenu(parentItem) {
        const dropdown = parentItem.querySelector('.nav-dropdown--default');
        if (!dropdown) return;

        dropdown.classList.remove('is-open');
        parentItem.classList.remove('is-open');

        const subItems = dropdown.querySelectorAll('.nav-dropdown-item');
        gsap.to(subItems, { opacity: 0, y: 6, duration: 0.16, ease: 'power2.in' });
    }

    // Toggler click
    toggler.addEventListener('click', () => {
        mobileOpen ? closeMobileMenu() : openMobileMenu();
    });

    // Mobile parent item click → accordion
    navList.querySelectorAll('.nav-item.has-dropdown > .nav-link').forEach(link => {
        link.addEventListener('click', e => {
            if (window.innerWidth >= BREAKPOINT) return;
            e.preventDefault();

            const parentItem = link.closest('.nav-item');
            parentItem.classList.contains('is-open')
                ? closeMobileSubmenu(parentItem)
                : openMobileSubmenu(parentItem);
        });
    });

    // ── DESKTOP ─────────────────────────────────────────────────────────────
    // Position and show the hoisted dropdown panel
    function positionHoisted(parentItem, panel) {
        const rect = parentItem.getBoundingClientRect();
        panel.style.insetBlockStart = `${rect.bottom + 10}px`;
        panel.style.insetInlineStart = `${rect.left}px`;
    }

    function openDesktopDropdown(parentItem) {
        const id    = parentItem.querySelector('.nav-link')?.dataset.dropdownTarget;
        const panel = id ? document.getElementById(id) : null; // hoisted panel

        parentItem.classList.add('is-open');
        openDropdowns.add(parentItem);

        if (panel) {
            positionHoisted(parentItem, panel);
            panel.hidden = false;

            const items = panel.querySelectorAll('.nav-dropdown-item');
            gsap.killTweensOf([panel, items]);

            gsap.fromTo(panel,
                { opacity: 0, y: 8 },
                { opacity: 1, y: 0, duration: 0.22, ease: 'power2.out',
                  onStart: () => { panel.style.pointerEvents = 'auto'; }
                }
            );
            gsap.fromTo(items,
                { opacity: 0, y: 6 },
                { opacity: 1, y: 0, duration: 0.25, ease: 'power2.out', stagger: 0.055, delay: 0.04 }
            );
        }
    }

    function closeDesktopDropdown(parentItem) {
        const id    = parentItem.querySelector('.nav-link')?.dataset.dropdownTarget;
        const panel = id ? document.getElementById(id) : null;

        parentItem.classList.remove('is-open');
        openDropdowns.delete(parentItem);

        if (panel) {
            gsap.killTweensOf(panel);
            gsap.to(panel, {
                opacity: 0,
                y: 8,
                duration: 0.18,
                ease: 'power2.in',
                onComplete: () => {
                    panel.hidden = true;
                    panel.style.pointerEvents = 'none';
                    // Reset item opacities for next open
                    gsap.set(panel.querySelectorAll('.nav-dropdown-item'), { opacity: 0, y: 6 });
                }
            });
        }
    }

    navList.querySelectorAll('.nav-item.has-dropdown').forEach(parentItem => {
        // Use a small delay on leave so pointer can travel to the panel
        let leaveTimer = null;

        const cancelLeave = () => {
            if (leaveTimer) { clearTimeout(leaveTimer); leaveTimer = null; }
        };

        const scheduleClose = () => {
            cancelLeave();
            leaveTimer = setTimeout(() => {
                if (window.innerWidth < BREAKPOINT) return;
                closeDesktopDropdown(parentItem);
            }, 120);
        };

        parentItem.addEventListener('mouseenter', () => {
            if (window.innerWidth < BREAKPOINT) return;
            cancelLeave();
            openDesktopDropdown(parentItem);
        });

        parentItem.addEventListener('mouseleave', scheduleClose);

        // Keep open while pointer is inside the hoisted panel
        const id    = parentItem.querySelector('.nav-link')?.dataset.dropdownTarget;
        const panel = id ? document.getElementById(id) : null;

        if (panel) {
            panel.addEventListener('mouseenter', cancelLeave);
            panel.addEventListener('mouseleave', scheduleClose);
        }
    });

    // ── RESIZE — reset mobile state when crossing breakpoint ────────────────
    let resizeTimer = null;
    window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
            setNavBarHeight();

            if (window.innerWidth >= BREAKPOINT) {
                // Entering desktop — hard-reset any open mobile state
                if (mobileOpen) {
                    mobileOpen = false;
                    toggler.setAttribute('aria-expanded', 'false');
                    toggler.classList.remove('is-active');

                    // Kill any running tweens and reset positions immediately
                    gsap.killTweensOf([...toggler.children]);
                    gsap.set([...toggler.children], { clearProps: 'all' });

                    navList.style.visibility = '';
                    navList.style.pointerEvents = '';

                    const items = navList.querySelectorAll(':scope > .nav-item');
                    gsap.set(items, { clearProps: 'all' });

                    navList.querySelectorAll('.nav-dropdown--default')
                        .forEach(d => d.classList.remove('is-open'));
                    navList.querySelectorAll('.nav-item')
                        .forEach(i => i.classList.remove('is-open'));

                    if (mobileCtx) { mobileCtx.revert(); mobileCtx = null; }
                }
            } else {
                // Entering mobile — close any open desktop panels
                openDropdowns.forEach(p => closeDesktopDropdown(p));
            }
        }, 80);
    });

    // ── Init ────────────────────────────────────────────────────────────────
    setNavBarHeight();
    // Measure again after fonts/images load
    window.addEventListener('load', setNavBarHeight);
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