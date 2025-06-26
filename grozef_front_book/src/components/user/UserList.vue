<!-- src/components/user/UserList.vue -->
<template>
  <div class="p-6 max-w-7xl mx-auto">
    <h2 class="text-2xl font-bold mb-4">Utilisateurs</h2>

    <div v-if="Object.keys(errors).length" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
      <ul>
        <li v-for="(error, field) in errors" :key="field">{{ field }}: {{ error }}</li>
      </ul>
    </div>

    <SearchBar @search="handleSearch" />

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
      <div v-for="user in userStore.users" :key="user.id" class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="p-4">
          <h3 class="text-lg font-semibold mb-2">{{ user.userInfo?.firstName || '' }} {{ user.userInfo?.lastName || '' }}</h3>
          <p class="text-gray-600 mb-1">
            <span class="font-medium">Email:</span> {{ user.mail }}
          </p>
          <p class="text-gray-600 mb-4">
            <span class="font-medium">Actif:</span> {{ user.isActive ? 'Oui' : 'Non' }}
          </p>
          <div class="flex gap-2">
            <button
              class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm transition-colors"
              @click="editUser(user)"
            >
              Modifier
            </button>
            <button
              class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm transition-colors"
              @click="deleteUser(user.id)"
            >
              Supprimer
            </button>
          </div>
        </div>
      </div>
      <div v-if="userStore.users.length === 0" class="col-span-full text-center text-gray-500 italic">
        Aucun utilisateur trouvé.
      </div>
    </div>

    <ThePagination
      v-model:currentPage="userStore.pagination.current_page"
      :total-items="userStore.pagination.total_items"
      :items-per-page="userStore.pagination.items_per_page"
      class="mt-6"
    />

    <button
      class="mt-6 bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded transition-colors"
      @click="showCreateModal"
    >
      Ajouter un utilisateur
    </button>

    <UserForm
      v-if="showModal"
      :user="selectedUser"
      @save="saveUser"
      @close="closeModal"
    />
  </div>
</template>

<script>
import { useAppStore } from '@/stores/appStore'
import { useUserStore } from '@/stores/userStore'
import SearchBar from '@/components/common/SearchBar.vue'
import ThePagination from '@/components/common/ThePagination.vue'
import UserForm from './UserForm.vue'

export default {
  components: { SearchBar, ThePagination, UserForm },
  setup() {
    const appStore = useAppStore()
    const userStore = useUserStore()
    return { appStore, userStore }
  },
  data() {
    return {
      showModal: false,
      selectedUser: null,
      fields: [
        { key: 'id', label: 'ID' },
        { key: 'mail', label: 'Email' },
        { key: 'isActive', label: 'Actif' },
        { key: 'userInfo.firstName', label: 'Prénom' },
        { key: 'userInfo.lastName', label: 'Nom' },
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
    const result = await this.userStore.fetchUsers()
    if (!result.success) {
      this.appStore.setErrors(result.error)
    }
  },
  methods: {
    handleSearch(search) {
      this.userStore.setSearch(search)
      this.fetchUsers()
    },
    async fetchUsers() {
      const result = await this.userStore.fetchUsers()
      if (!result.success) {
        this.appStore.setErrors(result.error)
      }
    },
    showCreateModal() {
      this.selectedUser = null
      this.showModal = true
    },
    editUser(user) {
      this.selectedUser = { ...user }
      this.showModal = true
    },
    async saveUser(user) {
      const result = this.selectedUser
        ? await this.userStore.updateUser(this.selectedUser.id, user)
        : await this.userStore.createUser(user)
      if (result.success) {
        this.closeModal()
      } else {
        this.appStore.setErrors(result.error)
      }
    },
    async deleteUser(id) {
      if (confirm('Confirmer la suppression ?')) {
        const result = await this.userStore.deleteUser(id)
        if (!result.success) {
          this.appStore.setErrors(result.error)
        }
      }
    },
    closeModal() {
      this.showModal = false
      this.selectedUser = null
      this.appStore.clearErrors()
    }
  }
}
</script>

<style scoped>
</style>