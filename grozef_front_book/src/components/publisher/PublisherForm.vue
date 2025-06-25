<template>
  <div v-if="show" class="modal-overlay" @click.self="close">
    <div class="modal">
      <header class="modal-header">
        <h3>Formulaire Éditeur</h3>
        <button class="close-btn" @click="close">&times;</button>
      </header>

      <form @submit.prevent="save" class="modal-body">
        <div class="form-group">
          <label for="name">Nom</label>
          <input id="name" type="text" v-model="form.name" required />
        </div>

        <div class="form-group">
          <label for="address">Adresse</label>
          <input id="address" type="text" v-model="form.address" required />
        </div>

        <div class="form-group">
          <label for="mail">Email</label>
          <input id="mail" type="email" v-model="form.mail" required />
        </div>

        <div class="form-group">
          <label for="country">Pays</label>
          <input id="country" type="text" v-model="form.country" required />
        </div>

        <footer class="modal-footer">
          <button type="submit" class="btn btn-primary">Sauvegarder</button>
          <button type="button" class="btn btn-secondary" @click="close">Annuler</button>
        </footer>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    publisher: { type: Object, default: null }
  },
  data() {
    return {
      show: true,
      form: {
        name: '',
        address: '',
        mail: '',
        country: ''
      }
    }
  },
  emits: ['save', 'close'],
  mounted() {
    if (this.publisher) {
      this.form = { ...this.publisher }
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
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}
.modal {
  background: white;
  border-radius: 8px;
  width: 400px;
  max-width: 90%;
  box-shadow: 0 2px 10px rgba(0,0,0,0.3);
  display: flex;
  flex-direction: column;
}
.modal-header {
  padding: 1rem;
  border-bottom: 1px solid #ddd;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.close-btn {
  font-size: 1.5rem;
  border: none;
  background: none;
  cursor: pointer;
  line-height: 1;
}
.modal-body {
  padding: 1rem;
  display: flex;
  flex-direction: column;
}
.form-group {
  margin-bottom: 1rem;
  display: flex;
  flex-direction: column;
}
.form-group label {
  margin-bottom: 0.3rem;
  font-weight: 600;
}
.form-group input {
  padding: 0.4rem 0.6rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 1rem;
}
.modal-footer {
  padding: 1rem;
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
  border-top: 1px solid #ddd;
}
.btn {
  padding: 0.5rem 1rem;
  font-size: 1rem;
  border-radius: 4px;
  border: none;
  cursor: pointer;
  color: white;
}
.btn-primary {
  background-color: #007bff;
}
.btn-primary:hover {
  background-color: #0056b3;
}
.btn-secondary {
  background-color: #6c757d;
}
.btn-secondary:hover {
  background-color: #545b62;
}
</style>
