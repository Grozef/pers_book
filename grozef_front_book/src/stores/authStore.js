// src/stores/authStore.js
import { defineStore } from 'pinia'
import api from '/services/api.js'
import { toRaw } from 'vue'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
  }),
  getters: {
    isAuthenticated: (state) => !!state.token,
  },
  actions: {
    async login(form) {
      try {
        const payload = toRaw(form); // Convertit le proxy Vue en objet JS simple
        const response = await api.post('/login_check', payload);

        this.token = response.data.token;
        this.user = { email: payload.email }; // Stocke uniquement les données nécessaires
        localStorage.setItem('token', this.token);

        // Configure le header Authorization pour les requêtes futures
        api.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;

        return { success: true };
      } catch (error) {
        // Gestion des erreurs plus précise
        const errorMessage =
          error.response?.data?.message ||
          error.message ||
          'Erreur lors de la connexion';
        return { success: false, error: errorMessage };
      }
    },
    logout() {
      this.token = null;
      this.user = null;
      localStorage.removeItem('token');
      delete api.defaults.headers.common['Authorization'];
    },
    initializeAuth() {
      if (this.token) {
        api.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
      }
    },
  },
});