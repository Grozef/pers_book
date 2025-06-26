<template>
  <div class="container mx-auto p-4">
    <h2 class="text-2xl font-bold mb-4">Livres</h2>

    <div v-if="Object.keys(errors).length" class="alert alert-danger mb-4">
      <ul>
        <li v-for="(error, field) in errors" :key="field">
          {{ field }}: {{ error }}
        </li>
      </ul>
    </div>

    <SearchBar @search="handleSearch" />

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
      <div v-for="book in bookStore.books" :key="book.id" class="bg-white shadow-md rounded-lg overflow-hidden">
        <img
          v-if="book.cover"
          :src="book.cover"
          :alt="`Couverture de ${book.title}`"
          class="w-full h-48 object-cover"
        />
        <div v-else class="w-full h-48 bg-gray-200 flex items-center justify-center">
          <span class="text-gray-500">Aucune couverture</span>
        </div>
        <div class="p-4">
          <h3 class="text-lg font-semibold">{{ book.title }}</h3>
          <p class="text-gray-600">{{ book.authorFirstName }} {{ book.authorLastName }}</p>
          <p class="text-sm text-gray-500">ISBN: {{ book.isbn }}</p>
          <p class="text-sm text-gray-500">Note: {{ book.rating || 'N/A' }}</p>
          <p class="text-sm text-gray-500">Public: {{ book.isPublic ? 'Oui' : 'Non' }}</p>
          <p class="text-sm text-gray-500">Date: {{ book.publishDate || 'N/A' }}</p>
          <p class="text-sm text-gray-500">Éditeur: {{ book.publisher || 'N/A' }}</p>
          <div class="mt-4 flex space-x-2">
            <button
              @click="editBook(book)"
              class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600"
            >
              Modifier
            </button>
            <button
              @click="deleteBook(book.id)"
              class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600"
            >
              Supprimer
            </button>
          </div>
        </div>
      </div>
    </div>

    <ThePagination
      v-model:currentPage="bookStore.pagination.current_page"
      :total-items="bookStore.pagination.total_items"
      :items-per-page="bookStore.pagination.items_per_page"
      class="mt-6"
    />

    <button @click="showCreateModal" class="create-btn bg-green-500 text-white px-4 py-2 rounded mt-6 hover:bg-green-600">
      Ajouter un livre
    </button>

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
import BookForm from '@/components/book/BookForm.vue'

const appStore = useAppStore()
const bookStore = useBookStore()

const showModal = ref(false)
const selectedBook = ref(null)

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
</style>