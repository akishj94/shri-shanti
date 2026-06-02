// main.js
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Lenis from 'lenis';
import { initShriCatalogScroll } from './shri-catalog';
import { setInitialStates, initScrollAnimations } from './animations';
gsap.registerPlugin(ScrollTrigger);
// ─── Smooth scroll (Lenis) ─────────────────────────────────

const lenis = new Lenis({
  duration: 1.2,
  easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
  prevent: (node) => {
    return node.closest('.modalContainer');
}
});

lenis.on('scroll', ScrollTrigger.update);

gsap.ticker.add((time) => {
  lenis.raf(time * 1000);
});
gsap.ticker.lagSmoothing(0);

function pageEnter() {
//   gsap.from('[data-animate]', {
//     opacity: 0,
//     y: 24,
//     duration: 0.7,
//     ease: 'power3.out',
//     stagger: 0.08,
//     clearProps: 'all',
//   });
//   document.querySelectorAll('video').forEach((video) => {
//     video.removeAttribute('controls');
//   });
}

function stickyPanels() {
  const section   = document.querySelector('.trusted_leader');
  const separator = section?.querySelector('.bordered_separator span');
  const panels    = Array.from(section?.querySelectorAll('.accordion_panel') ?? []);

  if (!section || !panels.length) return;

  const getContent = (panel) => panel.querySelector('.wp-block-group__inner-container > .wp-block-group');

  const tl = gsap.timeline({ paused: true });

  const segDuration = 1 / (panels.length - 1 || 1);
  const totalScroll = window.innerHeight * 0.5 * (panels.length - 1) + window.innerHeight * 0.3;

  panels.forEach((panel, i) => {
    const content = getContent(panel);
    if (!content) return;

    gsap.set(content, {
      opacity:  i === 0 ? 1 : 0,
      y:        i === 0 ? 0 : 14,
      height:   i === 0 ? content.scrollHeight : 0,
      overflow: 'hidden',
    });

    if (i === 0) return;

    const segStart = (i - 1) * segDuration;

    tl.to(getContent(panels[i - 1]), { opacity: 0, y: -10, height: 0,                    ease: 'power2.inOut', duration: segDuration }, segStart);
    tl.to(content,                   { opacity: 1, y:   0, height: content.scrollHeight,  ease: 'power2.inOut', duration: segDuration }, segStart);
  });

  ScrollTrigger.create({
    trigger: section,
    start:   'top top',
    end:     `+=${totalScroll * 1.3}`,
    pin:     true,
    scrub:   1,
    invalidateOnRefresh: true, // ← already present, good
    markers: false,            // ← remove markers
    onUpdate: self => {
      tl.progress(self.progress);
      gsap.set(separator, { width: `${self.progress * 100}%` });
    },
    onRefresh: self => {
      tl.progress(self.progress);
      gsap.set(separator, { width: `${self.progress * 100}%` });
    },
  });
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

  let targetDir = -1;
  let currentDir = -1;

  const speed = 0.35;

  function updatePattern() {
    currentDir += (targetDir - currentDir) * 0.08;
    posX += speed * currentDir;
    posY += speed * currentDir;
    gsap.set(pattern, { backgroundPosition: `${posX}px ${posY}px` });
  }

  gsap.ticker.add(updatePattern);

  let lastScroll = window.pageYOffset || document.documentElement.scrollTop;

  function handleScroll() {
    const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
    targetDir = currentScroll > lastScroll ? 1 : -1;
    lastScroll = Math.max(currentScroll, 0);
  }

  window.addEventListener("scroll", handleScroll, { passive: true });

  return () => {
    gsap.ticker.remove(updatePattern);
    window.removeEventListener("scroll", handleScroll);
  };
}

const ScrollLock = ( function () {
    let scrollY      = 0;
    let lockCount    = 0; // reference count — safe for nested lock calls
    let lenisInstance = null;

    // ── Register Lenis ───────────────────────────────────────────────────────

    /**
     * Pass your Lenis instance once during init.
     * ScrollLock will stop/start it automatically.
     *
     * @param {object} lenis - Your Lenis instance.
     */
    function registerLenis( lenis ) {
        lenisInstance = lenis;
    }

    // ── Lock ─────────────────────────────────────────────────────────────────

    function lock() {
        lockCount++;

        if ( lockCount > 1 ) return; // already locked

        if ( lenisInstance ) {
            lenisInstance.stop();
        } else {
            scrollY                      = window.scrollY;
            document.body.style.overflow = 'hidden';
            document.body.style.position = 'fixed';
            document.body.style.top      = `-${ scrollY }px`;
            document.body.style.width    = '100%';
        }
    }

    // ── Unlock ───────────────────────────────────────────────────────────────

    function unlock() {
        if ( lockCount <= 0 ) return;

        lockCount--;

        if ( lockCount > 0 ) return; // something else still needs the lock

        if ( lenisInstance ) {
            lenisInstance.start();
        } else {
            document.body.style.overflow = '';
            document.body.style.position = '';
            document.body.style.top      = '';
            document.body.style.width    = '';
            window.scrollTo( 0, scrollY );
        }
    }

    // ── Public API ────────────────────────────────────────────────────────────

    return { registerLenis, lock, unlock };

} )();

