import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [
    laravel({
      input: 'resources/js/app.js',
      refresh: [
        'resources/views/**/*.blade.php',
        'resources/js/**/*.vue',
        'resources/js/**/*.js',
        'resources/css/**/*.css',
      ],
    }),
    vue({
      template: {
        transformAssetUrls: {
          base: null,
          includeAbsolute: false,
        },
      },
    }),
  ],
  server: {
    watch: {
      ignored: ['**/resources/lang/**'],
    },
  },
})
