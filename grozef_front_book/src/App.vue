<!-- src/App.vue -->
<template>
  <div>
    <nav class="navbar">
      <router-link class="navbar-brand" to="/">Media Manager</router-link>
      <button class="navbar-toggler" @click="toggleNav">
        ☰
      </button>
      <div v-show="navOpen" class="navbar-collapse">
        <ul class="navbar-nav">
          <li v-if="authStore.isAuthenticated"><router-link to="/videos">Videos</router-link></li>
          <li v-if="authStore.isAuthenticated"><router-link to="/publishers">Publishers</router-link></li>
          <li v-if="authStore.isAuthenticated"><router-link to="/images">Images</router-link></li>
          <li v-if="authStore.isAuthenticated"><router-link to="/users">Users</router-link></li>
          <li v-if="authStore.isAuthenticated"><router-link to="/books">Books</router-link></li>
        </ul>
        <ul class="navbar-nav navbar-nav-right">
          <li v-if="authStore.isAuthenticated"><a href="#" @click.prevent="logout">Déconnexion</a></li>
          <li v-else><router-link to="/login">Connexion</router-link></li>
        </ul>
      </div>
    </nav>
    <main class="container">
      <router-view />
    </main>
  </div>
</template>

<script>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/authStore'
import { useRouter } from 'vue-router'

export default {
  setup() {
    const authStore = useAuthStore()
    const router = useRouter()
    const navOpen = ref(false)

    function toggleNav() {
      navOpen.value = !navOpen.value
    }

    function logout() {
      authStore.logout()
      router.push('/login')
    }

    return { authStore, router, navOpen, toggleNav, logout }
  }
}
</script>

<style scoped>
.navbar {
  display: flex;
  align-items: center;
  background-color: #007bff;
  padding: 0.5rem 1rem;
  color: white;
  flex-wrap: wrap;
}
.navbar-brand {
  font-weight: bold;
  font-size: 1.25rem;
  color: white;
  text-decoration: none;
  margin-right: auto;
}
.navbar-toggler {
  font-size: 1.5rem;
  background: none;
  border: none;
  color: white;
  cursor: pointer;
}
.navbar-collapse {
  width: 100%;
  display: flex;
  flex-wrap: wrap;
  margin-top: 0.5rem;
}
.navbar-nav {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-wrap: wrap;
}
.navbar-nav li {
  margin-right: 1rem;
}
.navbar-nav a,
.navbar-nav router-link {
  color: white;
  text-decoration: none;
}
.navbar-nav-right {
  margin-left: auto;
}
.container {
  margin-top: 1rem;
  padding: 1rem;
}
</style>
