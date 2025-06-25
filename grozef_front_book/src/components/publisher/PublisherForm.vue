<!-- src/components/publisher/PublisherForm.vue -->
<template>
  <b-modal v-model="show" title="Formulaire Éditeur" @hide="close">
    <b-form @submit.prevent="save">
      <b-form-group label="Nom">
        <b-form-input v-model="form.name" required></b-form-input>
      </b-form-group>
      <b-form-group label="Adresse">
        <b-form-input v-model="form.address" required></b-form-input>
      </b-form-group>
      <b-form-group label="Email">
        <b-form-input v-model="form.mail" type="email" required></b-form-input>
      </b-form-group>
      <b-form-group label="Pays">
        <b-form-input v-model="form.country" required></b-form-input>
      </b-form-group>
      <b-button type="submit" variant="primary">Sauvegarder</b-button>
      <b-button @click="close" variant="secondary">Annuler</b-button>
    </b-form>
  </b-modal>
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