// // src/stores/authStore.js
// import { defineStore } from 'pinia'
// import api from '/services/api.js'

// export const useAuthStore = defineStore('auth', {
//   state: () => ({
//     user: null,
//     token: localStorage.getItem('token') || null
//   }),
//   getters: {
//     isAuthenticated: (state) => !!state.token
//   },
//   actions: {
//     async login(email, password) {
//       try {
//         // Envoi les bonnes clés attendues par Symfony : email et password
//         const response = await api.post('/login_check', { email, password })

//         this.token = response.data.token
//         localStorage.setItem('token', this.token)

//         // Ajoute le header Authorization dans Axios pour toutes les requêtes suivantes
//         api.defaults.headers.common['Authorization'] = `Bearer ${this.token}`

//         // Optionnel : stocke les infos user ici si besoin
//         this.user = { email }

//         return { success: true }
//       } catch (error) {
//         return {
//           success: false,
//           error: error.response?.data?.message || 'Erreur lors de la connexion'
//         }
//       }
//     },
//     logout() {
//       this.token = null
//       this.user = null
//       localStorage.removeItem('token')

//       // Supprime le header Authorization d’Axios
//       delete api.defaults.headers.common['Authorization']
//     },
//     initializeAuth() {
//       if (this.token) {
//         api.defaults.headers.common['Authorization'] = `Bearer ${this.token}`
//       }
//     }
//   }
// })


// src/stores/authStore.js
import { defineStore } from 'pinia'
import api from '/services/api.js'
import { toRaw } from 'vue'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null
  }),
  actions: {
    async login(form) {
      try {
        const payload = toRaw(form)  // Convertit le proxy Vue en objet JS simple
        const response = await api.post('/login_check', payload)
        this.token = response.data.token
        this.user = { email: payload.email }
        localStorage.setItem('token', this.token)
        api.headers.common['Authorization'] = `Bearer ${this.token}`
        return { success: true }
      } catch (error) {
        return {
          success: false,
          error: error.response?.data?.message || 'Erreur lors de la connexion'
        }
      }
    },
    logout() {
      this.token = null
      this.user = null
      localStorage.removeItem('token')
      delete api.headers.common['Authorization']
    },
    initializeAuth() {
      if (this.token) {
        api.headers.common['Authorization'] = `Bearer ${this.token}`
      }
    }
  },
  getters: {
    isAuthenticated: (state) => !!state.token
  }
})
