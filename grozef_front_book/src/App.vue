<!-- src/App.vue -->
<template>
  <div>
    <b-navbar toggleable="lg" type="dark" variant="primary">
      <b-navbar-brand to="/">Media Manager</b-navbar-brand>
      <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>
      <b-collapse id="nav-collapse" is-nav>
        <b-navbar-nav>
          <b-nav-item to="/videos" v-if="authStore.isAuthenticated">Videos</b-nav-item>
          <b-nav-item to="/publishers" v-if="authStore.isAuthenticated">Publishers</b-nav-item>
          <b-nav-item to="/images" v-if="authStore.isAuthenticated">Images</b-nav-item>
          <b-nav-item to="/users" v-if="authStore.isAuthenticated">Users</b-nav-item>
          <b-nav-item to="/books" v-if="authStore.isAuthenticated">Books</b-nav-item>
        </b-navbar-nav>
        <b-navbar-nav class="ms-auto">
          <b-nav-item v-if="authStore.isAuthenticated" @click="logout">Déconnexion</b-nav-item>
          <b-nav-item v-else to="/login">Connexion</b-nav-item>
        </b-navbar-nav>
      </b-collapse>
    </b-navbar>
    <div class="container mt-4">
      <router-view />
    </div>
  </div>
</template>

<script>
import { useAuthStore } from '@/stores/authStore'
import { useRouter } from 'vue-router'

export default {
  name: 'App',
  setup() {
    const authStore = useAuthStore()
    const router = useRouter()
    return { authStore, router }
  },
  methods: {
    logout() {
      this.authStore.logout()
      this.router.push('/login')
    }
  }
}
</script>