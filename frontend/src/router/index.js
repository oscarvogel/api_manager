import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Setup from '../views/Setup.vue'
import Login from '../components/Login.vue'
import Dashboard from '../components/Dashboard.vue'

// Rutas protegidas (requieren autenticación)
const requireAuth = (to, from, next) => {
  const apiKey = localStorage.getItem('api_key')
  if (!apiKey) {
    next('/login')
  } else {
    next()
  }
}

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home
  },
  {
    path: '/setup',
    name: 'Setup',
    component: Setup
  },
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { requiresGuest: true } // No requiere autenticación
  },
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: Dashboard,
    beforeEnter: requireAuth // Requiere autenticación
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Middleware global para proteger rutas
router.beforeEach((to, from, next) => {
  if (to.meta.requiresGuest && localStorage.getItem('api_key')) {
    // Si está logueado y va a login, redirigir al dashboard
    next('/dashboard')
  } else {
    next()
  }
})

export default router