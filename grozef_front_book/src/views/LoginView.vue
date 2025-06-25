<!-- src/views/LoginView.vue -->
<template>
  <div class="login-container">
    <h2>Connexion</h2>

    <p v-if="error" class="error">{{ error }}</p>

    <form @submit.prevent="login">
      <label>
        Email :
        <input v-model="form.email" type="email" required />
      </label>

      <label>
        Mot de passe :
        <input v-model="form.password" type="password" required />
      </label>

      <button type="submit">Se connecter</button>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'

const router = useRouter()
const authStore = useAuthStore()

const form = reactive({
  email: '',
  password: ''
})

const error = ref(null)

const login = async () => {
  const result = await authStore.login(form)
  if (result.success) {
    router.push('/videos')
  } else {
    error.value = result.error
  }
}
</script>

<style scoped>
.login-container {
  max-width: 400px;
  margin: 4rem auto;
  padding: 2rem;
  border: 1px solid #ddd;
  border-radius: 8px;
}

label {
  display: block;
  margin-bottom: 1rem;
}

input {
  width: 100%;
  padding: 0.5rem;
  margin-top: 0.25rem;
  box-sizing: border-box;
}

button {
  width: 100%;
  padding: 0.75rem;
  margin-top: 1rem;
  background-color: #333;
  color: white;
  border: none;
  cursor: pointer;
}

button:hover {
  background-color: #555;
}

.error {
  color: red;
  margin-bottom: 1rem;
}
</style>
