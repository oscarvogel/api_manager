<template>
  <div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50 flex items-center justify-center p-6">
    <div class="max-w-md w-full">
      <!-- Header -->
      <div class="text-center mb-8">
        <div class="mx-auto w-16 h-16 bg-gradient-to-r from-purple-600 to-blue-600 rounded-full flex items-center justify-center mb-4">
          <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
          </svg>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Configuración Inicial</h1>
        <p class="text-gray-600">Configura tu administrador principal del sistema</p>
      </div>

      <!-- Main Card -->
      <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
        <div class="mb-6">
          <h2 class="text-xl font-semibold text-gray-900 mb-2">Setup Superadministrador</h2>
          <p class="text-sm text-gray-600">
            Este proceso creará la cuenta de administrador principal con acceso completo al sistema.
          </p>
        </div>

        <!-- Warning Box -->
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
          <div class="flex">
            <svg class="w-5 h-5 text-amber-400 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
            </svg>
            <div>
              <h3 class="text-sm font-medium text-amber-800">Importante</h3>
              <p class="text-sm text-amber-700 mt-1">
                Solo ejecuta este proceso una vez. Guarda la clave API generada en un lugar seguro.
              </p>
            </div>
          </div>
        </div>

        <!-- Setup Button -->
        <div class="mb-6">
          <button 
            @click="setupSuperAdmin"
            :disabled="loading || hasSetup"
            class="w-full flex justify-center items-center px-6 py-3 bg-gradient-to-r from-purple-600 to-blue-600 hover:from-purple-700 hover:to-blue-700 disabled:from-gray-300 disabled:to-gray-300 text-white font-semibold rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 disabled:cursor-not-allowed transform hover:scale-105 disabled:hover:scale-100"
          >
            <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <svg v-else-if="!hasSetup" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ loading ? 'Configurando...' : hasSetup ? 'Configuración Completa' : 'Crear Superadministrador' }}
          </button>
        </div>

        <!-- Success Message -->
        <div v-if="successMessage" class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
          <div class="flex">
            <svg class="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <div class="flex-1">
              <h3 class="text-sm font-medium text-green-800">¡Configuración Exitosa!</h3>
              <div class="mt-2">
                <p class="text-sm text-green-700 mb-2">Tu clave API ha sido generada:</p>
                <div class="bg-gray-800 rounded-lg p-3 relative">
                  <code class="text-green-400 font-mono text-sm break-all">{{ apiKey }}</code>
                  <button 
                    @click="copyApiKey"
                    class="absolute top-2 right-2 p-1 hover:bg-gray-700 rounded transition-colors"
                    title="Copiar al portapapeles"
                  >
                    <svg class="w-4 h-4 text-gray-400 hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                    </svg>
                  </button>
                </div>
                <p class="text-xs text-green-600 mt-2">⚠️ Guarda esta clave en un lugar seguro. No se volverá a mostrar.</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Error Message -->
        <div v-if="errorMessage" class="bg-red-50 border border-red-200 rounded-lg p-4">
          <div class="flex">
            <svg class="w-5 h-5 text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <div>
              <h3 class="text-sm font-medium text-red-800">Error en la configuración</h3>
              <p class="text-sm text-red-700 mt-1">{{ errorMessage }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="text-center mt-6">
        <p class="text-sm text-gray-500">
          ¿Ya tienes una cuenta? 
          <router-link to="/login" class="font-medium text-purple-600 hover:text-purple-500 transition-colors">
            Inicia sesión aquí
          </router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import ApiService from '@/services/ApiService'

export default {
  name: 'Setup',
  data() {
    return {
      loading: false,
      successMessage: '',
      errorMessage: '',
      apiKey: '',
      hasSetup: false
    }
  },
  methods: {
    async setupSuperAdmin() {
      if (this.loading || this.hasSetup) return
      
      this.loading = true
      this.successMessage = ''
      this.errorMessage = ''
      
      try {
        const response = await ApiService.setupSuperAdmin()
        
        this.apiKey = response.data.api_key
        this.successMessage = 'Superadministrador creado exitosamente'
        this.hasSetup = true
        
        // Auto-redirect to login after 10 seconds
        setTimeout(() => {
          this.$router.push('/login')
        }, 10000)
        
      } catch (error) {
        console.error('Setup error:', error)
        
        // Manejar diferentes tipos de errores
        if (error.response) {
          // Error HTTP con respuesta del servidor
          const errorData = error.response.data
          if (typeof errorData === 'string') {
            // Si la respuesta es HTML (error de PHP)
            if (errorData.includes('Fatal error') || errorData.includes('Warning')) {
              this.errorMessage = 'Error de base de datos. Verifica que MySQL esté ejecutándose y la base de datos esté configurada correctamente.'
            } else {
              this.errorMessage = errorData
            }
          } else if (errorData && errorData.error) {
            // Error JSON estructurado
            this.errorMessage = errorData.error
          } else {
            this.errorMessage = `Error HTTP ${error.response.status}: ${error.response.statusText}`
          }
        } else if (error.request) {
          // Error de red
          this.errorMessage = 'No se pudo conectar con el servidor. Verifica que el servidor API esté ejecutándose.'
        } else {
          // Error de configuración
          this.errorMessage = error.message || 'Error desconocido al crear el superadministrador.'
        }
      } finally {
        this.loading = false
      }
    },
    
    async copyApiKey() {
      try {
        await navigator.clipboard.writeText(this.apiKey)
        // You could add a toast notification here
        console.log('API Key copied to clipboard')
      } catch (err) {
        console.error('Failed to copy API key:', err)
        // Fallback: select the text
        this.selectApiKeyText()
      }
    },
    
    selectApiKeyText() {
      const codeElement = document.querySelector('code')
      if (codeElement) {
        const range = document.createRange()
        range.selectNode(codeElement)
        window.getSelection().removeAllRanges()
        window.getSelection().addRange(range)
      }
    }
  },
  
  mounted() {
    // Check if already has setup by trying to login
    if (ApiService.getApiKey()) {
      this.$router.push('/dashboard')
    }
  }
}
</script>