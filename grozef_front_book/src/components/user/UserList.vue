<!-- src/components/user/UserList.vue -->
<template>
  <div>
    <h2>Utilisateurs</h2>

    <div v-if="Object.keys(errors).length" class="alert-danger">
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
        <tr v-for="user in userStore.users" :key="user.id">
          <td>{{ user.id }}</td>
          <td>{{ user.mail }}</td>
          <td>{{ user.isActive ? 'Oui' : 'Non' }}</td>
          <td>{{ user.userInfo?.firstName || '' }}</td>
          <td>{{ user.userInfo?.lastName || '' }}</td>
          <td>
            <button class="btn btn-primary btn-sm" @click="editUser(user)">Modifier</button>
            <button class="btn btn-danger btn-sm" @click="deleteUser(user.id)">Supprimer</button>
          </td>
        </tr>
      </tbody>
    </table>

    <ThePagination
      v-model:currentPage="userStore.pagination.current_page"
      :total-items="userStore.pagination.total_items"
      :items-per-page="userStore.pagination.items_per_page"
    />

    <button class="btn btn-success" @click="showCreateModal">Ajouter un utilisateur</button>

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
.table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 1em;
}

.table th,
.table td {
  border: 1px solid #ddd;
  padding: 0.5em 0.75em;
  text-align: left;
}

.table th {
  background-color: #f8f9fa;
}

.btn {
  cursor: pointer;
  border: none;
  padding: 0.35em 0.7em;
  font-size: 0.9em;
  border-radius: 3px;
  margin-right: 0.3em;
  color: white;
}

.btn-sm {
  padding: 0.25em 0.5em;
  font-size: 0.8em;
}

.btn-primary {
  background-color: #007bff;
}

.btn-primary:hover {
  background-color: #0069d9;
}

.btn-danger {
  background-color: #dc3545;
}

.btn-danger:hover {
  background-color: #bd2130;
}

.btn-success {
  background-color: #28a745;
  margin-top: 1em;
}

.btn-success:hover {
  background-color: #218838;
}

.alert-danger {
  background-color: #f8d7da;
  border: 1px solid #f5c2c7;
  color: #842029;
  padding: 0.75em 1em;
  margin-bottom: 1em;
  border-radius: 3px;
}
</style>
