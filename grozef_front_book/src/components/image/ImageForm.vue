<!-- src/components/image/ImageForm.vue -->
<template>
  <b-modal v-model="show" title="Formulaire Image" @hide="close">
    <b-form @submit.prevent="save">
      <b-form-group label="Titre">
        <b-form-input v-model="form.title" required></b-form-input>
      </b-form-group>
      <b-form-group label="Prénom de l'auteur">
        <b-form-input v-model="form.authorFirstName" required></b-form-input>
      </b-form-group>
      <b-form-group label="Nom de l'auteur">
        <b-form-input v-model="form.authorLastName" required></b-form-input>
      </b-form-group>
      <b-form-group label="Note (1-5)">
        <b-form-input v-model="form.rating" type="number" min="1" max="5"></b-form-input>
      </b-form-group>
      <b-form-group label="Public">
        <b-form-checkbox v-model="form.isPublic"></b-form-checkbox>
      </b-form-group>
      <b-form-group label="Date de publication">
        <b-form-input v-model="form.publishDate" type="date"></b-form-input>
      </b-form-group>
      <b-form-group label="Éditeur">
        <b-form-input v-model="form.publisher"></b-form-input>
      </b-form-group>
      <b-form-group label="Chemin du fichier">
        <b-form-input v-model="form.filepath"></b-form-input>
      </b-form-group>
      <b-form-group label="Éditeurs associés">
        <b-form-select v-model="form.publisherIds" multiple :options="publishers"></b-form-select>
      </b-form-group>
      <b-button type="submit" variant="primary">Sauvegarder</b-button>
      <b-button @click="close" variant="secondary">Annuler</b-button>
    </b-form>
  </b-modal>
</template>

<script>
import { usePublisherStore } from '@/stores/publisherStore'

export default {
  props: {
    image: { type: Object, default: null }
  },
  setup() {
    const publisherStore = usePublisherStore()
    return { publisherStore }
  },
  data() {
    return {
      show: true,
      form: {
        title: '',
        authorFirstName: '',
        authorLastName: '',
        rating: null,
        isPublic: false,
        publishDate: '',
        publisher: '',
        filepath: '',
        publisherIds: []
      },
      publishers: []
    }
  },
  emits: ['save', 'close'],
  async mounted() {
    if (this.image) {
      this.form = { ...this.image, publisherIds: this.image.publisherIds || [] }
    }
    await this.fetchPublishers()
  },
  methods: {
    async fetchPublishers() {
      const result = await this.publisherStore.fetchPublishers(100)
      if (result.success) {
        this.publishers = this.publisherStore.publishers.map(p => ({ value: p.id, text: p.name }))
      }
    },
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