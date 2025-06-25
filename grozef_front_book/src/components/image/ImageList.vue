<!-- src/components/image/ImageList.vue -->
<template>
  <div>
    <h2>Images</h2>
    <b-alert v-if="Object.keys(errors).length" variant="danger" show>
      <ul>
        <li v-for="(error, field) in errors" :key="field">{{ field }}: {{ error }}</li>
      </ul>
    </b-alert>
    <SearchBar @search="handleSearch" />
    <b-table striped hover :items="imageStore.images" :fields="fields">
      <template #cell(actions)="row">
        <b-button size="sm" @click="editImage(row.item)" variant="primary">Modifier</b-button>
        <b-button size="sm" @click="deleteImage(row.item.id)" variant="danger">Supprimer</b-button>
      </template>
    </b-table>
    <ThePagination
      v-model:currentPage="imageStore.pagination.current_page"
      :total-items="imageStore.pagination.total_items"
      :items-per-page="imageStore.pagination.items_per_page"
    />
    <b-button @click="showCreateModal" variant="success">Ajouter une image</b-button>
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