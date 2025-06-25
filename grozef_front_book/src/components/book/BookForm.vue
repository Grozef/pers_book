<!-- src/components/book/BookForm.vue -->
<template>
  <div class="modal-backdrop" v-if="show">
    <div class="modal">
      <h2>Formulaire Livre</h2>
      <form @submit.prevent="save">
        <label>
          Titre :
          <input v-model="form.title" required />
        </label>

        <label>
          Prénom de l'auteur :
          <input v-model="form.authorFirstName" required />
        </label>

        <label>
          Nom de l'auteur :
          <input v-model="form.authorLastName" required />
        </label>

        <label>
          Note (1-5) :
          <input v-model="form.rating" type="number" min="1" max="5" />
        </label>

        <label>
          Public :
          <input v-model="form.isPublic" type="checkbox" />
        </label>

        <label>
          Date de publication :
          <input v-model="form.publishDate" type="date" />
        </label>

        <label>
          Éditeur :
          <input v-model="form.publisher" />
        </label>

        <label>
          ISBN :
          <input v-model="form.isbn" />
        </label>

        <label>
          Éditeurs associés :
          <select v-model="form.publisherIds" multiple>
            <option v-for="p in publishers" :key="p.value" :value="p.value">
              {{ p.text }}
            </option>
          </select>
        </label>

        <div class="actions">
          <button type="submit">Sauvegarder</button>
          <button type="button" @click="close">Annuler</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted/*, watch */} from 'vue'
import { usePublisherStore } from '@/stores/publisherStore'
import { useAttrs, defineEmits } from 'vue'

const emits = defineEmits(['save', 'close'])
const attrs = useAttrs()
const show = ref(true)
const publisherStore = usePublisherStore()

const form = reactive({
  title: '',
  authorFirstName: '',
  authorLastName: '',
  rating: null,
  isPublic: false,
  publishDate: '',
  publisher: '',
  isbn: '',
  publisherIds: []
})

const publishers = ref([])

const fetchPublishers = async () => {
  const result = await publisherStore.fetchPublishers(100)
  if (result.success) {
    publishers.value = publisherStore.publishers.map(p => ({
      value: p.id,
      text: p.name
    }))
  }
}

const close = () => {
  show.value = false
  emits('close')
}

const save = () => {
  emits('save', { ...form })
}

onMounted(async () => {
  if (attrs.book) {
    Object.assign(form, {
      ...attrs.book,
      publisherIds: attrs.book.publisherIds || []
    })
  }
  await fetchPublishers()
})
</script>

<style scoped>
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}
.modal {
  background: white;
  padding: 2rem;
  border-radius: 10px;
  width: 500px;
  max-width: 90%;
}
label {
  display: block;
  margin-bottom: 1rem;
}
input,
select {
  width: 100%;
  padding: 0.5rem;
  margin-top: 0.25rem;
  box-sizing: border-box;
}
.actions {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  margin-top: 1.5rem;
}
button {
  padding: 0.5rem 1rem;
  cursor: pointer;
}
</style>