(function () {
    
    const modal = document.getElementById('site-modal');
    const overlay = modal.querySelector('.modal__overlay');
    const closeBtn = modal.querySelector('.close_modal');
    const container = modal.querySelector('.modalContainer');
    if (!modal) return;
    const modalViews = {
        contact: document.getElementById('modalContactInfo'),
        form: document.getElementById('modalContactForm'),
    };

    let isOpen = false;
    let lastFocused = null;

    // Accessibility
    modal.setAttribute('role', 'dialog');
    modal.setAttribute('aria-modal', 'true');
    modal.setAttribute('aria-hidden', 'true');
    modal.setAttribute('aria-label', 'Site modal');

    closeBtn.setAttribute('aria-label', 'Close modal');
    closeBtn.setAttribute('type', 'button');

    // Initial state
    Object.values(modalViews).forEach(el => {
        el.style.display = 'none';
    });

    // Focus trap
    function getFocusableElements() {

        return Array.from(
            modal.querySelectorAll(
                'a[href], button:not([disabled]), input:not([disabled]), ' +
                'textarea:not([disabled]), select:not([disabled]), ' +
                '[tabindex]:not([tabindex="-1"])'
            )
        ).filter(el => el.offsetParent !== null);

    }

    function trapFocus(e) {

        if (e.key !== 'Tab') return;

        const focusable = getFocusableElements();

        if (!focusable.length) return;

        const first = focusable[0];
        const last = focusable[focusable.length - 1];

        if (e.shiftKey) {

            if (document.activeElement === first) {
                e.preventDefault();
                last.focus();
            }

        } else {

            if (document.activeElement === last) {
                e.preventDefault();
                first.focus();
            }

        }

    }

    // GSAP timelines
    function buildOpenTL() {

        return gsap.timeline({ paused: true })

            .set(modal, {
                pointerEvents: 'auto'
            })

            .to(modal, {
                opacity: 1,
                duration: 0.01
            })

            .to(overlay, {
                opacity: 1,
                duration: 1,
                ease: 'power1.inOut'
            }, '<')

            .to(container, {
                x: '0%',
                opacity: 1,
                duration: 0.6,
                ease: 'back.out(1)'
            }, '-=0.6');

    }

    function buildCloseTL() {

        return gsap.timeline({ paused: true })

            .to(container, {
                x: '100%',
                opacity: 0,
                duration: 0.35,
                ease: 'power3.in'
            })

            .to(overlay, {
                opacity: 0,
                duration: 0.4,
                ease: 'power1.inOut'
            }, '-=0.2')

            .to(modal, {
                opacity: 0,
                duration: 0.01
            })

            .set(modal, {
                pointerEvents: 'none'
            });

    }

    // View toggle
    function showView(view) {

        Object.values(modalViews).forEach(el => {
            el.style.display = 'none';
        });

        if (view) {
            view.style.display = '';
        }

    }

    // Open modal
    function openModal(view, trigger) {

        if (isOpen) return;

        isOpen = true;

        lastFocused = trigger || document.activeElement;

        showView(view);

        modal.setAttribute('aria-hidden', 'false');

        modal.setAttribute(
            'aria-label',
            view === modalViews.contact
                ? 'Contact information'
                : 'Contact form'
        );

        ScrollLock.lock();

        buildOpenTL().play();

        document.addEventListener('keydown', trapFocus);

        closeBtn.focus();

    }

    // Close modal
    function closeModal() {

        if (!isOpen) return;

        document.removeEventListener('keydown', trapFocus);

        buildCloseTL()
            .play()
            .eventCallback('onComplete', () => {

                isOpen = false;

                showView(null);

                modal.setAttribute('aria-hidden', 'true');

                ScrollLock.unlock();

                if (lastFocused) {
                    lastFocused.focus();
                }

                lastFocused = null;

            });

    }

    // Resolve view
    function resolveView(el) {

        const link = (
            el.dataset.link ||
            el.getAttribute('href') ||
            ''
        ).replace('#', '');

        if (link === 'quote-form') {
            return modalViews.form;
        }

        if (link === 'contact-form') {
            return modalViews.contact;
        }

        return null;

    }

    // Global click handling
    document.addEventListener('click', function (e) {

        // Trigger buttons
        const triggerBtn = e.target.closest('.site-btn, .shri-cta-btn');

        if (triggerBtn && triggerBtn.dataset.link) {

            e.preventDefault();

            const view = resolveView(triggerBtn);

            if (view) {
                openModal(view, triggerBtn);
            }

            return;

        }

        // Footer links
        const footerLink = e.target.closest('.footer-nav-list li a');

        if (footerLink) {

            const href = footerLink.getAttribute('href') || '';

            if (
                href === '#contact-form' ||
                href === '#quote-form'
            ) {

                e.preventDefault();

                const view = resolveView(footerLink);

                if (view) {
                    openModal(view, footerLink);
                }

                return;

            }

        }

        // Close if clicking outside modal
        if (
            isOpen &&
            !e.target.closest('.modalContainer')
        ) {

            closeModal();

        }

    });

    // Prevent modal clicks from bubbling
    container.addEventListener('click', function (e) {
        e.stopPropagation();
    });

    // Close button
    closeBtn.addEventListener('click', closeModal);

    // Escape key
    document.addEventListener('keydown', function (e) {

        if (e.key === 'Escape' && isOpen) {
            closeModal();
        }

    });

})();

