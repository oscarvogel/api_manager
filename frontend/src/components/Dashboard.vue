<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 p-6">
    <!-- Header -->
    <div class="max-w-7xl mx-auto">
      <header class="mb-8">
        <h1 class="text-4xl font-bold text-gray-900 mb-2">Dashboard</h1>
        <p class="text-gray-600">Gestiona y visualiza tus datos de manera eficiente</p>
      </header>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Total Registros</p>
              <p class="text-2xl font-bold text-gray-900">{{ totalRecords }}</p>
            </div>
            <div class="bg-blue-100 p-3 rounded-full">
              <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Estado</p>
              <p class="text-2xl font-bold text-green-600">Activo</p>
            </div>
            <div class="bg-green-100 p-3 rounded-full">
              <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
              </svg>
            </div>
          </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-100">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm font-medium text-gray-600">Última Actualización</p>
              <p class="text-sm font-medium text-gray-900">{{ lastUpdate }}</p>
            </div>
            <div class="bg-purple-100 p-3 rounded-full">
              <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Data Panel -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
          <div class="p-6 border-b border-gray-200">
            <div class="flex items-center justify-between">
              <h2 class="text-xl font-semibold text-gray-900">Datos del Sistema</h2>
              <button 
                @click="fetchData"
                :disabled="loading"
                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white text-sm font-medium rounded-lg transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
              >
                <svg v-if="loading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                {{ loading ? 'Cargando...' : 'Actualizar Datos' }}
              </button>
            </div>
          </div>
          <div class="p-6">
            <div v-if="error" class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
              <div class="flex">
                <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <div class="ml-3">
                  <h3 class="text-sm font-medium text-red-800">Error al cargar datos</h3>
                  <p class="text-sm text-red-700 mt-1">{{ error }}</p>
                </div>
              </div>
            </div>
            <div v-else-if="Array.isArray(data) && data.length" class="bg-gray-50 rounded-lg p-4 overflow-x-auto">
              <table class="min-w-full table-auto">
                <thead>
                  <tr class="bg-gray-100">
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Nombre</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Email</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Teléfono</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Creado</th>
                    <th class="px-4 py-2 text-center text-sm font-medium text-gray-600">Acciones</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="row in data" :key="row.id" class="border-b">
                    <td class="px-4 py-3 text-sm text-gray-800">{{ row.nombre || row.username || '' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-800">{{ row.email }}</td>
                    <td class="px-4 py-3 text-sm text-gray-800">{{ row.telefono || '-' }}</td>
                    <td class="px-4 py-3 text-sm text-gray-800">{{ row.created_at || row.createdAt || '' }}</td>
                    <td class="px-4 py-3 text-sm text-center">
                      <button @click="openEditModal(row)" class="px-3 py-1 bg-blue-600 text-white rounded-md text-sm hover:bg-blue-700">Editar</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div v-else class="text-center py-12">
              <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <p class="text-gray-500">No hay datos disponibles</p>
              <p class="text-sm text-gray-400 mt-1">Haz clic en "Actualizar Datos" para cargar información</p>
            </div>
          </div>
        </div>

        <!-- Quick Actions Panel -->
        <div class="bg-white rounded-xl shadow-lg border border-gray-100">
          <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Acciones Rápidas</h2>
          </div>
          <div class="p-6">
            <div class="space-y-4">
              <button @click="openCreateUserModal" class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-blue-50 hover:border-blue-200 border border-transparent rounded-lg transition-all duration-200">
                <div class="flex items-center">
                  <div class="bg-blue-100 p-2 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                  </div>
                  <span class="font-medium text-gray-900">Crear Nuevo Usuario</span>
                </div>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>

              <button @click="runSystemCheck" :disabled="systemCheckLoading" class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-green-50 hover:border-green-200 border border-transparent rounded-lg transition-all duration-200 disabled:opacity-50">
                <div class="flex items-center">
                  <div class="bg-green-100 p-2 rounded-lg mr-3">
                    <svg v-if="systemCheckLoading" class="w-5 h-5 text-green-600 animate-spin" fill="none" viewBox="0 0 24 24">
                      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg v-else class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                  <span class="font-medium text-gray-900">{{ systemCheckLoading ? 'Verificando Sistema...' : 'Verificar Sistema' }}</span>
                </div>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>

              <button @click="openSettingsModal" class="w-full flex items-center justify-between p-4 bg-gray-50 hover:bg-purple-50 hover:border-purple-200 border border-transparent rounded-lg transition-all duration-200">
                <div class="flex items-center">
                  <div class="bg-purple-100 p-2 rounded-lg mr-3">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                  </div>
                  <span class="font-medium text-gray-900">Configuración del Sistema</span>
                </div>
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- System Check Results -->
      <div v-if="systemCheckResults" class="mt-8 bg-white rounded-xl shadow-lg border border-gray-100">
        <div class="p-6 border-b border-gray-200">
          <h2 class="text-xl font-semibold text-gray-900">Diagnóstico del Sistema</h2>
        </div>
        <div class="p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="check in systemCheckResults" :key="check.name" class="flex items-center p-4 rounded-lg border" :class="check.status === 'success' ? 'bg-green-50 border-green-200' : check.status === 'warning' ? 'bg-yellow-50 border-yellow-200' : 'bg-red-50 border-red-200'">
              <svg class="w-6 h-6 mr-3" :class="check.status === 'success' ? 'text-green-600' : check.status === 'warning' ? 'text-yellow-600' : 'text-red-600'" fill="currentColor" viewBox="0 0 20 20">
                <path v-if="check.status === 'success'" fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                <path v-else-if="check.status === 'warning'" fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                <path v-else fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
              </svg>
              <div>
                <h3 class="font-medium" :class="check.status === 'success' ? 'text-green-900' : check.status === 'warning' ? 'text-yellow-900' : 'text-red-900'">{{ check.name }}</h3>
                <p class="text-sm" :class="check.status === 'success' ? 'text-green-700' : check.status === 'warning' ? 'text-yellow-700' : 'text-red-700'">{{ check.message }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create User Modal -->
    <div v-if="showCreateUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="closeCreateUserModal">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" @click.stop>
        <div class="mt-3">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Crear Nuevo Usuario</h3>
            <button @click="closeCreateUserModal" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          <form @submit.prevent="createUser" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
              <input v-model="newUser.nombre" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Nombre completo">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input v-model="newUser.email" type="email" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="correo@ejemplo.com">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
              <input v-model="newUser.telefono" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Teléfono (opcional)">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña</label>
              <input v-model="newUser.password" type="password" required minlength="8" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Al menos 8 caracteres">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña</label>
              <input v-model="newUserConfirm.passwordConfirm" type="password" required minlength="8" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Repite la contraseña">
            </div>
            <div class="flex justify-end space-x-3 mt-6">
              <button type="button" @click="closeCreateUserModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md transition-colors">
                Cancelar
              </button>
              <button type="submit" :disabled="createUserLoading" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 rounded-md transition-colors">
                {{ createUserLoading ? 'Creando...' : 'Crear Usuario' }}
              </button>
            </div>
            <!-- Inline error for create user -->
            <div v-if="createUserError" class="mt-4 bg-red-50 border border-red-200 text-red-800 rounded-md p-3 text-sm">
              <strong>Error:</strong>
              <div class="mt-1">{{ createUserError }}</div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Edit User Modal -->
    <div v-if="editUser" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="closeEditModal">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" @click.stop>
        <div class="mt-3">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Editar Usuario</h3>
            <button @click="closeEditModal" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          <form @submit.prevent="saveEdit" class="space-y-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
              <input v-model="editUser.nombre" type="text" required class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Nombre completo">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
              <input v-model="editUser.email" type="email" required class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="correo@ejemplo.com">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
              <input v-model="editUser.telefono" type="text" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Teléfono (opcional)">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Contraseña (opcional)</label>
              <input v-model="editUser.password" type="password" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Dejar vacío para no cambiar">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar Contraseña</label>
              <input v-model="editUserConfirm.passwordConfirm" type="password" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Repite la contraseña">
            </div>
            <div class="flex justify-end space-x-3 mt-6">
              <button type="button" @click="closeEditModal" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-md">Cancelar</button>
              <button type="submit" :disabled="editUserLoading" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 rounded-md">{{ editUserLoading ? 'Guardando...' : 'Guardar cambios' }}</button>
            </div>
            <div v-if="editUserError" class="mt-4 bg-red-50 border border-red-200 text-red-800 rounded-md p-3 text-sm">
              <strong>Error:</strong>
              <div class="mt-1">{{ editUserError }}</div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Settings Modal -->
    <div v-if="showSettingsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" @click="closeSettingsModal">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white" @click.stop>
        <div class="mt-3">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900">Configuración del Sistema</h3>
            <button @click="closeSettingsModal" class="text-gray-400 hover:text-gray-600">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          <div>
            <div class="mb-4 border-b">
              <nav class="-mb-px flex space-x-4" aria-label="Tabs">
                <button @click="settingsTab = 'info'" :class="settingsTab === 'info' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500'" class="whitespace-nowrap py-2 px-4 border-b-2 font-medium text-sm">Info</button>
                <button @click="settingsTab = 'api_keys'; loadApiKeys()" :class="settingsTab === 'api_keys' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500'" class="whitespace-nowrap py-2 px-4 border-b-2 font-medium text-sm">API Keys</button>
                <button @click="settingsTab = 'diagnostic'; loadSystemInfo()" :class="settingsTab === 'diagnostic' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500'" class="whitespace-nowrap py-2 px-4 border-b-2 font-medium text-sm">Diagnóstico</button>
              </nav>
            </div>

            <div v-if="settingsTab === 'info'" class="space-y-4">
              <div class="p-4 bg-gray-50 rounded-lg">
                <h4 class="font-medium text-gray-900 mb-2">Información del Sistema</h4>
                <div class="text-sm text-gray-600 space-y-1">
                  <p><strong>API Key:</strong> {{ ApiService.getApiKey() ? '***' + ApiService.getApiKey().slice(-6) : 'No configurada' }}</p>
                  <p><strong>Usuario:</strong> Superadministrador</p>
                  <p><strong>Última Conexión:</strong> {{ lastUpdate }}</p>
                </div>
              </div>

              <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <h4 class="font-medium text-yellow-800 mb-2">⚠️ Zona Peligrosa</h4>
                <p class="text-sm text-yellow-700 mb-3">Estas acciones no se pueden deshacer.</p>
                <button @click="confirmLogout" class="w-full px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md transition-colors">
                  Cerrar Sesión
                </button>
              </div>
            </div>

            <div v-if="settingsTab === 'api_keys'" class="space-y-4">
              <div class="flex items-center justify-between">
                <h4 class="font-medium text-gray-900">Claves API</h4>
                <div>
                  <button @click="createNewKey" class="px-3 py-1 bg-green-600 text-white rounded-md text-sm hover:bg-green-700">Generar nueva</button>
                </div>
              </div>
              <div class="p-3 bg-gray-50 rounded-lg">
                <div v-if="apiKeysLoading" class="text-sm text-gray-500">Cargando claves...</div>
                <div v-else>
                  <div v-if="apiKeys.length">
                    <ul class="space-y-2">
                      <li v-for="k in apiKeys" :key="k.id" class="flex items-center justify-between p-2 border rounded-md bg-white">
                        <div class="text-sm">
                          <div><strong>ID:</strong> {{ k.id }}</div>
                          <div class="text-xs text-gray-500"><strong>Creada:</strong> {{ k.created_at }}</div>
                        </div>
                        <div class="flex items-center space-x-2">
                          <button @click="revokeKey(k)" class="px-2 py-1 text-sm bg-red-600 text-white rounded-md">Revocar</button>
                        </div>
                      </li>
                    </ul>
                  </div>
                  <div v-else class="text-sm text-gray-500">No hay claves registradas</div>
                </div>
              </div>
            </div>

            <div v-if="settingsTab === 'diagnostic'" class="space-y-4">
              <div class="flex items-center justify-between">
                <h4 class="font-medium text-gray-900">Diagnóstico</h4>
                <button @click="loadSystemInfo" class="px-3 py-1 bg-blue-600 text-white rounded-md text-sm">Actualizar</button>
              </div>
              <div class="p-3 bg-gray-50 rounded-lg">
                <div v-if="systemInfoLoading" class="text-sm text-gray-500">Cargando diagnóstico...</div>
                <div v-else>
                  <pre class="text-xs text-gray-700 whitespace-pre-wrap">{{ systemInfo ? JSON.stringify(systemInfo, null, 2) : 'Ejecuta la verificación para ver resultados' }}</pre>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import ApiService from '@/services/ApiService'

export default {
  name: 'Dashboard',
  data() {
    return {
      data: null,
      loading: false,
      error: null,
      totalRecords: 0,
      lastUpdate: 'Nunca',
      // Modals
      showCreateUserModal: false,
      showSettingsModal: false,
      // Create User
      newUser: {
        nombre: '',
        email: '',
        telefono: '',
        password: ''
      },
      newUserConfirm: {
        passwordConfirm: ''
      },
      createUserLoading: false,
      createUserError: null,
      // System Check
      systemCheckLoading: false,
      systemCheckResults: null
  // Settings / API Keys
  ,apiKeys: [],
  apiKeysLoading: false,
  apiKeysError: null,
  systemInfoLoading: false,
  systemInfo: null,
  settingsTab: 'info',
  // Edit user
  editUser: null,
      editUserConfirm: { passwordConfirm: '' },
      editUserLoading: false,
      editUserError: null
    }
  },
  mounted() {
    this.updateLastUpdate()
  },
  methods: {
    async fetchData() {
      this.loading = true
      this.error = null
      
      try {
  const response = await ApiService.read('usuarios', { limit: 10 })
  const payload = response.data && response.data.data ? response.data.data : response.data
  this.data = Array.isArray(payload) ? payload : []
  this.totalRecords = this.data.length
        this.updateLastUpdate()
      } catch (error) {
        console.error('Error fetching data:', error)
        const resp = error.server || error.response?.data
        const message = resp?.detail || resp?.error || error.message || 'Error desconocido al cargar los datos'
        this.error = typeof message === 'string' ? message : JSON.stringify(message)
      } finally {
        this.loading = false
      }
    },
    updateLastUpdate() {
      const now = new Date()
      this.lastUpdate = now.toLocaleString('es-ES', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      })
    },

    // === CREATE USER MODAL ===
    openCreateUserModal() {
      this.showCreateUserModal = true
      this.resetNewUser()
    },

    closeCreateUserModal() {
      this.showCreateUserModal = false
      this.resetNewUser()
    },

    resetNewUser() {
      this.newUser = {
        nombre: '',
        email: '',
        telefono: ''
      }
    },

    async createUser() {
      if (this.createUserLoading) return
      
      this.createUserLoading = true
      this.createUserError = null
      
      // Client-side password validation
      const pwd = this.newUser.password || ''
      const pwdConfirm = this.newUserConfirm.passwordConfirm || ''
      if (!pwd || pwd.length < 8) {
        this.createUserError = 'La contraseña debe tener al menos 8 caracteres.'
        this.createUserLoading = false
        return
      }
      if (pwd !== pwdConfirm) {
        this.createUserError = 'La contraseña y su confirmación no coinciden.'
        this.createUserLoading = false
        return
      }

      try {
        // Send password as plain text; backend will hash it before storing
        const payload = Object.assign({}, this.newUser)
        const response = await ApiService.create('usuarios', payload)

        // Refrescar datos
        await this.fetchData()

        // Cerrar modal
        this.closeCreateUserModal()

        // Mostrar mensaje de éxito usando SweetAlert2
        const message = response?.data?.message || 'Usuario creado exitosamente'
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'success',
            title: '¡Listo!',
            text: message,
            toast: true,
            position: 'top-end',
            timer: 3000,
            showConfirmButton: false
          })
        } else {
          console.log('Usuario creado exitosamente')
        }
        
      } catch (error) {
        console.error('Error creating user:', error)
        // Prefer server-provided structured message (ApiService attaches error.server)
        const respData = error.server || error.response?.data
        const errMsg = respData?.detail || respData?.error || (typeof respData === 'string' ? respData : null) || error.message || 'Error al crear el usuario'
        this.createUserError = typeof errMsg === 'string' ? errMsg : JSON.stringify(errMsg)

        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'error',
            title: 'No se pudo crear el usuario',
            text: typeof errMsg === 'string' ? errMsg : JSON.stringify(errMsg),
            width: 600,
          })
        }
      } finally {
        this.createUserLoading = false
      }
    },

    // === SYSTEM CHECK ===
    async runSystemCheck() {
      if (this.systemCheckLoading) return
      
      this.systemCheckLoading = true
      this.systemCheckResults = null
      
      const checks = []
      
      try {
        // Verificar conexión API
        try {
          await ApiService.read('usuarios', { limit: 1 })
          checks.push({
            name: 'Conexión API',
            status: 'success',
            message: 'API respondiendo correctamente'
          })
        } catch (error) {
          checks.push({
            name: 'Conexión API',
            status: 'error',
            message: 'Error de conexión con la API'
          })
        }

        // Verificar autenticación
        const apiKey = ApiService.getApiKey()
        if (apiKey) {
          checks.push({
            name: 'Autenticación',
            status: 'success',
            message: 'Clave API válida y activa'
          })
        } else {
          checks.push({
            name: 'Autenticación',
            status: 'error',
            message: 'No hay clave API configurada'
          })
        }

        // Verificar datos de muestra
        try {
          const response = await ApiService.read('usuarios')
          // Normalize response: API returns { data: [...] } or raw array
          const payload = response.data && response.data.data ? response.data.data : response.data
          const recordCount = Array.isArray(payload) ? payload.length : 0

          if (recordCount > 0) {
            checks.push({
              name: 'Base de Datos',
              status: 'success',
              message: `${recordCount} registros encontrados`
            })
          } else {
            checks.push({
              name: 'Base de Datos',
              status: 'warning',
              message: 'Base de datos vacía'
            })
          }
        } catch (error) {
          checks.push({
            name: 'Base de Datos',
            status: 'error',
            message: 'Error al acceder a la base de datos'
          })
        }

        // Verificar configuración del navegador
        if (typeof Storage !== 'undefined') {
          checks.push({
            name: 'LocalStorage',
            status: 'success',
            message: 'Almacenamiento local disponible'
          })
        } else {
          checks.push({
            name: 'LocalStorage',
            status: 'warning',
            message: 'LocalStorage no disponible'
          })
        }

        this.systemCheckResults = checks
        // Mostrar resumen con SweetAlert2 (toast)
        try {
          const hasError = checks.some(c => c.status === 'error')
          const hasWarning = checks.some(c => c.status === 'warning')
          if (typeof Swal !== 'undefined') {
            if (hasError) {
              Swal.fire({
                icon: 'error',
                title: 'Verificación completada',
                text: 'Se detectaron errores en el sistema. Revisa el diagnóstico.',
              })
            } else if (hasWarning) {
              Swal.fire({
                icon: 'warning',
                title: 'Verificación completada',
                text: 'Hay advertencias. Revisa el diagnóstico.',
                toast: true,
                position: 'top-end',
                timer: 3500,
                showConfirmButton: false
              })
            } else {
              Swal.fire({
                icon: 'success',
                title: 'Verificación completada',
                text: 'Todo parece estar en orden.',
                toast: true,
                position: 'top-end',
                timer: 2500,
                showConfirmButton: false
              })
            }
          }
        } catch (swalErr) {
          console.error('Swal error:', swalErr)
        }
      } catch (error) {
        console.error('System check error:', error)
        checks.push({
          name: 'Error General',
          status: 'error',
          message: 'Error durante la verificación del sistema'
        })
        this.systemCheckResults = checks
      } finally {
        this.systemCheckLoading = false
      }
    },

    // === EDIT USER ===
    openEditModal(row) {
      // Create a shallow copy to avoid mutating table directly
      this.editUser = Object.assign({}, row)
      this.editUserConfirm = { passwordConfirm: '' }
      this.editUserError = null
    },

    closeEditModal() {
      this.editUser = null
      this.editUserConfirm = { passwordConfirm: '' }
      this.editUserError = null
    },

    async saveEdit() {
      if (!this.editUser) return
      if (this.editUserLoading) return

      this.editUserLoading = true
      this.editUserError = null

      try {
        const payload = Object.assign({}, this.editUser)

        // If user provided a password, validate and include it; otherwise don't send password
        if (this.editUser.password && this.editUser.password.length > 0) {
          if (this.editUser.password.length < 8) {
            this.editUserError = 'La contraseña debe tener al menos 8 caracteres.'
            this.editUserLoading = false
            return
          }
          if (this.editUser.password !== this.editUserConfirm.passwordConfirm) {
            this.editUserError = 'La contraseña y su confirmación no coinciden.'
            this.editUserLoading = false
            return
          }
        } else {
          // Ensure we don't accidentally send undefined password
          delete payload.password
        }

        // Build where clause (assume id exists)
        const where = { id: payload.id }
        // Remove id from payload
        delete payload.id

        await ApiService.update('usuarios', where, payload)

        // Refresh data and close modal
        await this.fetchData()
        this.closeEditModal()

        if (typeof Swal !== 'undefined') {
          Swal.fire({ icon: 'success', title: 'Usuario actualizado', toast: true, position: 'top-end', timer: 2500, showConfirmButton: false })
        }
      } catch (error) {
        console.error('Error updating user:', error)
        const resp = error.server || error.response?.data
        const msg = resp?.detail || resp?.error || error.message || 'Error al actualizar usuario'
        this.editUserError = typeof msg === 'string' ? msg : JSON.stringify(msg)
        if (typeof Swal !== 'undefined') {
          Swal.fire({ icon: 'error', title: 'No se pudo actualizar el usuario', text: this.editUserError, width: 600 })
        }
      } finally {
        this.editUserLoading = false
      }
    },

    // === SETTINGS MODAL ===
    openSettingsModal() {
      this.showSettingsModal = true
    },

    closeSettingsModal() {
      this.showSettingsModal = false
    },

    confirmLogout() {
      if (confirm('¿Estás seguro de que quieres cerrar sesión?')) {
        ApiService.clearApiKey()
        this.$router.push('/login')
      }
    }
    ,

    // === API KEYS ===
    async loadApiKeys() {
      this.apiKeysLoading = true
      this.apiKeysError = null
      try {
        const resp = await ApiService.listApiKeys()
        const payload = resp.data && resp.data.data ? resp.data.data : resp.data
        this.apiKeys = Array.isArray(payload) ? payload : []
      } catch (err) {
        console.error('Error loading API keys', err)
        const resp = err.server || err.response?.data
        this.apiKeysError = resp?.detail || resp?.error || err.message || 'Error al cargar claves API'
      } finally {
        this.apiKeysLoading = false
      }
    },

    async revokeKey(key) {
      if (!confirm('Revocar esta clave API?')) return
      try {
        await ApiService.revokeApiKey(key.id)
        if (typeof Swal !== 'undefined') Swal.fire({ icon: 'success', title: 'Clave revocada', toast: true, position: 'top-end', timer: 2000, showConfirmButton: false })
        await this.loadApiKeys()
      } catch (err) {
        console.error('Error revoking key', err)
        const resp = err.server || err.response?.data
        const msg = resp?.detail || resp?.error || err.message || 'Error al revocar'
        if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'No se pudo revocar', text: msg })
      }
    },

    async createNewKey() {
      try {
        const resp = await ApiService.createApiKey()
        const apiKey = resp?.data?.api_key
        if (apiKey) {
          if (typeof Swal !== 'undefined') {
            Swal.fire({ title: 'Nueva API Key', html: `<code class="font-mono">${apiKey}</code>`, width: 600 })
          }
          await this.loadApiKeys()
        }
      } catch (err) {
        console.error('Error creating api key', err)
        const resp = err.server || err.response?.data
        const msg = resp?.detail || resp?.error || err.message || 'Error al crear clave'
        if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'No se pudo crear clave', text: msg })
      }
    },

    // === SYSTEM INFO ===
    async loadSystemInfo() {
      this.systemInfoLoading = true
      try {
        const resp = await ApiService.systemInfo()
        const payload = resp.data && resp.data.data ? resp.data.data : resp.data
        this.systemInfo = payload
        // Map to systemCheckResults for reuse
        const checks = []
        checks.push({ name: 'DEBUG', status: payload.debug ? 'warning' : 'success', message: `DEBUG=${payload.debug}` })
        if (payload.db) {
          if (payload.db.error) {
            checks.push({ name: 'DB', status: 'error', message: payload.db.error })
          } else {
            for (const [t, c] of Object.entries(payload.db.counts || {})) {
              checks.push({ name: `Tabla: ${t}`, status: c > 0 ? 'success' : 'warning', message: `${c} registros` })
            }
          }
        }
        this.systemCheckResults = checks
      } catch (err) {
        console.error('Error loading system info', err)
        const resp = err.server || err.response?.data
        const msg = resp?.detail || resp?.error || err.message || 'Error al obtener información del sistema'
        if (typeof Swal !== 'undefined') Swal.fire({ icon: 'error', title: 'Error', text: msg })
      } finally {
        this.systemInfoLoading = false
      }
    },
  }
}
</script>