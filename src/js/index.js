// main.js
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';
import Lenis from 'lenis';
import { initShriCatalogScroll } from './shri-catalog';
gsap.registerPlugin(ScrollTrigger);
// ─── Smooth scroll (Lenis) ─────────────────────────────────

const lenis = new Lenis({
  duration: 1.2,
  easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
});

gsap.ticker.add((time) => lenis.raf(time * 1000));
gsap.ticker.lagSmoothing(0);

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

function stickyPanels() {
  const section   = document.querySelector('.trusted_leader');
  const separator = section?.querySelector('.bordered_separator span');
  const panels    = Array.from(section?.querySelectorAll('.accordion_panel') ?? []);

  if (!section || !panels.length) return;

  const getContent = (panel) => panel.querySelector('.wp-block-group__inner-container > .wp-block-group');

  const tl = gsap.timeline({ paused: true });

  const segDuration = 1 / (panels.length - 1 || 1);
  const totalScroll = window.innerHeight * 0.5 * (panels.length - 1) + window.innerHeight * 0.3;

  tl.to(separator, { width: '100%', ease: 'none', duration: 1 }, 0);

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

    tl.to(getContent(panels[i - 1]), { opacity: 0, y: -10, height: 0,                ease: 'power2.inOut', duration: segDuration }, segStart);
    tl.to(content,                   { opacity: 1, y:   0, height: content.scrollHeight, ease: 'power2.inOut', duration: segDuration }, segStart);
  });

  ScrollTrigger.create({
    trigger: section,
    start:   'top top',
    end:     `+=${totalScroll * 1.3}`,
    pin:     true,
    scrub:   1,
    markers: true,
    invalidateOnRefresh: true,
    onUpdate:  self => tl.progress(self.progress),
    onRefresh: self => tl.progress(self.progress),
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


// ─── Init ─────────────────────────────────────────────────

function initNav() {
  const header   = document.querySelector('.site-header');
  const toggler  = document.getElementById('nav-toggler');
  const siteNav  = document.getElementById('site-nav');
  const navList  = document.querySelector('.nav-list');
  const mobileBg = document.querySelector('.navbar_background-mobile');
  const logo     = document.querySelector('.site_branding img');
  const siteCta  = document.querySelector('.site_cta');

  if (!header || !toggler || !siteNav || !navList) return;

  const BREAK    = 768;
  const isMobile = () => window.innerWidth < BREAK;

  let mobileOpen  = false;
  let isAnimating = false;
  let wasMobile   = isMobile();

  const getStaggerItems = () =>
    [...navList.querySelectorAll(':scope > .nav-item'), siteCta].filter(Boolean);

  // ── Initial states ──────────────────────────────────────

  function setInitialStates() {
    gsap.set(getStaggerItems(), { opacity: 0, y: 12 });
    navList.querySelectorAll('.nav-dropdown--default .nav-dropdown-item')
      .forEach(item => gsap.set(item, { opacity: 0, y: 6 }));
  }

  // ── Reset ───────────────────────────────────────────────

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
    siteNav.style.visibility    = 'hidden';
    siteNav.style.pointerEvents = 'none';
    navList.querySelectorAll('.nav-item.is-open')
      .forEach(item => closeMobileSubmenu(item));
  }

  function resetDesktopState() {
    resetCommon();
    gsap.set([header, logo, mobileBg, siteNav, navList, ...getStaggerItems(), ...toggler.children], { clearProps: 'all' });
    siteNav.style.visibility    = '';
    siteNav.style.pointerEvents = '';
    navList.querySelectorAll('.nav-item.is-open')
      .forEach(item => item.classList.remove('is-open'));
  }

  // ── Mobile menu ─────────────────────────────────────────

  function openMobileMenu() {
    if (isAnimating) return;
    isAnimating = true;
    mobileOpen  = true;
    toggler.setAttribute('aria-expanded', 'true');
    header.setAttribute('data-expanded',  'true');

    siteNav.style.visibility    = 'visible';
    siteNav.style.pointerEvents = 'auto';

    gsap.to(toggler.children[0], { y:  4, rotation:  45, duration: 0.25, ease: 'power2.inOut' });
    gsap.to(toggler.children[1], { y: -3, rotation: -45, duration: 0.25, ease: 'power2.inOut' });
    gsap.to(header, { height: '100lvh', duration: 0.45, ease: 'expo.inOut' });
    gsap.to(logo,   { filter: 'brightness(0) invert(1)', duration: 0.3 });

    gsap.fromTo(mobileBg,
      { opacity: 0, scaleY: 0.94, transformOrigin: 'top center' },
      { opacity: 1, scaleY: 1, duration: 0.4, ease: 'expo.out',
        onComplete: () => { isAnimating = false; }
      }
    );

    gsap.fromTo(getStaggerItems(),
      { opacity: 0, y: 14 },
      { opacity: 1, y: 0, duration: 0.5, ease: 'back.out(1.4)', stagger: 0.06, delay: 0.15 }
    );
  }

  function closeMobileMenu() {
    if (isAnimating) return;
    isAnimating = true;
    mobileOpen  = false;
    toggler.setAttribute('aria-expanded', 'false');
    header.setAttribute('data-expanded',  'false');

    navList.querySelectorAll('.nav-item.is-open')
      .forEach(item => closeMobileSubmenu(item));

    gsap.to(toggler.children[0], { y: 0, rotation: 0, duration: 0.22, ease: 'power2.inOut' });
    gsap.to(toggler.children[1], { y: 0, rotation: 0, duration: 0.22, ease: 'power2.inOut' });

    gsap.timeline()
      .to(getStaggerItems(), { opacity: 0, y: 10, duration: 0.2, ease: 'power2.in', stagger: 0.03 })
      .to(mobileBg, { opacity: 0, duration: 0.25, ease: 'power2.in' }, '<')
      .to(logo,     { filter: 'brightness(1) invert(0)', duration: 0.3, ease: 'power1.inOut' }, '<')
      .to(header,   { height: '', duration: 0.35, ease: 'expo.inOut',
          onComplete: () => {
            siteNav.style.visibility    = 'hidden';
            siteNav.style.pointerEvents = 'none';
            gsap.set(getStaggerItems(), { opacity: 0, y: 12 });
            isAnimating = false;
          }
        });
  }

  // ── Mobile submenus ─────────────────────────────────────

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

  // ── Desktop dropdowns ───────────────────────────────────

  navList.querySelectorAll('.nav-item.has-dropdown').forEach(parentItem => {
    const dropdown = parentItem.querySelector('.nav-dropdown--default');
    if (!dropdown) return;

    const trigger = parentItem.querySelector(':scope > .nav-link');
    let closeTimer;

    trigger?.addEventListener('click', e => {
      if (!isMobile()) return;
      e.preventDefault();
      parentItem.classList.contains('is-open')
        ? closeMobileSubmenu(parentItem)
        : openMobileSubmenu(parentItem);
    });

    const openDropdown = () => {
      if (isMobile()) return;
      clearTimeout(closeTimer);
      if (parentItem.classList.contains('is-hovered')) return;
      parentItem.classList.add('is-hovered');
      const items = dropdown.querySelectorAll('.nav-dropdown-item');
      gsap.killTweensOf(items);
      gsap.fromTo(items,
        { opacity: 0, y: 8 },
        { opacity: 1, y: 0, duration: 0.25, stagger: 0.05, ease: 'back.out(1.4)' }
      );
    };

    const closeDropdown = (e) => {
      if (isMobile()) return;
      if (parentItem.contains(e.relatedTarget)) return;
      closeTimer = setTimeout(() => {
        parentItem.classList.remove('is-hovered');
        const items = dropdown.querySelectorAll('.nav-dropdown-item');
        gsap.killTweensOf(items);
        gsap.set(items, { opacity: 0, y: 6 });
      }, 80);
    };

parentItem.addEventListener('mouseenter', openDropdown);
parentItem.addEventListener('mouseleave', closeDropdown);

    parentItem.addEventListener('mouseenter', openDropdown);
    parentItem.addEventListener('mouseleave', closeDropdown);
  });

  // ── Toggler ─────────────────────────────────────────────

  toggler.addEventListener('click', () => {
    if (isAnimating) return;
    mobileOpen ? closeMobileMenu() : openMobileMenu();
  });

  // ── Resize ──────────────────────────────────────────────

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

  // ── Init ────────────────────────────────────────────────

  setInitialStates();
  isMobile() ? resetMobileState() : resetDesktopState();
}
function init() {
  pageEnter();
  initPatternBg();
  initShriCatalogScroll();
  stickyPanels();
  initNav(); 
}

document.addEventListener('DOMContentLoaded', init);

ScrollTrigger.refresh();
let resizeTimer;
window.addEventListener('resize', () => {
  clearTimeout(resizeTimer);
  resizeTimer = setTimeout(() => {
    ScrollTrigger.refresh();
  }, 150);
});