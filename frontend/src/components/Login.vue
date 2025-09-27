<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 flex items-center justify-center p-6">
    <div class="max-w-md w-full space-y-8">
      <!-- Logo y Header -->
      <div class="text-center">
        <div class="mx-auto w-20 h-20 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full flex items-center justify-center mb-6 shadow-lg">
          <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
          </svg>
        </div>
        <h2 class="text-3xl font-bold text-gray-900 mb-2">Iniciar Sesión</h2>
        <p class="text-gray-600">Accede a tu cuenta para continuar</p>
      </div>

      <!-- Formulario de Login -->
      <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8">
        <form @submit.prevent="login" class="space-y-6">
          <!-- Campo Usuario -->
          <div>
            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
              Usuario
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
              <input
                id="username"
                v-model="username"
                type="text"
                required
                class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                placeholder="Ingresa tu usuario"
              />
            </div>
          </div>

          <!-- Campo Contraseña -->
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
              Contraseña
            </label>
            <div class="relative">
              <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
              </div>
              <input
                id="password"
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                required
                class="block w-full pl-10 pr-12 py-3 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                placeholder="Ingresa tu contraseña"
              />
              <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                <button
                  type="button"
                  @click="togglePasswordVisibility"
                  class="text-gray-400 hover:text-gray-600 focus:outline-none focus:text-gray-600 transition-colors duration-200"
                >
                  <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                  </svg>
                  <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>

          <!-- Opciones adicionales -->
          <div class="flex items-center justify-between">
            <div class="flex items-center">
              <input
                id="remember-me"
                v-model="rememberMe"
                type="checkbox"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              />
              <label for="remember-me" class="ml-2 block text-sm text-gray-700">
                Recordarme
              </label>
            </div>
            <div class="text-sm">
              <a href="#" class="font-medium text-blue-600 hover:text-blue-500 transition-colors duration-200">
                ¿Olvidaste tu contraseña?
              </a>
            </div>
          </div>

          <!-- Botón de Login -->
          <div>
            <button
              type="submit"
              :disabled="loading || !username || !password"
              class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 transform hover:scale-105 disabled:hover:scale-100"
            >
              <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
              </svg>
              {{ loading ? 'Iniciando sesión...' : 'Iniciar Sesión' }}
            </button>
          </div>
        </form>

        <!-- Mensajes de Estado -->
        <div v-if="successMessage" class="mt-4 bg-green-50 border border-green-200 rounded-lg p-4">
          <div class="flex">
            <svg class="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <p class="text-sm text-green-700">{{ successMessage }}</p>
          </div>
        </div>

        <div v-if="errorMessage" class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
          <div class="flex">
            <svg class="w-5 h-5 text-red-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <div>
              <h3 class="text-sm font-medium text-red-800">Error de autenticación</h3>
              <p class="text-sm text-red-700 mt-1">{{ errorMessage }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="text-center">
        <p class="text-sm text-gray-600">
          ¿No tienes una cuenta?
          <router-link to="/setup" class="font-medium text-blue-600 hover:text-blue-500 transition-colors duration-200">
            Configurar Sistema
          </router-link>
        </p>
        <p class="text-xs text-gray-400 mt-2">
          Credenciales por defecto: <strong>superadmin</strong> / <strong>admin123</strong>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import ApiService from '@/services/ApiService'

export default {
  name: 'Login',
  data() {
    return {
      username: '',
      password: '',
      rememberMe: false,
      showPassword: false,
      loading: false,
      successMessage: '',
      errorMessage: ''
    }
  },
  methods: {
    async login() {
      if (this.loading) return
      
      this.loading = true
      this.successMessage = ''
      this.errorMessage = ''
      
      try {
        const response = await ApiService.login({
          username: this.username,
          password: this.password
        })
        
        // Guardar la API key
        ApiService.setApiKey(response.data.api_key)
        
        // Si el usuario marcó "Recordarme", guardar las credenciales
        if (this.rememberMe) {
          localStorage.setItem('saved_username', this.username)
        } else {
          localStorage.removeItem('saved_username')
        }
        
        this.successMessage = 'Inicio de sesión exitoso. Redirigiendo...'
        
        // Redirigir después de un breve delay para mostrar el mensaje
        setTimeout(() => {
          this.$router.push('/dashboard')
        }, 1500)
        
      } catch (error) {
        console.error('Login error:', error)
        
        // Manejar diferentes tipos de errores
        // Preferir payload servidor adjuntado por ApiService: error.server
        const server = error.server || error.response?.data
        if (server) {
          // If server includes a detailed debug field, show it first
          if (server.detail) {
            this.errorMessage = server.detail
          } else if (server.error) {
            this.errorMessage = server.error
          } else if (typeof server === 'string') {
            this.errorMessage = server
          } else {
            this.errorMessage = 'Credenciales incorrectas. Verifica tu usuario y contraseña.'
          }
        } else if (error.request) {
          this.errorMessage = 'No se pudo conectar con el servidor. Verifica que el servidor API esté ejecutándose.'
        } else {
          this.errorMessage = 'Error de conexión. Intenta nuevamente.'
        }
      } finally {
        this.loading = false
      }
    },
    
    togglePasswordVisibility() {
      this.showPassword = !this.showPassword
    },
    
    clearMessages() {
      this.successMessage = ''
      this.errorMessage = ''
    }
  },
  
  mounted() {
    // Si ya está autenticado, redirigir al dashboard
    if (ApiService.getApiKey()) {
      this.$router.push('/dashboard')
      return
    }
    
    // Cargar username guardado si existe
    const savedUsername = localStorage.getItem('saved_username')
    if (savedUsername) {
      this.username = savedUsername
      this.rememberMe = true
    }
  },
  
  watch: {
    // Limpiar mensajes cuando el usuario empiece a escribir
    username() {
      if (this.errorMessage) this.clearMessages()
    },
    password() {
      if (this.errorMessage) this.clearMessages()
    }
  }
}
</script>