import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react()],
  server: {
    host: '0.0.0.0',
    port: 5173,
    allowedHosts: ['lims.ddev.site'],
    proxy: {
      '/api': {
        target: 'https://lims.ddev.site',
        changeOrigin: true,
        secure: false,
      },
    },
  },
})
