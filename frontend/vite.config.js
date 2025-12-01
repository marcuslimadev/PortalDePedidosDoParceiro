import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
  plugins: [vue()],
  server: {
    port: 5173,
    host: '0.0.0.0'
  },
  preview: {
    port: 4173,
    host: '0.0.0.0'
  },
  build: {
    sourcemap: true, // Enable source maps for Sentry
    rollupOptions: {
      input: {
        main: './index.html',
        sw: './public/service-worker.js'
      },
      output: {
        entryFileNames: (chunkInfo) => {
          return chunkInfo.name === 'sw' ? 'service-worker.js' : 'assets/[name]-[hash].js';
        },
        sourcemapExcludeSources: true // Don't include source code in maps
      }
    }
  }
});

