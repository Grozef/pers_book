<!-- src/components/user/UserForm.vue -->
<template>
  <div v-if="show" class="modal-overlay" @click.self="close">
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="modal-title">
      <h3 id="modal-title">Formulaire Utilisateur</h3>
      <form @submit.prevent="save">
        <label>
          Email *
          <input type="email" v-model="form.mail" required />
        </label>

        <label>
          Mot de passe <span v-if="!user">(requis si création)</span>
          <input type="password" v-model="form.password" :required="!user" />
        </label>

        <label class="checkbox-label">
          <input type="checkbox" v-model="form.isActive" />
          Actif
        </label>

        <label>
          Prénom *
          <input type="text" v-model="form.firstName" required />
        </label>

        <label>
          Nom *
          <input type="text" v-model="form.lastName" required />
        </label>

        <label>
          Adresse
          <input type="text" v-model="form.address" />
        </label>

        <label>
          Téléphone
          <input type="text" v-model="form.tel" />
        </label>

        <label>
          Code postal
          <input type="text" v-model="form.postalCode" />
        </label>

        <label>
          Pays
          <input type="text" v-model="form.country" />
        </label>

        <div class="buttons">
          <button type="submit" class="btn-primary">Sauvegarder</button>
          <button type="button" @click="close" class="btn-secondary">Annuler</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    user: { type: Object, default: null }
  },
  data() {
    return {
      show: true,
      form: {
        mail: '',
        password: '',
        isActive: false,
        firstName: '',
        lastName: '',
        address: '',
        tel: '',
        postalCode: '',
        country: ''
      }
    }
  },
  emits: ['save', 'close'],
  mounted() {
    if (this.user) {
      this.form = {
        mail: this.user.mail || '',
        password: '',
        isActive: this.user.isActive || false,
        firstName: this.user.userInfo?.firstName || '',
        lastName: this.user.userInfo?.lastName || '',
        address: this.user.userInfo?.address || '',
        tel: this.user.userInfo?.tel || '',
        postalCode: this.user.userInfo?.postalCode || '',
        country: this.user.userInfo?.country || ''
      }
    }
  },
  methods: {
    save() {
      this.$emit('save', this.form)
    },
    close() {
      this.show = false
      this.$emit('close')
    }
  }
}
</script>

<style scoped>
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
  background: white;
  padding: 1.5em 2em;
  border-radius: 5px;
  width: 360px;
  max-width: 90%;
  box-shadow: 0 0 10px rgba(0,0,0,0.3);
}

.modal-content h3 {
  margin-top: 0;
  margin-bottom: 1em;
  text-align: center;
}

form label {
  display: block;
  margin-bottom: 1em;
  font-weight: 600;
  font-size: 0.9em;
}

form input[type="text"],
form input[type="email"],
form input[type="password"] {
  width: 100%;
  padding: 0.4em 0.5em;
  margin-top: 0.3em;
  box-sizing: border-box;
  font-size: 1em;
  border: 1px solid #ccc;
  border-radius: 3px;
}

.checkbox-label {
  display: flex;
  align-items: center;
  gap: 0.5em;
  font-weight: 600;
  margin-bottom: 1em;
}

.buttons {
  display: flex;
  justify-content: space-between;
  margin-top: 1.2em;
}

.btn-primary, .btn-secondary {
  cursor: pointer;
  padding: 0.5em 1.2em;
  font-size: 1em;
  border-radius: 3px;
  border: none;
}

.btn-primary {
  background-color: #007bff;
  color: white;
}

.btn-primary:hover {
  background-color: #0069d9;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background-color: #5a6268;
}
</style>
