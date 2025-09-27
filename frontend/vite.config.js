import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import { resolve } from 'path'

// Determinar entorno
const isDev = process.env.NODE_ENV !== 'production'

// Configurar URL base de la API según entorno
const apiBaseUrl = isDev 
  ? 'http://localhost:8080'  // Desarrollo
  : 'https://midominio.com'  // Producción (cambia por tu dominio)

export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': resolve(__dirname, 'src')
    }
  },
  server: {
    host: '0.0.0.0',
    port: 5173,
    proxy: isDev 
      ? {
          '/api': {
            target: apiBaseUrl,
            changeOrigin: true,
            secure: false
          }
        }
      : {} // No hay proxy en producción
  },
  // En producción, las peticiones irán directamente a la API
  build: {
    outDir: 'dist',
    assetsDir: 'assets',
    sourcemap: false
  },
  // Configuración de entorno
  define: {
    'process.env.NODE_ENV': JSON.stringify(process.env.NODE_ENV || 'development'),
    'API_BASE_URL': JSON.stringify(apiBaseUrl)
  }
})