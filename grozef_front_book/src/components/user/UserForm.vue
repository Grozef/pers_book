<!-- src/components/user/UserForm.vue -->
<template>
  <b-modal v-model="show" title="Formulaire Utilisateur" @hide="close">
    <b-form @submit.prevent="save">
      <b-form-group label="Email">
        <b-form-input v-model="form.mail" type="email" required></b-form-input>
      </b-form-group>
      <b-form-group label="Mot de passe">
        <b-form-input v-model="form.password" type="password" :required="!user"></b-form-input>
      </b-form-group>
      <b-form-group label="Actif">
        <b-form-checkbox v-model="form.isActive"></b-form-checkbox>
      </b-form-group>
      <b-form-group label="Prénom">
        <b-form-input v-model="form.firstName" required></b-form-input>
      </b-form-group>
      <b-form-group label="Nom">
        <b-form-input v-model="form.lastName" required></b-form-input>
      </b-form-group>
      <b-form-group label="Adresse">
        <b-form-input v-model="form.address"></b-form-input>
      </b-form-group>
      <b-form-group label="Téléphone">
        <b-form-input v-model="form.tel"></b-form-input>
      </b-form-group>
      <b-form-group label="Code postal">
        <b-form-input v-model="form.postalCode"></b-form-input>
      </b-form-group>
      <b-form-group label="Pays">
        <b-form-input v-model="form.country"></b-form-input>
      </b-form-group>
      <b-button type="submit" variant="primary">Sauvegarder</b-button>
      <b-button @click="close" variant="secondary">Annuler</b-button>
    </b-form>
  </b-modal>
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
        ...this.user,
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