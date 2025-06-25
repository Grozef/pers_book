// src/stores/publisherStore.js
import { defineStore } from 'pinia'
import api from '/services/api.js'

export const usePublisherStore = defineStore('publisher', {
  state: () => ({
    publishers: [],
    pagination: {
      current_page: 1,
      total_pages: 1,
      total_items: 0,
      items_per_page: 8
    },
    search: ''
  }),
  actions: {
    async fetchPublishers(limit = 8) {
      try {
        const response = await api.get('/fierce_publishers', {
          params: {
            page: this.pagination.current_page,
            limit,
            search: this.search
          }
        })
        this.publishers = response.data.data
        this.pagination = response.data.pagination
        return { success: true }
      } catch (error) {
        return { success: false, error: error.response?.data?.errors || { general: 'Erreur lors du chargement des éditeurs' } }
      }
    },
    async createPublisher(data) {
      try {
        await api.post('/fierce_publishers', data)
        await this.fetchPublishers()
        return { success: true }
      } catch (error) {
        return { success: false, error: error.response?.data?.errors || { general: 'Erreur lors de la création' } }
      }
    },
    async updatePublisher(id, data) {
      try {
        await api.put(`/fierce_publishers/${id}`, data)
        await this.fetchPublishers()
        return { success: true }
      } catch (error) {
        return { success: false, error: error.response?.data?.errors || { general: 'Erreur lors de la mise à jour' } }
      }
    },
    async deletePublisher(id) {
      try {
        await api.delete(`/fierce_publishers/${id}`)
        await this.fetchPublishers()
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