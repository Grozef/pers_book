<!-- src/components/publisher/PublisherList.vue -->
<template>
  <div class="p-6 max-w-7xl mx-auto">
    <h2 class="text-2xl font-bold mb-4">Éditeurs</h2>

    <div v-if="hasErrors" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
      <ul>
        <li v-for="(error, field) in errors" :key="field">{{ field }} : {{ error }}</li>
      </ul>
    </div>

    <input
      type="text"
      placeholder="Rechercher..."
      v-model="searchTerm"
      @input="handleSearch"
      class="w-full p-2 mb-4 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500"
    />

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div v-for="publisher in publisherStore.publishers" :key="publisher.id" class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4">
          <h3 class="text-lg font-semibold mb-2">{{ publisher.name }}</h3>
          <p class="text-gray-600 mb-1">
            <span class="font-medium">Adresse:</span> {{ publisher.address }}
          </p>
          <p class="text-gray-600 mb-1">
            <span class="font-medium">Email:</span> {{ publisher.email }}
          </p>
          <p class="text-gray-600 mb-1">
            <span class="font-medium">Pays:</span> {{ publisher.country }}
          </p>
          <p class="text-gray-600 mb-1">
            <span class="font-medium">Téléphone:</span> {{ publisher.tel }}
          </p>
          <p class="text-gray-600 mb-1">
            <span class="font-medium">Code postal:</span> {{ publisher.postalCode }}
          </p>
          <div class="text-gray-600 mb-4">
            <span class="font-medium">Vidéos:</span>
            <span v-if="publisher.videos?.length">
              <router-link
                v-for="videoId in publisher.videos"
                :key="videoId"
                :to="`/videos/${videoId}`"
                class="text-blue-500 hover:underline mx-1"
              >
                {{ videoId }}
              </router-link>
            </span>
            <span v-else class="italic text-gray-500">Aucune</span>
          </div>
          <div class="text-gray-600 mb-4">
            <span class="font-medium">Images:</span>
            <span v-if="publisher.images?.length">
              <router-link
                v-for="imageId in publisher.images"
                :key="imageId"
                :to="`/images/${imageId}`"
                class="text-blue-500 hover:underline mx-1"
              >
                {{ imageId }}
              </router-link>
            </span>
            <span v-else class="italic text-gray-500">Aucune</span>
          </div>
          <div class="text-gray-600 mb-4">
            <span class="font-medium">Livres:</span>
            <span v-if="publisher.books?.length">
              <router-link
                v-for="bookId in publisher.books"
                :key="bookId"
                :to="`/books/${bookId}`"
                class="text-blue-500 hover:underline mx-1"
              >
                {{ bookId }}
              </router-link>
            </span>
            <span v-else class="italic text-gray-500">Aucune</span>
          </div>
          <div class="flex gap-2">
            <button
              class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm transition-colors"
              @click="editPublisher(publisher)"
            >
              Modifier
            </button>
            <button
              class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm transition-colors"
              @click="deletePublisher(publisher.id)"
            >
              Supprimer
            </button>
          </div>
        </div>
      </div>
      <div v-if="publisherStore.publishers.length === 0" class="col-span-full text-center text-gray-500 italic">
        Aucun éditeur trouvé.
      </div>
    </div>

    <div class="flex justify-center items-center gap-4 mt-6">
      <button
        :disabled="publisherStore.pagination.current_page === 1"
        @click="changePage(publisherStore.pagination.current_page - 1)"
        class="px-4 py-2 bg-gray-200 text-gray-700 rounded disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Précédent
      </button>
      <span class="text-gray-700">Page {{ publisherStore.pagination.current_page }} / {{ publisherStore.pagination.total_pages }}</span>
      <button
        :disabled="publisherStore.pagination.current_page === publisherStore.pagination.total_pages"
        @click="changePage(publisherStore.pagination.current_page + 1)"
        class="px-4 py-2 bg-gray-200 text-gray-700 rounded disabled:opacity-50 disabled:cursor-not-allowed"
      >
        Suivant
      </button>
    </div>

    <button
      class="mt-6 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded transition-colors"
      @click="showCreateModal"
    >
      Ajouter un éditeur
    </button>

    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-40 flex justify-center items-center z-[1000]" @click.self="closeModal">
      <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md max-h-[90vh] overflow-y-auto">
        <h3 class="text-lg font-semibold mb-4 text-center">Formulaire Éditeur</h3>
        <form @submit.prevent="savePublisher">
          <label class="block mb-3 font-semibold">
            Nom
            <input type="text" v-model="form.name" required class="w-full p-2 mt-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
          </label>
          <label class="block mb-3 font-semibold">
            Adresse
            <input type="text" v-model="form.address" required class="w-full p-2 mt-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
          </label>
          <label class="block mb-3 font-semibold">
            Email
            <input type="email" v-model="form.mail" required class="w-full p-2 mt-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
          </label>
          <label class="block mb-3 font-semibold">
            Pays
            <input type="text" v-model="form.country" required class="w-full p-2 mt-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
          </label>
          <label class="block mb-3 font-semibold">
            Téléphone
            <input type="text" v-model="form.tel" class="w-full p-2 mt-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
          </label>
          <label class="block mb-3 font-semibold">
            Code postal
            <input type="text" v-model="form.postalCode" class="w-full p-2 mt-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500" />
          </label>
          <div class="flex justify-between mt-4">
            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded transition-colors">
              Sauvegarder
            </button>
            <button type="button" @click="closeModal" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded transition-colors">
              Annuler
            </button>
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
import { useRouter } from 'vue-router' // Ajout pour gérer les liens

export default {
  setup() {
    const appStore = useAppStore()
    const publisherStore = usePublisherStore()
    const router = useRouter()

    const searchTerm = ref('')
    const showModal = ref(false)
    const selectedPublisher = ref(null)
    const form = reactive({
      name: '',
      address: '',
      mail: '',
      country: '',
      tel: '',
      postalCode: ''
    })

    // Chargement initial
    const fetchPublishers = async () => {
      const result = await publisherStore.fetchPublishers(publisherStore.pagination.items_per_page)
      if (!result.success) {
        appStore.setErrors(result.error)
      }
    }

    onMounted(fetchPublishers)

    // Recherche
    watch(searchTerm, async (newSearch) => {
      publisherStore.setSearch(newSearch)
      publisherStore.setPage(1)
      await fetchPublishers()
    })

    // Gestion erreurs affichage
    const errors = computed(() => appStore.errors)
    const hasErrors = computed(() => Object.keys(errors.value).length > 0)

    // Modale gestion
    function showCreateModal() {
      selectedPublisher.value = null
      Object.assign(form, { name: '', address: '', mail: '', country: '', tel: '', postalCode: '' })
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
          // to avoid the error delete that shit when fixed
          console.log(router)
          appStore.setErrors(result.error)
        }
      }
    }

    async function changePage(page) {
      publisherStore.setPage(page)
      await fetchPublishers()
    }

    return {
      searchTerm,
      publisherStore,
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
</style>