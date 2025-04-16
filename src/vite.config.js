
import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import path from 'path';

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [react()],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
  build: {
    // Generate manifest.json in outDir
    manifest: true,
    // Output as ES module
    target: 'es2015',
    // Customize entry point
    rollupOptions: {
      input: {
        main: './src/main.tsx',
      },
      output: {
        entryFileNames: 'js/app.js',
        chunkFileNames: 'js/[name].[hash].js',
        assetFileNames: (assetInfo) => {
          const info = assetInfo.name.split('.');
          const ext = info[info.length - 1];
          if (/png|jpe?g|svg|gif|tiff|bmp|ico/i.test(ext)) {
            return `images/[name].[hash][extname]`;
          }
          if (/css/i.test(ext)) {
            return `css/app.css`;
          }
          return `assets/[name].[hash][extname]`;
        },
      },
    },
  },
});
