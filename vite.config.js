import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
  // Assets go into /src, output to /dist
  root: resolve(__dirname, 'src'),

  build: {
    outDir: resolve(__dirname, 'dist'),
    emptyOutDir: true,
    manifest: true, // generates dist/.vite/manifest.json — used by functions.php
    rollupOptions: {
      input: {
        main: resolve(__dirname, 'src/js/main.js'),
      },
    },
  },

  server: {
    host: 'localhost',
    port: 5173,
    strictPort: true,

    // Allow Vite to serve assets to the PHP dev server
    cors: true,

    // Hot reload watches PHP template changes
    watch: {
      usePolling: true, // needed inside Docker/VMs
    },
  },

  css: {
    preprocessorOptions: {
      scss: {
        // Auto-import variables + mixins everywhere
        additionalData: `@use "@/scss/abstracts" as *;`,
      },
    },
  },

  resolve: {
    alias: {
      '@': resolve(__dirname, 'src'),
    },
  },
});
