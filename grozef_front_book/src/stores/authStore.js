// src/stores/authStore.js
import { defineStore } from 'pinia'
import api from '@/services/api'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null
  }),
  actions: {
    async login(email, password) {
      try {
        const response = await api.post('/login', { mail: email, password })
        this.token = response.data.token
        this.user = { email }
        localStorage.setItem('token', this.token)
        api.headers.common['Authorization'] = `Bearer ${this.token}`
        return { success: true }
      } catch (error) {
        return { success: false, error: error.response?.data?.message || 'Erreur lors de la connexion' }
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