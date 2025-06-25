<!-- src/views/LoginView.vue -->
<template>
  <div>
      <h2>Connexion</h2>
      <b-alert v-if="error" variant="danger" show>{{ error }}</b-alert>
      <b-form @submit.prevent="login">
          <b-form-group label="Email">
              <b-form-input v-model="form.email" type="email" required></b-form-input>
          </b-form-group>
          <b-form-group label="Mot de passe">
              <b-form-input v-model="form.password" type="password" required></b-form-input>
          </b-form-group>
          <b-button type="submit" variant="primary">Se connecter</b-button>
      </b-form>
  </div>
</template>

<script>
import { useAuthStore } from '@/stores/authStore'

export default {
  setup() {
      const authStore = useAuthStore()
      return { authStore }
  },
  data() {
      return {
          form: {
              email: '',
              password: ''
          },
          error: null
      }
  },
  methods: {
      async login() {
          const result = await this.authStore.login(this.form)
          if (result.success) {
              this.$router.push('/videos')
          } else {
              this.error = result.error
          }
      }
  }
}
</script>