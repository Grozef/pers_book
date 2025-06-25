<!-- src/components/video/VideoList.vue -->
<template>
  <div>
    <h2>Vidéos</h2>
    <b-alert v-if="Object.keys(errors).length" variant="danger" show>
      <ul>
        <li v-for="(error, field) in errors" :key="field">{{ field }}: {{ error }}</li>
      </ul>
    </b-alert>
    <SearchBar @search="handleSearch" />
    <b-table striped hover :items="videoStore.videos" :fields="fields">
      <template #cell(actions)="row">
        <b-button size="sm" @click="editVideo(row.item)" variant="primary">Modifier</b-button>
        <b-button size="sm" @click="deleteVideo(row.item.id)" variant="danger">Supprimer</b-button>
      </template>
    </b-table>
    <ThePagination
      v-model:currentPage="videoStore.pagination.current_page"
      :total-items="videoStore.pagination.total_items"
      :items-per-page="videoStore.pagination.items_per_page"
    />
    <b-button @click="showCreateModal" variant="success">Ajouter une vidéo</b-button>
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