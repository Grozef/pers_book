<!-- src/components/image/ImageList.vue -->
<template>
  <div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Images</h2>

    <div v-if="Object.keys(errors).length" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
      <ul>
        <li v-for="(error, field) in errors" :key="field">{{ field }}: {{ error }}</li>
      </ul>
    </div>

    <SearchBar @search="handleSearch" />

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div v-for="image in imageStore.images" :key="image.id" class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4">
          <h3 class="text-lg font-semibold mb-2">{{ image.title }}</h3>
          <p class="text-gray-600 mb-1">
            <span class="font-medium">Auteur:</span> {{ image.authorFirstName }} {{ image.authorLastName }}
          </p>
          <p class="text-gray-600 mb-1">
            <span class="font-medium">Note:</span> {{ image.rating }}/5
          </p>
          <p class="text-gray-600 mb-1">
            <span class="font-medium">Public:</span> {{ image.isPublic ? 'Oui' : 'Non' }}
          </p>
          <p class="text-gray-600 mb-1">
            <span class="font-medium">Date:</span> {{ image.publishDate }}
          </p>
          <p class="text-gray-600 mb-4">
            <span class="font-medium">Éditeur:</span> {{ image.publisher }}
          </p>
          <div class="flex gap-2">
            <button
              class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm transition-colors"
              @click="editImage(image)"
            >
              Modifier
            </button>
            <button
              class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm transition-colors"
              @click="deleteImage(image.id)"
            >
              Supprimer
            </button>
          </div>
        </div>
      </div>
    </div>

    <ThePagination
      v-model:currentPage="imageStore.pagination.current_page"
      :total-items="imageStore.pagination.total_items"
      :items-per-page="imageStore.pagination.items_per_page"
      class="mt-6"
    />

    <button
      class="mt-6 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded transition-colors"
      @click="showCreateModal"
    >
      Ajouter une image
    </button>

    <ImageForm
      v-if="showModal"
      :image="selectedImage"
      @save="saveImage"
      @close="closeModal"
    />
  </div>
</template>

<script>
import { useAppStore } from '@/stores/appStore'
import { useImageStore } from '@/stores/imageStore'
import SearchBar from '@/components/common/SearchBar.vue'
import ThePagination from '@/components/common/ThePagination.vue'
import ImageForm from './ImageForm.vue'

export default {
  components: { SearchBar, ThePagination, ImageForm },
  setup() {
    const appStore = useAppStore()
    const imageStore = useImageStore()
    return { appStore, imageStore }
  },
  data() {
    return {
      showModal: false,
      selectedImage: null,
      fields: [
        { key: 'id', label: 'ID' },
        { key: 'title', label: 'Titre' },
        { key: 'authorFirstName', label: 'Prénom auteur' },
        { key: 'authorLastName', label: 'Nom auteur' },
        { key: 'rating', label: 'Note' },
        { key: 'isPublic', label: 'Public' },
        { key: 'publishDate', label: 'Date de publication' },
        { key: 'publisher', label: 'Éditeur' },
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
    const result = await this.imageStore.fetchImages()
    if (!result.success) {
      this.appStore.setErrors(result.error)
    }
  },
  methods: {
    handleSearch(search) {
      this.imageStore.setSearch(search)
      this.fetchImages()
    },
    async fetchImages() {
      const result = await this.imageStore.fetchImages()
      if (!result.success) {
        this.appStore.setErrors(result.error)
      }
    },
    showCreateModal() {
      this.selectedImage = null
      this.showModal = true
    },
    editImage(image) {
      this.selectedImage = { ...image }
      this.showModal = true
    },
    async saveImage(image) {
      const result = this.selectedImage
        ? await this.imageStore.updateImage(this.selectedImage.id, image)
        : await this.imageStore.createImage(image)
      if (result.success) {
        this.closeModal()
      } else {
        this.appStore.setErrors(result.error)
      }
    },
    async deleteImage(id) {
      if (confirm('Confirmer la suppression ?')) {
        const result = await this.imageStore.deleteImage(id)
        if (!result.success) {
          this.appStore.setErrors(result.error)
        }
      }
    },
    closeModal() {
      this.showModal = false
      this.selectedImage = null
      this.appStore.clearErrors()
    }
  }
}
</script>