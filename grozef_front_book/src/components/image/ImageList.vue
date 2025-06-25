<template>
  <div>
    <h2>Images</h2>

    <div v-if="Object.keys(errors).length" class="alert alert-danger">
      <ul>
        <li v-for="(error, field) in errors" :key="field">{{ field }}: {{ error }}</li>
      </ul>
    </div>

    <SearchBar @search="handleSearch" />

    <table class="table">
      <thead>
        <tr>
          <th v-for="field in fields" :key="field.key">{{ field.label }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="image in imageStore.images" :key="image.id">
          <td>{{ image.id }}</td>
          <td>{{ image.title }}</td>
          <td>{{ image.authorFirstName }}</td>
          <td>{{ image.authorLastName }}</td>
          <td>{{ image.rating }}</td>
          <td>{{ image.isPublic ? 'Oui' : 'Non' }}</td>
          <td>{{ image.publishDate }}</td>
          <td>{{ image.publisher }}</td>
          <td>
            <button class="btn btn-primary btn-sm" @click="editImage(image)">Modifier</button>
            <button class="btn btn-danger btn-sm" @click="deleteImage(image.id)">Supprimer</button>
          </td>
        </tr>
      </tbody>
    </table>

    <ThePagination
      v-model:currentPage="imageStore.pagination.current_page"
      :total-items="imageStore.pagination.total_items"
      :items-per-page="imageStore.pagination.items_per_page"
    />

    <button class="btn btn-success" @click="showCreateModal">Ajouter une image</button>

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

<style scoped>
.table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 1rem;
}
.table th, .table td {
  border: 1px solid #ddd;
  padding: 0.5rem;
  text-align: left;
}
.table th {
  background-color: #f4f4f4;
}
.btn {
  cursor: pointer;
  border: none;
  border-radius: 4px;
  padding: 0.3rem 0.6rem;
  font-size: 0.875rem;
  margin-right: 0.3rem;
  color: white;
}
.btn-sm {
  font-size: 0.75rem;
  padding: 0.2rem 0.4rem;
}
.btn-primary {
  background-color: #007bff;
}
.btn-primary:hover {
  background-color: #0056b3;
}
.btn-danger {
  background-color: #dc3545;
}
.btn-danger:hover {
  background-color: #a71d2a;
}
.btn-success {
  background-color: #28a745;
  margin-top: 1rem;
}
.btn-success:hover {
  background-color: #1e7e34;
}
.alert {
  padding: 0.75rem 1rem;
  border-radius: 4px;
  margin-bottom: 1rem;
}
.alert-danger {
  background-color: #f8d7da;
  color: #721c24;
}
</style>
