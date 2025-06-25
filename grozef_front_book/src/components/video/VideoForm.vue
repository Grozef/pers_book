<!-- src/components/video/VideoForm.vue -->
<template>
  <div v-if="show" class="modal-overlay" @click.self="close">
    <div class="modal">
      <h3>Formulaire Vidéo</h3>
      <form @submit.prevent="save" novalidate>
        <label>
          Titre
          <input type="text" v-model="form.title" required />
        </label>

        <label>
          Prénom de l'auteur
          <input type="text" v-model="form.authorFirstName" required />
        </label>

        <label>
          Nom de l'auteur
          <input type="text" v-model="form.authorLastName" required />
        </label>

        <label>
          Note (1-5)
          <input type="number" v-model.number="form.rating" min="1" max="5" />
        </label>

        <label class="checkbox-label">
          <input type="checkbox" v-model="form.isPublic" />
          Public
        </label>

        <label>
          Date de publication
          <input type="date" v-model="form.publishDate" />
        </label>

        <label>
          Éditeur
          <input type="text" v-model="form.publisher" />
        </label>

        <label>
          Chemin du fichier
          <input type="text" v-model="form.filepath" />
        </label>

        <label>
          Éditeurs associés
          <select v-model="form.publisherIds" multiple size="5">
            <option
              v-for="publisher in publishers"
              :key="publisher.value"
              :value="publisher.value"
            >
              {{ publisher.text }}
            </option>
          </select>
        </label>

        <div class="buttons">
          <button type="submit" class="btn btn-primary">Sauvegarder</button>
          <button type="button" class="btn btn-secondary" @click="close">Annuler</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { usePublisherStore } from '@/stores/publisherStore'

export default {
  props: {
    video: { type: Object, default: null }
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
    if (this.video) {
      this.form = { ...this.video, publisherIds: this.video.publisherIds || [] }
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

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.4);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.modal {
  background: white;
  padding: 1.5em;
  border-radius: 6px;
  width: 400px;
  max-width: 90%;
  box-shadow: 0 2px 10px rgba(0,0,0,0.3);
}

form {
  display: flex;
  flex-direction: column;
}

label {
  margin-bottom: 1em;
  font-weight: 600;
  display: flex;
  flex-direction: column;
  font-size: 0.9em;
}

input[type="text"],
input[type="number"],
input[type="date"],
select {
  margin-top: 0.3em;
  padding: 0.4em 0.6em;
  font-size: 1em;
  border: 1px solid #ccc;
  border-radius: 3px;
}

.checkbox-label {
  flex-direction: row;
  align-items: center;
  font-weight: 400;
}

.checkbox-label input[type="checkbox"] {
  margin-right: 0.5em;
}

.buttons {
  display: flex;
  justify-content: flex-end;
  gap: 0.5em;
  margin-top: 1.2em;
}

.btn {
  cursor: pointer;
  border: none;
  padding: 0.5em 1em;
  font-size: 1em;
  border-radius: 4px;
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
  background-color: #565e64;
}
</style>
