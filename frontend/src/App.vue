<template>
  <div id="app" class="min-h-screen bg-gray-50">
    <nav v-if="showNav" class="bg-white shadow-sm border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex items-center space-x-8">
            <div class="flex-shrink-0">
              <h1 class="text-xl font-bold text-gray-900">API Manager</h1>
            </div>
            <div class="hidden md:block">
              <div class="ml-10 flex items-baseline space-x-4">
                <router-link 
                  to="/" 
                  class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                  active-class="text-blue-600 bg-blue-50"
                >
                  Inicio
                </router-link>
                <router-link 
                  to="/setup" 
                  class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                  active-class="text-blue-600 bg-blue-50"
                >
                  Setup
                </router-link>
                <router-link 
                  v-if="!isLoggedIn"
                  to="/login" 
                  class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                  active-class="text-blue-600 bg-blue-50"
                >
                  Login
                </router-link>
                <router-link 
                  v-if="isLoggedIn"
                  to="/dashboard" 
                  class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors"
                  active-class="text-blue-600 bg-blue-50"
                >
                  Dashboard
                </router-link>
              </div>
            </div>
          </div>
          <div v-if="isLoggedIn" class="flex items-center">
            <button 
              @click="logout"
              class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200"
            >
              Cerrar sesión
            </button>
          </div>
        </div>
      </div>
    </nav>
    <main class="flex-1">
      <router-view />
    </main>
  </div>
</template>

<script>
import ApiService from './services/ApiService'

export default {
  name: 'App',
  computed: {
    isLoggedIn() {
      return !!ApiService.getApiKey()
    },
    showNav() {
      // No mostrar nav en login
      return this.$route.path !== '/login'
    }
  },
  methods: {
    logout() {
      ApiService.clearApiKey()
      this.$router.push('/login')
    }
  }
}
</script>

<style>
/* Estilos globales mínimos - TailwindCSS maneja el resto */
#app {
  font-family: 'Inter', 'Avenir', 'Helvetica', Arial, sans-serif;
}
</style>