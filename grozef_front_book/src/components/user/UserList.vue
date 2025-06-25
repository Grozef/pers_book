<!-- src/components/user/UserList.vue -->
<template>
  <div>
    <h2>Utilisateurs</h2>
    <b-alert v-if="Object.keys(errors).length" variant="danger" show>
      <ul>
        <li v-for="(error, field) in errors" :key="field">{{ field }}: {{ error }}</li>
      </ul>
    </b-alert>
    <SearchBar @search="handleSearch" />
    <b-table striped hover :items="userStore.users" :fields="fields">
      <template #cell(actions)="row">
        <b-button size="sm" @click="editUser(row.item)" variant="primary">Modifier</b-button>
        <b-button size="sm" @click="deleteUser(row.item.id)" variant="danger">Supprimer</b-button>
      </template>
    </b-table>
    <ThePagination
      v-model:currentPage="userStore.pagination.current_page"
      :total-items="userStore.pagination.total_items"
      :items-per-page="userStore.pagination.items_per_page"
    />
    <b-button @click="showCreateModal" variant="success">Ajouter un utilisateur</b-button>
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