// src/stores/imageStore.js
import { defineStore } from 'pinia'
import api from '/services/api.js'

export const useImageStore = defineStore('image', {
  state: () => ({
    images: [],
    pagination: {
      current_page: 1,
      total_pages: 1,
      total_items: 0,
      items_per_page: 8
    },
    search: ''
  }),
  actions: {
    async fetchImages() {
      try {
        const response = await api.get('/stunning_images', {
          params: {
            page: this.pagination.current_page,
            limit: this.pagination.items_per_page,
            search: this.search
          }
        })
        this.images = response.data.data
        this.pagination = response.data.pagination
        return { success: true }
      } catch (error) {
        return { success: false, error: error.response?.data?.errors || { general: 'Erreur lors du chargement des images' } }
      }
    },
    async createImage(data) {
      try {
        await api.post('/stunning_images', data)
        await this.fetchImages()
        return { success: true }
      } catch (error) {
        return { success: false, error: error.response?.data?.errors || { general: 'Erreur lors de la création' } }
      }
    },
    async updateImage(id, data) {
      try {
        await api.put(`/stunning_images/${id}`, data)
        await this.fetchImages()
        return { success: true }
      } catch (error) {
        return { success: false, error: error.response?.data?.errors || { general: 'Erreur lors de la mise à jour' } }
      }
    },
    async deleteImage(id) {
      try {
        await api.delete(`/stunning_images/${id}`)
        await this.fetchImages()
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