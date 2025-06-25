<!-- src/components/book/BookList.vue -->
<template>
  <div>
    <h2>Livres</h2>
    <b-alert v-if="Object.keys(errors).length" variant="danger" show>
      <ul>
        <li v-for="(error, field) in errors" :key="field">{{ field }}: {{ error }}</li>
      </ul>
    </b-alert>
    <SearchBar @search="handleSearch" />
    <b-table striped hover :items="bookStore.books" :fields="fields">
      <template #cell(actions)="row">
        <b-button size="sm" @click="editBook(row.item)" variant="primary">Modifier</b-button>
        <b-button size="sm" @click="deleteBook(row.item.id)" variant="danger">Supprimer</b-button>
      </template>
    </b-table>
    <ThePagination
      v-model:currentPage="bookStore.pagination.current_page"
      :total-items="bookStore.pagination.total_items"
      :items-per-page="bookStore.pagination.items_per_page"
    />
    <b-button @click="showCreateModal" variant="success">Ajouter un livre</b-button>
    <BookForm
      v-if="showModal"
      :book="selectedBook"
      @save="saveBook"
      @close="closeModal"
    />
  </div>
</template>

<script>
import { useAppStore } from '@/stores/appStore'
import { useBookStore } from '@/stores/bookStore'
import SearchBar from '@/components/common/SearchBar.vue'
import ThePagination from '@/components/common/ThePagination.vue'
import BookForm from './BookForm.vue'

export default {
  components: { SearchBar, ThePagination, BookForm },
  setup() {
    const appStore = useAppStore()
    const bookStore = useBookStore()
    return { appStore, bookStore }
  },
  data() {
    return {
      showModal: false,
      selectedBook: null,
      fields: [
        { key: 'id', label: 'ID' },
        { key: 'title', label: 'Titre' },
        { key: 'authorFirstName', label: 'Prénom auteur' },
        { key: 'authorLastName', label: 'Nom auteur' },
        { key: 'rating', label: 'Note' },
        { key: 'isPublic', label: 'Public' },
        { key: 'publishDate', label: 'Date de publication' },
        { key: 'publisher', label: 'Éditeur' },
        { key: 'isbn', label: 'ISBN' },
        { key: 'actions', label: 'Actions' }
      ]
    }
  },
  computed: {
    errors() {
      return this.appStore.errors
    }
  },
  async mounted() {
    const result = await this.bookStore.fetchBooks()
    if (!result.success) {
      this.appStore.setErrors(result.error)
    }
  },
  methods: {
    handleSearch(search) {
      this.bookStore.setSearch(search)
      this.fetchBooks()
    },
    async fetchBooks() {
      const result = await this.bookStore.fetchBooks()
      if (!result.success) {
        this.appStore.setErrors(result.error)
      }
    },
    showCreateModal() {
      this.selectedBook = null
      this.showModal = true
    },
    editBook(book) {
      this.selectedBook = { ...book }
      this.showModal = true
    },
    async saveBook(book) {
      const result = this.selectedBook
        ? await this.bookStore.updateBook(this.selectedBook.id, book)
        : await this.bookStore.createBook(book)
      if (result.success) {
        this.closeModal()
      } else {
        this.appStore.setErrors(result.error)
      }
    },
    async deleteBook(id) {
      if (confirm('Confirmer la suppression ?')) {
        const result = await this.bookStore.deleteBook(id)
        if (!result.success) {
          this.appStore.setErrors(result.error)
        }
      }
    },
    closeModal() {
      this.showModal = false
      this.selectedBook = null
      this.appStore.clearErrors()
    }
  }
}
</script>