<template>
  <input
    type="text"
    v-model="searchTerm"
    placeholder="Rechercher..."
    @input="onSearchDebounced"
  />
</template>

<script setup>
import { ref } from 'vue'
// import { debounce } from 'lodash-es' // optionnel, sinon on fait un debounce maison

const searchTerm = ref('')

// Émettre un event "search" avec debounce de 300ms

// Sans lodash, on peut coder un debounce simple :
function debounceFn(fn, delay) {
  let timeout
  return function (...args) {
    clearTimeout(timeout)
    timeout = setTimeout(() => fn(...args), delay)
  }
}

const emit = defineEmits(['search'])

const onSearch = () => {
  emit('search', searchTerm.value)
}

const onSearchDebounced = debounceFn(onSearch, 300)
</script>

<style scoped>
input {
  padding: 0.4rem 0.8rem;
  font-size: 1rem;
  width: 100%;
  box-sizing: border-box;
  border: 1px solid #ccc;
  border-radius: 4px;
}
input:focus {
  border-color: #3b82f6;
  outline: none;
  box-shadow: 0 0 3px #3b82f6;
}
</style>
