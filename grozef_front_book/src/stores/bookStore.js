import { defineStore } from 'pinia'
import api from '/services/api.js'
import axios from 'axios'

export const useBookStore = defineStore('book', {
  state: () => ({
    books: [],
    pagination: {
      current_page: 1,
      total_pages: 1,
      total_items: 0,
      items_per_page: 8
    },
    search: ''
  }),
  actions: {
    async fetchBooks() {
      try {
        const response = await api.get('/wonderfull_books', {
          params: {
            page: this.pagination.current_page,
            limit: this.pagination.items_per_page,
            search: this.search
          }
        })
        this.books = await Promise.all(
          response.data.data.map(async (book) => {
            const bookDetails = await this.fetchBookDetails(book.isbn)
            return {
              ...book,
              cover: bookDetails.cover || null
            }
          })
        )
        this.pagination = response.data.pagination
        return { success: true }
      } catch (error) {
        return { success: false, error: error.response?.data?.errors || { general: 'Erreur lors du chargement des livres' } }
      }
    },
    async fetchBookDetails(isbn) {
      try {
        const response = await axios.get(`https://openlibrary.org/api/books?bibkeys=ISBN:${isbn}&format=json&jscmd=data`)
        const data = response.data[`ISBN:${isbn}`]
        return {
          cover: data?.cover?.medium || null
        }
      } catch (error) {
        console.error('Erreur lors de la récupération des détails du livre:', error)
        return { cover: null }
      }
    },
    async createBook(data) {
      try {
        await api.post('/wonderfull_books', data)
        await this.fetchBooks()
        return { success: true }
      } catch (error) {
        return { success: false, error: error.response?.data?.errors || { general: 'Erreur lors de la création' } }
      }
    },
    async updateBook(id, data) {
      try {
        await api.put(`/wonderfull_books/${id}`, data)
        await this.fetchBooks()
        return { success: true }
      } catch (error) {
        return { success: false, error: error.response?.data?.errors || { general: 'Erreur lors de la mise à jour' } }
      }
    },
    async deleteBook(id) {
      try {
        await api.delete(`/wonderfull_books/${id}`)
        await this.fetchBooks()
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