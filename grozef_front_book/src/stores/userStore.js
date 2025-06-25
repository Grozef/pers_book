// src/stores/userStore.js
import { defineStore } from 'pinia'
import api from '@/services/api'

export const useUserStore = defineStore('user', {
  state: () => ({
    users: [],
    pagination: {
      current_page: 1,
      total_pages: 1,
      total_items: 0,
      items_per_page: 8
    },
    search: ''
  }),
  actions: {
    async fetchUsers() {
      try {
        const response = await api.get('/users', {
          params: {
            page: this.pagination.current_page,
            limit: this.pagination.items_per_page,
            search: this.search
          }
        })
        this.users = response.data.data
        this.pagination = response.data.pagination
        return { success: true }
      } catch (error) {
        return { success: false, error: error.response?.data?.errors || { general: 'Erreur lors du chargement des utilisateurs' } }
      }
    },
    async createUser(data) {
      try {
        await api.post('/users', data)
        await this.fetchUsers()
        return { success: true }
      } catch (error) {
        return { success: false, error: error.response?.data?.errors || { general: 'Erreur lors de la création' } }
      }
    },
    async updateUser(id, data) {
      try {
        await api.put(`/users/${id}`, data)
        await this.fetchUsers()
        return { success: true }
      } catch (error) {
        return { success: false, error: error.response?.data?.errors || { general: 'Erreur lors de la mise à jour' } }
      }
    },
    async deleteUser(id) {
      try {
        await api.delete(`/users/${id}`)
        await this.fetchUsers()
        return { success: true }
      } catch (error) {
        return { success: false, error: error.response?.data?.errors || { general: 'Erreur lors de la suppression' } }
      }
    },
    setSearch(search) {
      this.search = search
      this.pagination.current_page = 1
    },
    setPage(page) {
      this.pagination.current_page = page
    }
  }
})