function initNav() {
    if (typeof gsap === 'undefined') { console.warn('initNav: GSAP not found.'); return; }

    const header   = document.querySelector('.site-header');
    const toggler  = document.getElementById('nav-toggler');
    const siteNav  = document.getElementById('site-nav');
    const navList  = document.querySelector('.nav-list');
    const mobileBg = document.querySelector('.navbar_background-mobile');
    const logo     = document.querySelector('.site_branding img');
    const siteCta  = document.querySelector('.site-btn');

    if (!header || !toggler || !siteNav || !navList) return;

    const BREAK    = 768;
    const isMobile = () => window.innerWidth < BREAK;

    let mobileOpen  = false;
    let isAnimating = false;
    let wasMobile   = isMobile();

    const getStaggerItems = () =>
        [...navList.querySelectorAll(':scope > .nav-item'), siteCta].filter(Boolean);

    // ── Reset ─────────────────────────────────────────────────────────────────

    function resetCommon() {
        gsap.killTweensOf([header, logo, mobileBg, siteNav, navList, ...toggler.children, ...getStaggerItems()]);
        mobileOpen  = false;
        isAnimating = false;
        toggler.setAttribute('aria-expanded', 'false');
        header.setAttribute('data-expanded',  'false');
    }

    function resetMobileState() {
        resetCommon();
        gsap.set([header, logo, mobileBg, ...toggler.children], { clearProps: 'all' });
        gsap.set(getStaggerItems(), { opacity: 0, y: 12 });
        siteNav.classList.remove('is-visible');
        navList.querySelectorAll('.nav-item.is-open').forEach(closeMobileSubmenu);        
    }

    function resetDesktopState() {
        resetCommon();
        gsap.set([header, logo, mobileBg, siteNav, navList, ...getStaggerItems(), ...toggler.children], { clearProps: 'all' });
        siteNav.classList.remove('is-visible');
        navList.querySelectorAll('.nav-item.is-open').forEach(item => item.classList.remove('is-open'));
        navList.querySelectorAll('.nav-dropdown--default .nav-dropdown-item')
            .forEach(item => gsap.set(item, { opacity: 0, y: 6 }));
        ScrollLock.unlock();
    }

    // ── Mobile menu ───────────────────────────────────────────────────────────

    function openMobileMenu() {
        if (isAnimating) return;
        isAnimating = true;
        mobileOpen  = true;
        toggler.setAttribute('aria-expanded', 'true');
        header.setAttribute('data-expanded',  'true');

        ScrollLock.lock();

        gsap.to(toggler.children[0], { y:  4, rotation:  45, duration: 0.25, ease: 'power2.inOut' });
        gsap.to(toggler.children[1], { y: -3, rotation: -45, duration: 0.25, ease: 'power2.inOut' });

        gsap.timeline()
        .add(() => {
            if (!header.hasAttribute('data-theme')) {
                gsap.to(logo, { filter: 'brightness(0) invert(1)', duration: 0 });
            }
            gsap.set(getStaggerItems(), { opacity: 0, y: 14 });
            siteNav.classList.add('is-visible');
        })
        .to(mobileBg, { opacity: 1, scaleY: 1, duration: 0.4, ease: 'expo.out',
            onStart: () => gsap.set(mobileBg, { scaleY: 0.94, transformOrigin: 'top center' })
        })
        .to(header, { height: '100lvh', duration: 0.45, ease: 'expo.inOut' }, '<')
        
        .to(getStaggerItems(), {
            opacity: 1, y: 0,
            duration: 0.4, ease: 'back.out(1.4)', stagger: 0.06,
            onComplete: () => { isAnimating = false; }
        });
    }


    function closeMobileMenu() {
        if (isAnimating) return;
        isAnimating = true;
        mobileOpen  = false;
        toggler.setAttribute('aria-expanded', 'false');
        header.setAttribute('data-expanded',  'false');
        ScrollLock.unlock();
        navList.querySelectorAll('.nav-item.is-open').forEach(closeMobileSubmenu);

        gsap.to(toggler.children[0], { y: 0, rotation: 0, duration: 0.22, ease: 'power2.inOut' });
        gsap.to(toggler.children[1], { y: 0, rotation: 0, duration: 0.22, ease: 'power2.inOut' });

        gsap.timeline()
            // Fade items out first while nav is still full height
            .to(getStaggerItems(), { opacity: 0, y: 10, duration: 0.2, ease: 'power2.in', stagger: 0.03 })
            // Hide nav immediately after items are gone — before height collapses
            .add(() => siteNav.classList.remove('is-visible'))
            .add(() => {
                if (!header.hasAttribute('data-theme')) {
                    gsap.set(logo, { clearProps: 'filter' });
                }
            }, '<')
            .to(mobileBg, { opacity: 0, duration: 0.25, ease: 'power2.in' }, '<')
            // Height collapses last, nothing visible inside it
            .to(header, {
                height: '', duration: 0.35, ease: 'expo.inOut',
                onComplete: () => {
                    gsap.set(getStaggerItems(), { opacity: 0, y: 12 });
                    isAnimating = false;
                }
            });
    }

    // ── Mobile submenus ───────────────────────────────────────────────────────

    function openMobileSubmenu(parentItem) {
        const dropdown = parentItem.querySelector('.nav-dropdown--default');
        if (!dropdown) return;
        parentItem.classList.add('is-open');
        parentItem.querySelector('.nav-link')?.setAttribute('aria-expanded', 'true');
        gsap.fromTo(dropdown.querySelectorAll('.nav-dropdown-item'),
            { opacity: 0, y: 8 },
            { opacity: 1, y: 0, duration: 0.28, ease: 'back.out(1.4)', stagger: 0.05 }
        );
    }

    function closeMobileSubmenu(parentItem) {
        const dropdown = parentItem.querySelector('.nav-dropdown--default');
        if (!dropdown) return;
        parentItem.classList.remove('is-open');
        parentItem.querySelector('.nav-link')?.setAttribute('aria-expanded', 'false');
        gsap.to(dropdown.querySelectorAll('.nav-dropdown-item'),
            { opacity: 0, y: 6, duration: 0.16, ease: 'power2.in' }
        );
    }

    // ── Submenu click (mobile only) ───────────────────────────────────────────

    navList.querySelectorAll('.nav-item.has-dropdown').forEach(parentItem => {
        const dropdown = parentItem.querySelector('.nav-dropdown--default');
        if (!dropdown) return;

        parentItem.querySelector(':scope > .nav-link')?.addEventListener('click', e => {
            if (!isMobile()) return;
            e.preventDefault();
            parentItem.classList.contains('is-open')
                ? closeMobileSubmenu(parentItem)
                : openMobileSubmenu(parentItem);
        });

        // ── Desktop hover on parent <li> only ─────────────────────────────────
        let closeTimer;

        parentItem.addEventListener('mouseenter', () => {
            if (isMobile()) return;
            clearTimeout(closeTimer);
            if (parentItem.classList.contains('is-hovered')) return;
            parentItem.classList.add('is-hovered');
            const items = dropdown.querySelectorAll('.nav-dropdown-item');
            gsap.killTweensOf(items);
            gsap.fromTo(items,
                { opacity: 0, y: -20 },
                { opacity: 1, y: 0, duration: 0.5, stagger: 0.08, ease: 'back.out(1.4)' }
            );
        });

        parentItem.addEventListener('mouseleave', e => {
            if (isMobile()) return;
            if (parentItem.contains(e.relatedTarget)) return;
            closeTimer = setTimeout(() => {
                parentItem.classList.remove('is-hovered');
                const items = dropdown.querySelectorAll('.nav-dropdown-item');
                gsap.killTweensOf(items);
                gsap.to(items, {
                    opacity: 0,
                    y: -10,
                    duration: 0.25,
                    stagger: 0.05,
                    ease: 'power2.in',
                    onComplete: () => {
                        parentItem.classList.remove('is-hovered');
                    }
                });
            }, 80);
        });
    });

    // ── Toggler ───────────────────────────────────────────────────────────────

    toggler.addEventListener('click', () => {
        if (isAnimating) return;
        mobileOpen ? closeMobileMenu() : openMobileMenu();
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
function initHeaderTheme() {
  const header = document.querySelector('.site-header');
  if (!header) return;

  const sections = document.querySelectorAll('.header--theme-light');
  if (!sections.length) return;

  const headerHeight = header.offsetHeight + 2 + 'px';

  sections.forEach(section => {
    ScrollTrigger.create({
      trigger:     section,
      start:       `top ${headerHeight}`,
      end:         `bottom ${headerHeight}`,
      onEnter:     () => header.setAttribute('data-theme', 'light'),
      onEnterBack: () => header.setAttribute('data-theme', 'light'),
      onLeave:     () => header.removeAttribute('data-theme'),
      onLeaveBack: () => header.removeAttribute('data-theme'),
    });
  });
}

function initLoadAnimations() {

     const isHardRefresh = !sessionStorage.getItem('headerAnimated') 
    || performance.getEntriesByType('navigation')[0]?.type === 'reload';

  if (!isHardRefresh) return;
  sessionStorage.setItem('headerAnimated', '1');
  
    const header = document.querySelector('.site-header');
    if (!header) return;

    const logo = header.querySelector('.site_branding a');
    const navItems = header.querySelectorAll('.nav-list > .nav-item');

    const blurBg = header.querySelector('.navBlurBg');
    const button = header.querySelector('.site-btn');
    const buttonText = header.querySelector('.site-btn .hover-text__inner');

    gsap.set(blurBg, {
        scaleX: 0,
        transformOrigin: "right center",
        autoAlpha: 1
    });

    gsap.set(button, {
        scaleX: 0,
        transformOrigin: "right center"
    });

    gsap.set(buttonText, {
        yPercent: 120
    });

    gsap.set([logo], {
        yPercent: 120
    });

    gsap.set(navItems, {
        autoAlpha: 0,
    });

    const tl = gsap.timeline({
        defaults: {
            ease: "power2.out"
        }
    });

    tl.addLabel("intro")

    .to(blurBg, {
        scaleX: 1,
        duration: 0.8
    }, "intro")

    .to(button, {
        scaleX: 1,
        duration: 0.6
    }, "intro")
    .to(logo, {
        yPercent: 0,
        duration: 0.8
    }, "+=0.05")

    .to(buttonText, {
        yPercent: 0,
        duration: 0.04
    }, "<")

    .to(navItems, {
        autoAlpha: 1,
        duration: 0.6,
        stagger: 0.05
    }, "<")   
    
    return tl;
}
function init() {
  setInitialStates();
  initPatternBg();
  initShriCatalogScroll();
  initHeaderTheme();
  initNav();
  stickyPanels();
  initLoadAnimations();
}

function waitForImages() {
  const imgs = [...document.querySelectorAll('img:not([loading="lazy"])')];
  if (!imgs.length) return Promise.resolve();

  return Promise.all(
    imgs.map(img =>
      img.complete
        ? Promise.resolve()
        : new Promise(resolve => {
            img.addEventListener('load',  resolve, { once: true });
            img.addEventListener('error', resolve, { once: true });
          })
    )
  );
}

document.addEventListener('DOMContentLoaded', async () => {
  await Promise.all([
    document.fonts.ready,
    waitForImages(),
  ]);

  requestAnimationFrame(() => {
    init();
    initScrollAnimations();
    ScrollTrigger.refresh(true);
  });
});


let resizeTimer;
window.addEventListener('resize', () => {
  clearTimeout(resizeTimer);
  resizeTimer = setTimeout(() => {
    ScrollTrigger.refresh();
  }, 250);
});