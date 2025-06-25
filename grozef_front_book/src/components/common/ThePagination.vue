<template>
  <nav class="pagination" aria-label="Pagination">
    <button
      :disabled="currentPage === 1"
      @click="changePage(currentPage - 1)"
      class="page-btn"
    >
      Précédent
    </button>

    <button
      v-for="page in pages"
      :key="page"
      @click="changePage(page)"
      :class="['page-btn', { active: page === currentPage }]"
    >
      {{ page }}
    </button>

    <button
      :disabled="currentPage === totalPages"
      @click="changePage(currentPage + 1)"
      class="page-btn"
    >
      Suivant
    </button>
  </nav>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  totalItems: { type: Number, required: true },
  itemsPerPage: { type: Number, required: true },
  currentPage: { type: Number, required: true }
})

const emit = defineEmits(['update:currentPage'])

const totalPages = computed(() =>
  Math.max(1, Math.ceil(props.totalItems / props.itemsPerPage))
)

const pages = computed(() => {
  const arr = []
  for (let i = 1; i <= totalPages.value; i++) {
    arr.push(i)
  }
  return arr
})

function changePage(page) {
  if (page >= 1 && page <= totalPages.value) {
    emit('update:currentPage', page)
  }
}
</script>

<style scoped>
.pagination {
  display: flex;
  gap: 0.5rem;
  justify-content: center;
  margin: 1rem 0;
}
.page-btn {
  padding: 0.4rem 0.8rem;
  border: 1px solid #ccc;
  background: white;
  cursor: pointer;
  border-radius: 4px;
  user-select: none;
}
.page-btn:disabled {
  cursor: default;
  opacity: 0.5;
}
.page-btn.active {
  background-color: #3b82f6;
  color: white;
  border-color: #3b82f6;
}
</style>
