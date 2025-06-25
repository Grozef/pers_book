<!-- src/components/video/VideoList.vue -->
<template>
  <div>
    <h2>Vidéos</h2>

    <div v-if="Object.keys(errors).length" class="alert alert-danger">
      <ul>
        <li v-for="(error, field) in errors" :key="field">{{ field }}: {{ error }}</li>
      </ul>
    </div>

    <SearchBar @search="handleSearch" />

    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th v-for="field in fields" :key="field.key">{{ field.label }}</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="video in videoStore.videos" :key="video.id">
          <td>{{ video.id }}</td>
          <td>{{ video.title }}</td>
          <td>{{ video.authorFirstName }}</td>
          <td>{{ video.authorLastName }}</td>
          <td>{{ video.rating }}</td>
          <td>{{ video.isPublic ? 'Oui' : 'Non' }}</td>
          <td>{{ video.publishDate }}</td>
          <td>{{ video.publisher }}</td>
          <td>
            <button class="btn btn-sm btn-primary" @click="editVideo(video)">Modifier</button>
            <button class="btn btn-sm btn-danger" @click="deleteVideo(video.id)">Supprimer</button>
          </td>
        </tr>
      </tbody>
    </table>

    <ThePagination
      v-model:currentPage="videoStore.pagination.current_page"
      :total-items="videoStore.pagination.total_items"
      :items-per-page="videoStore.pagination.items_per_page"
    />

    <button class="btn btn-success" @click="showCreateModal">Ajouter une vidéo</button>

    <VideoForm
      v-if="showModal"
      :video="selectedVideo"
      @save="saveVideo"
      @close="closeModal"
    />
  </div>
</template>

<script>
import { useAppStore } from '@/stores/appStore'
import { useVideoStore } from '@/stores/videoStore'
import SearchBar from '@/components/common/SearchBar.vue'
import ThePagination from '@/components/common/ThePagination.vue'
import VideoForm from './VideoForm.vue'

export default {
  components: { SearchBar, ThePagination, VideoForm },
  setup() {
    const appStore = useAppStore()
    const videoStore = useVideoStore()
    return { appStore, videoStore }
  },
  data() {
    return {
      showModal: false,
      selectedVideo: null,
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
    const result = await this.videoStore.fetchVideos()
    if (!result.success) {
      this.appStore.setErrors(result.error)
    }
  },
  methods: {
    handleSearch(search) {
      this.videoStore.setSearch(search)
      this.fetchVideos()
    },
    async fetchVideos() {
      const result = await this.videoStore.fetchVideos()
      if (!result.success) {
        this.appStore.setErrors(result.error)
      }
    },
    showCreateModal() {
      this.selectedVideo = null
      this.showModal = true
    },
    editVideo(video) {
      this.selectedVideo = { ...video }
      this.showModal = true
    },
    async saveVideo(video) {
      const result = this.selectedVideo
        ? await this.videoStore.updateVideo(this.selectedVideo.id, video)
        : await this.videoStore.createVideo(video)
      if (result.success) {
        this.closeModal()
      } else {
        this.appStore.setErrors(result.error)
      }
    },
    async deleteVideo(id) {
      if (confirm('Confirmer la suppression ?')) {
        const result = await this.videoStore.deleteVideo(id)
        if (!result.success) {
          this.appStore.setErrors(result.error)
        }
      }
    },
    closeModal() {
      this.showModal = false
      this.selectedVideo = null
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

.table th,
.table td {
  padding: 0.5rem;
  border: 1px solid #ddd;
  text-align: left;
}

.table-striped tbody tr:nth-child(odd) {
  background-color: #f9f9f9;
}

.table-hover tbody tr:hover {
  background-color: #f1f1f1;
}

.btn {
  padding: 0.25em 0.5em;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.875rem;
  color: white;
  margin-right: 0.3rem;
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
  padding: 0.4em 0.8em;
}

.btn-success:hover {
  background-color: #1c7430;
}

.alert {
  padding: 1rem;
  margin-bottom: 1rem;
  border-radius: 4px;
  color: #721c24;
  background-color: #f8d7da;
  border: 1px solid #f5c6cb;
}
</style>
