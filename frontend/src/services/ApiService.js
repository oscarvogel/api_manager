import axios from 'axios'

class ApiService {
  constructor() {
    // Determinar URL base según entorno
    const baseURL = import.meta.env.MODE === 'production'
      ? 'https://midominio.com/api'  // URL de producción
      : '/api'                       // Ruta para proxy en desarrollo

    this.api = axios.create({
      baseURL,
      timeout: 10000,
      headers: {
        'Content-Type': 'application/json'
      }
    })

    // Interceptor para añadir clave de API en cada petición
    this.api.interceptors.request.use(
      (config) => {
        const apiKey = localStorage.getItem('api_key')
        if (apiKey) {
          config.headers['X-API-Key'] = apiKey
        }
        return config
      },
      (error) => {
        return Promise.reject(error)
      }
    )

    // Interceptor para manejar respuestas de error
    this.api.interceptors.response.use(
      (response) => response,
      (error) => {
        if (error.response?.status === 401) {
          // Si la API devuelve 401, limpiar clave y redirigir al login
          localStorage.removeItem('api_key')
          window.location.href = '/login'
        }
        // Attach server-provided JSON (if any) to the error object for easier consumption
        if (error.response && error.response.data) {
          try {
            error.server = error.response.data
            // also log for dev convenience
            console.debug('API error payload:', error.response.data)
          } catch (e) {
            // ignore
          }
        }
        return Promise.reject(error)
      }
    )
  }

  // Autenticación
  async login(credentials) {
    // Este endpoint no requiere clave de API
    const apiNoAuth = axios.create({
      baseURL: this.api.defaults.baseURL,
      timeout: 10000
    })
    return apiNoAuth.post('/?endpoint=login', credentials)
  }

  async setupSuperAdmin() {
    // Este endpoint no requiere clave de API
    const apiNoAuth = axios.create({
      baseURL: this.api.defaults.baseURL,
      timeout: 10000
    })
    return apiNoAuth.post('/?endpoint=setup_superadmin')
  }

  // CRUD
  async read(table, params = {}) {
    const query = new URLSearchParams(params).toString()
    return this.api.get(`/?endpoint=read&table=${table}&${query}`)
  }

  async create(table, data) {
    return this.api.post('/?endpoint=create', { table, data })
  }

  async update(table, where, data) {
    return this.api.put('/?endpoint=update', { table, where, data })
  }

  async delete(table, where) {
    return this.api.delete('/?endpoint=delete', { data: { table, where } })
  }

  // Obtener clave de API actual
  getApiKey() {
    return localStorage.getItem('api_key')
  }

  // Guardar clave de API
  setApiKey(apiKey) {
    localStorage.setItem('api_key', apiKey)
  }

  // Limpiar clave de API
  clearApiKey() {
    localStorage.removeItem('api_key')
  }
}

export default new ApiService()