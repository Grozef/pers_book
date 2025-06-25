// src/stores/videoStore.js
import { defineStore } from 'pinia'
import api from '/services/api.js'

export const useVideoStore = defineStore('video', {
  state: () => ({
    videos: [],
    pagination: {
      current_page: 1,
      total_pages: 1,
      total_items: 0,
      items_per_page: 8
    },
    search: ''
  }),
  actions: {
    async fetchVideos() {
      try {
        const response = await api.get('/astonishing_videos', {
          params: {
            page: this.pagination.current_page,
            limit: this.pagination.items_per_page,
            search: this.search
          }
        })
        this.videos = response.data.data
        this.pagination = response.data.pagination
        return { success: true }
      } catch (error) {
        return { success: false, error: error.response?.data?.errors || { general: 'Erreur lors du chargement des vidéos' } }
      }
    },
    async createVideo(data) {
      try {
        await api.post('/astonishing_videos', data)
        await this.fetchVideos()
        return { success: true }
      } catch (error) {
        return { success: false, error: error.response?.data?.errors || { general: 'Erreur lors de la création' } }
      }
    },
    async updateVideo(id, data) {
      try {
        await api.put(`/astonishing_videos/${id}`, data)
        await this.fetchVideos()
        return { success: true }
      } catch (error) {
        return { success: false, error: error.response?.data?.errors || { general: 'Erreur lors de la mise à jour' } }
      }
    },
    async deleteVideo(id) {
      try {
        await api.delete(`/astonishing_videos/${id}`)
        await this.fetchVideos()
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