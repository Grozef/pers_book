<template>
  <div>
    <h2>Livres</h2>

    <div v-if="Object.keys(errors).length" class="alert alert-danger">
      <ul>
        <li v-for="(error, field) in errors" :key="field">
          {{ field }}: {{ error }}
        </li>
      </ul>
    </div>

    <SearchBar @search="handleSearch" />

    <table class="book-table">
      <thead>
        <tr>
          <th v-for="field in fields" :key="field.key">{{ field.label }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="book in bookStore.books" :key="book.id">
          <td>{{ book.id }}</td>
          <td>{{ book.title }}</td>
          <td>{{ book.authorFirstName }}</td>
          <td>{{ book.authorLastName }}</td>
          <td>{{ book.rating }}</td>
          <td>{{ book.isPublic ? 'Oui' : 'Non' }}</td>
          <td>{{ book.publishDate }}</td>
          <td>{{ book.publisher }}</td>
          <td>{{ book.isbn }}</td>
          <td>
            <button @click="editBook(book)">Modifier</button>
            <button @click="deleteBook(book.id)">Supprimer</button>
          </td>
        </tr>
      </tbody>
    </table>

    <ThePagination
      v-model:currentPage="bookStore.pagination.current_page"
      :total-items="bookStore.pagination.total_items"
      :items-per-page="bookStore.pagination.items_per_page"
    />

    <button @click="showCreateModal" class="create-btn">Ajouter un livre</button>

    <BookForm
      v-if="showModal"
      :book="selectedBook"
      @save="saveBook"
      @close="closeModal"
    />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAppStore } from '@/stores/appStore'
import { useBookStore } from '@/stores/bookStore'
import SearchBar from '@/components/common/SearchBar.vue'
import ThePagination from '@/components/common/ThePagination.vue'
import BookForm from './BookForm.vue'

const appStore = useAppStore()
const bookStore = useBookStore()

const showModal = ref(false)
const selectedBook = ref(null)

const fields = [
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

const errors = computed(() => appStore.errors)

const handleSearch = async (search) => {
  bookStore.setSearch(search)
  await fetchBooks()
}

const fetchBooks = async () => {
  const result = await bookStore.fetchBooks()
  if (!result.success) {
    appStore.setErrors(result.error)
  }
}

const showCreateModal = () => {
  selectedBook.value = null
  showModal.value = true
}

const editBook = (book) => {
  selectedBook.value = { ...book }
  showModal.value = true
}

const saveBook = async (book) => {
  const result = selectedBook.value
    ? await bookStore.updateBook(selectedBook.value.id, book)
    : await bookStore.createBook(book)

  if (result.success) {
    closeModal()
  } else {
    appStore.setErrors(result.error)
  }
}

const deleteBook = async (id) => {
  if (confirm('Confirmer la suppression ?')) {
    const result = await bookStore.deleteBook(id)
    if (!result.success) {
      appStore.setErrors(result.error)
    }
  }
}

const closeModal = () => {
  showModal.value = false
  selectedBook.value = null
  appStore.clearErrors()
}

onMounted(fetchBooks)
</script>

<style scoped>
.alert {
  background-color: #f8d7da;
  border: 1px solid #f5c2c7;
  padding: 1rem;
  margin-bottom: 1rem;
  color: #842029;
  border-radius: 4px;
}
.book-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
}
.book-table th,
.book-table td {
  padding: 0.5rem;
  border: 1px solid #ddd;
}
.book-table th {
  background-color: #f2f2f2;
}
button {
  margin-right: 0.5rem;
  padding: 0.4rem 0.8rem;
}
.create-btn {
  margin-top: 1rem;
}
</style>
