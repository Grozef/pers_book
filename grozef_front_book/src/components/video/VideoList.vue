<!-- src/components/video/VideoList.vue -->
<template>
  <div class="p-6">
    <h2 class="text-2xl font-bold mb-4">Vidéos</h2>

    <div v-if="Object.keys(errors).length" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
      <ul>
        <li v-for="(error, field) in errors" :key="field">{{ field }}: {{ error }}</li>
      </ul>
    </div>

    <SearchBar @search="handleSearch" />

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div v-for="video in videoStore.videos" :key="video.id" class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4">
          <h3 class="text-lg font-semibold mb-2">{{ video.title }}</h3>
          <p class="text-gray-600 mb-1">
            <span class="font-medium">Auteur:</span> {{ video.authorFirstName }} {{ video.authorLastName }}
          </p>
          <p class="text-gray-600 mb-1">
            <span class="font-medium">Note:</span> {{ video.rating }}/5
          </p>
          <p class="text-gray-600 mb-1">
            <span class="font-medium">Public:</span> {{ video.isPublic ? 'Oui' : 'Non' }}
          </p>
          <p class="text-gray-600 mb-1">
            <span class="font-medium">Date:</span> {{ video.publishDate }}
          </p>
          <p class="text-gray-600 mb-4">
            <span class="font-medium">Éditeur:</span> {{ video.publisher }}
          </p>
          <div class="flex gap-2">
            <button
              class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm transition-colors"
              @click="editVideo(video)"
            >
              Modifier
            </button>
            <button
              class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm transition-colors"
              @click="deleteVideo(video.id)"
            >
              Supprimer
            </button>
          </div>
        </div>
      </div>
    </div>

    <ThePagination
      v-model:currentPage="videoStore.pagination.current_page"
      :total-items="videoStore.pagination.total_items"
      :items-per-page="videoStore.pagination.items_per_page"
      class="mt-6"
    />

    <button
      class="mt-6 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded transition-colors"
      @click="showCreateModal"
    >
      Ajouter une vidéo
    </button>

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