# WP Theme

Minimal WordPress theme with Vite, GSAP, Lenis, and SCSS.

## Structure

```
wp-theme/
├── src/
│   ├── js/
│   │   └── main.js          # Entry point — GSAP, Lenis init
│   └── scss/
│       ├── main.scss         # Imports abstracts + base
│       ├── abstracts.scss    # Variables, mixins (no CSS output)
│       └── base.scss         # Reset, global styles
├── dist/                     # Built assets (git-ignored)
├── functions.php             # Enqueue — dev vs prod
├── vite.config.js
├── header.php
├── footer.php
├── index.php
└── style.css                 # Required by WordPress (theme header only)
```

## Dev setup

```bash
npm install
npm run dev
```

Then in `wp-config.php`, tell WordPress to use the Vite dev server:

```php
define( 'WP_VITE_DEV', true );
```

Vite runs on `localhost:5173`. Your PHP site runs on its normal URL (Local, MAMP, etc.).
HMR works for JS and SCSS. PHP template changes auto-refresh via Vite's `watch`.

## Production

```bash
npm run build
```

Remove (or set to false) `WP_VITE_DEV` in `wp-config.php`.
`functions.php` will read `dist/.vite/manifest.json` and enqueue hashed assets automatically.

## Animations

Add `data-animate` to any element you want to fade/slide in on page load.
GSAP picks these up in `pageEnter()` inside `main.js`.

Lenis is synced to GSAP's ticker, so `ScrollTrigger` works without extra config.
