<!-- src/components/publisher/PublisherList.vue -->
<template>
  <div class="publisher-list">
    <h2>Éditeurs</h2>

    <div v-if="hasErrors" class="alert alert-danger">
      <ul>
        <li v-for="(error, field) in errors" :key="field">{{ field }} : {{ error }}</li>
      </ul>
    </div>

    <input
      type="text"
      placeholder="Rechercher..."
      v-model="searchTerm"
      @input="handleSearch"
      class="search-input"
    />

    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Nom</th>
          <th>Adresse</th>
          <th>Email</th>
          <th>Pays</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="publisher in paginatedPublishers" :key="publisher.id">
          <td>{{ publisher.id }}</td>
          <td>{{ publisher.name }}</td>
          <td>{{ publisher.address }}</td>
          <td>{{ publisher.mail }}</td>
          <td>{{ publisher.country }}</td>
          <td>
            <button @click="editPublisher(publisher)">Modifier</button>
            <button @click="deletePublisher(publisher.id)">Supprimer</button>
          </td>
        </tr>
        <tr v-if="paginatedPublishers.length === 0">
          <td colspan="6" class="text-center">Aucun éditeur trouvé.</td>
        </tr>
      </tbody>
    </table>

    <div class="pagination">
      <button
        :disabled="currentPage === 1"
        @click="changePage(currentPage - 1)"
      >
        Précédent
      </button>
      <span>Page {{ currentPage }} / {{ totalPages }}</span>
      <button
        :disabled="currentPage === totalPages"
        @click="changePage(currentPage + 1)"
      >
        Suivant
      </button>
    </div>

    <button class="btn-add" @click="showCreateModal">Ajouter un éditeur</button>

    <!-- Modal simple -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal-content">
        <h3>Formulaire Éditeur</h3>
        <form @submit.prevent="savePublisher">
          <label>
            Nom
            <input type="text" v-model="form.name" required />
          </label>

          <label>
            Adresse
            <input type="text" v-model="form.address" required />
          </label>

          <label>
            Email
            <input type="email" v-model="form.mail" required />
          </label>

          <label>
            Pays
            <input type="text" v-model="form.country" required />
          </label>

          <div class="modal-actions">
            <button type="submit">Sauvegarder</button>
            <button type="button" @click="closeModal">Annuler</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, reactive, computed, watch, onMounted } from 'vue'
import { useAppStore } from '@/stores/appStore'
import { usePublisherStore } from '@/stores/publisherStore'

export default {
  setup() {
    const appStore = useAppStore()
    const publisherStore = usePublisherStore()

    const searchTerm = ref('')
    const currentPage = ref(1)
    const itemsPerPage = 10

    const showModal = ref(false)
    const selectedPublisher = ref(null)
    const form = reactive({
      name: '',
      address: '',
      mail: '',
      country: ''
    })

    // Chargement initial
    const fetchPublishers = async () => {
      const result = await publisherStore.fetchPublishers()
      if (!result.success) {
        appStore.setErrors(result.error)
      }
    }

    onMounted(fetchPublishers)

    // Recherche
    watch(searchTerm, async (newSearch) => {
      publisherStore.setSearch(newSearch)
      currentPage.value = 1
      await fetchPublishers()
    })

    // Pagination calculée
    const totalItems = computed(() => publisherStore.publishers.length)
    const totalPages = computed(() =>
      Math.ceil(totalItems.value / itemsPerPage)
    )
    const paginatedPublishers = computed(() => {
      const start = (currentPage.value - 1) * itemsPerPage
      return publisherStore.publishers.slice(start, start + itemsPerPage)
    })

    function changePage(page) {
      if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page
      }
    }

    // Gestion erreurs affichage
    const errors = computed(() => appStore.errors)
    const hasErrors = computed(() => Object.keys(errors.value).length > 0)

    // Modale gestion
    function showCreateModal() {
      selectedPublisher.value = null
      Object.assign(form, { name: '', address: '', mail: '', country: '' })
      showModal.value = true
      appStore.clearErrors()
    }

    function editPublisher(publisher) {
      selectedPublisher.value = publisher
      Object.assign(form, publisher)
      showModal.value = true
      appStore.clearErrors()
    }

    function closeModal() {
      showModal.value = false
      selectedPublisher.value = null
      appStore.clearErrors()
    }

    async function savePublisher() {
      const payload = { ...form }
      let result
      if (selectedPublisher.value) {
        result = await publisherStore.updatePublisher(selectedPublisher.value.id, payload)
      } else {
        result = await publisherStore.createPublisher(payload)
      }
      if (result.success) {
        await fetchPublishers()
        closeModal()
      } else {
        appStore.setErrors(result.error)
      }
    }

    async function deletePublisher(id) {
      if (confirm('Confirmer la suppression ?')) {
        const result = await publisherStore.deletePublisher(id)
        if (result.success) {
          await fetchPublishers()
        } else {
          appStore.setErrors(result.error)
        }
      }
    }

    return {
      searchTerm,
      currentPage,
      totalPages,
      paginatedPublishers,
      errors,
      hasErrors,
      showModal,
      form,
      showCreateModal,
      editPublisher,
      closeModal,
      savePublisher,
      deletePublisher,
      changePage
    }
  }
}
</script>

<style scoped>
.publisher-list {
  max-width: 900px;
  margin: auto;
  padding: 1em;
}

.alert {
  background-color: #f8d7da;
  color: #721c24;
  padding: 1em;
  margin-bottom: 1em;
  border-radius: 4px;
}

.search-input {
  width: 100%;
  padding: 0.5em;
  margin-bottom: 1em;
  font-size: 1em;
  box-sizing: border-box;
}

.table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 1em;
}

.table th,
.table td {
  border: 1px solid #ccc;
  padding: 0.5em;
  text-align: left;
}

.table th {
  background-color: #eee;
}

.text-center {
  text-align: center;
  font-style: italic;
  color: #777;
}

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1em;
  margin-bottom: 1em;
}

.pagination button {
  padding: 0.5em 1em;
  cursor: pointer;
}

.pagination button:disabled {
  cursor: default;
  opacity: 0.5;
}

.btn-add {
  display: block;
  margin: 0 auto;
  padding: 0.7em 1.2em;
  font-size: 1em;
  cursor: pointer;
  background-color: #28a745;
  border: none;
  color: white;
  border-radius: 4px;
}

.btn-add:hover {
  background-color: #218838;
}

/* Modal simple */
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.4);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-content {
  background-color: white;
  padding: 1.5em;
  border-radius: 5px;
  width: 320px;
  box-shadow: 0 0 10px rgba(0,0,0,0.25);
}

.modal-content h3 {
  margin-top: 0;
  margin-bottom: 1em;
  font-size: 1.25em;
  text-align: center;
}

.modal-content form label {
  display: block;
  margin-bottom: 0.75em;
  font-weight: 600;
}

.modal-content form input {
  width: 100%;
  padding: 0.4em;
  margin-top: 0.25em;
  box-sizing: border-box;
}

.modal-actions {
  display: flex;
  justify-content: space-between;
  margin-top: 1em;
}

.modal-actions button {
  padding: 0.5em 1em;
  cursor: pointer;
  border-radius: 3px;
  border: none;
}

.modal-actions button[type="submit"] {
  background-color: #007bff;
  color: white;
}

.modal-actions button[type="submit"]:hover {
  background-color: #0069d9;
}

.modal-actions button[type="button"] {
  background-color: #6c757d;
  color: white;
}

.modal-actions button[type="button"]:hover {
  background-color: #5a6268;
}
</style>
