<template>
  <div v-if="show" class="modal-overlay" @click.self="close">
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
      <h3 id="modalTitle">Formulaire Image</h3>
      <form @submit.prevent="save">
        <label>
          Titre
          <input v-model="form.title" required />
        </label>

        <label>
          Prénom de l'auteur
          <input v-model="form.authorFirstName" required />
        </label>

        <label>
          Nom de l'auteur
          <input v-model="form.authorLastName" required />
        </label>

        <label>
          Note (1-5)
          <input v-model.number="form.rating" type="number" min="1" max="5" />
        </label>

        <label>
          Public
          <input type="checkbox" v-model="form.isPublic" />
        </label>

        <label>
          Date de publication
          <input v-model="form.publishDate" type="date" />
        </label>

        <label>
          Éditeur
          <input v-model="form.publisher" />
        </label>

        <label>
          Chemin du fichier
          <input v-model="form.filepath" />
        </label>

        <label>
          Éditeurs associés
          <select v-model="form.publisherIds" multiple>
            <option v-for="p in publishers" :key="p.value" :value="p.value">{{ p.text }}</option>
          </select>
        </label>

        <div class="buttons">
          <button type="submit">Sauvegarder</button>
          <button type="button" @click="close">Annuler</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { usePublisherStore } from '@/stores/publisherStore'

export default {
  props: {
    image: { type: Object, default: null }
  },
  setup(props, { emit }) {
    const show = ref(true)
    const publisherStore = usePublisherStore()
    const form = ref({
      title: '',
      authorFirstName: '',
      authorLastName: '',
      rating: null,
      isPublic: false,
      publishDate: '',
      publisher: '',
      filepath: '',
      publisherIds: []
    })
    const publishers = ref([])

    onMounted(async () => {
      if (props.image) {
        Object.assign(form.value, props.image, {
          publisherIds: props.image.publisherIds || []
        })
      }
      const result = await publisherStore.fetchPublishers(100)
      if (result.success) {
        publishers.value = publisherStore.publishers.map(p => ({ value: p.id, text: p.name }))
      }
    })

    function save() {
      emit('save', form.value)
    }
    function close() {
      show.value = false
      emit('close')
    }

    return { show, form, publishers, save, close }
  }
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0,0,0,0.4);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}
.modal-content {
  background: white;
  padding: 1.5rem;
  border-radius: 8px;
  max-width: 500px;
  width: 100%;
  max-height: 90vh;
  overflow-y: auto;
  box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}
label {
  display: flex;
  flex-direction: column;
  margin-bottom: 1rem;
  font-weight: 600;
}
input, select {
  margin-top: 0.3rem;
  padding: 0.4rem 0.5rem;
  font-size: 1rem;
}
.buttons {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
}
button {
  padding: 0.5rem 1rem;
  font-weight: 600;
  cursor: pointer;
  border: 1px solid #3b82f6;
  background: #3b82f6;
  color: white;
  border-radius: 4px;
  transition: background-color 0.2s ease;
}
button[type="button"] {
  background: #ccc;
  border-color: #aaa;
  color: #333;
}
button:hover {
  background-color: #2563eb;
}
button[type="button"]:hover {
  background-color: #999;
}
</style>
