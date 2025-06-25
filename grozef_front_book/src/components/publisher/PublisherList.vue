<!-- src/components/publisher/PublisherList.vue -->
<template>
  <div>
    <h2>Éditeurs</h2>
    <b-alert v-if="Object.keys(errors).length" variant="danger" show>
      <ul>
        <li v-for="(error, field) in errors" :key="field">{{ field }}: {{ error }}</li>
      </ul>
    </b-alert>
    <SearchBar @search="handleSearch" />
    <b-table striped hover :items="publisherStore.publishers" :fields="fields">
      <template #cell(actions)="row">
        <b-button size="sm" @click="editPublisher(row.item)" variant="primary">Modifier</b-button>
        <b-button size="sm" @click="deletePublisher(row.item.id)" variant="danger">Supprimer</b-button>
      </template>
    </b-table>
    <ThePagination
      v-model:currentPage="publisherStore.pagination.current_page"
      :total-items="publisherStore.pagination.total_items"
      :items-per-page="publisherStore.pagination.items_per_page"
    />
    <b-button @click="showCreateModal" variant="success">Ajouter un éditeur</b-button>
    <PublisherForm
      v-if="showModal"
      :publisher="selectedPublisher"
      @save="savePublisher"
      @close="closeModal"
    />
  </div>
</template>

<script>
import { useAppStore } from '@/stores/appStore'
import { usePublisherStore } from '@/stores/publisherStore'
import SearchBar from '@/components/common/SearchBar.vue'
import ThePagination from '@/components/common/ThePagination.vue'
import PublisherForm from './PublisherForm.vue'

export default {
  components: { SearchBar, ThePagination, PublisherForm },
  setup() {
    const appStore = useAppStore()
    const publisherStore = usePublisherStore()
    return { appStore, publisherStore }
  },
  data() {
    return {
      showModal: false,
      selectedPublisher: null,
      fields: [
        { key: 'id', label: 'ID' },
        { key: 'name', label: 'Nom' },
        { key: 'address', label: 'Adresse' },
        { key: 'mail', label: 'Email' },
        { key: 'country', label: 'Pays' },
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
    const result = await this.publisherStore.fetchPublishers()
    if (!result.success) {
      this.appStore.setErrors(result.error)
    }
  },
  methods: {
    handleSearch(search) {
      this.publisherStore.setSearch(search)
      this.fetchPublishers()
    },
    async fetchPublishers() {
      const result = await this.publisherStore.fetchPublishers()
      if (!result.success) {
        this.appStore.setErrors(result.error)
      }
    },
    showCreateModal() {
      this.selectedPublisher = null
      this.showModal = true
    },
    editPublisher(publisher) {
      this.selectedPublisher = { ...publisher }
      this.showModal = true
    },
    async savePublisher(publisher) {
      const result = this.selectedPublisher
        ? await this.publisherStore.updatePublisher(this.selectedPublisher.id, publisher)
        : await this.publisherStore.createPublisher(publisher)
      if (result.success) {
        this.closeModal()
      } else {
        this.appStore.setErrors(result.error)
      }
    },
    async deletePublisher(id) {
      if (confirm('Confirmer la suppression ?')) {
        const result = await this.publisherStore.deletePublisher(id)
        if (!result.success) {
          this.appStore.setErrors(result.error)
        }
      }
    },
    closeModal() {
      this.showModal = false
      this.selectedPublisher = null
      this.appStore.clearErrors()
    }
  }
}
</script>