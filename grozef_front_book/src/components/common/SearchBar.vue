<!-- src/components/common/SearchBar.vue -->
<template>
  <b-form-input
    v-model="searchTerm"
    placeholder="Rechercher..."
    @input="onSearch"
    debounce="300"
  ></b-form-input>
</template>

<script>
export default {
  data() {
    return {
      searchTerm: ''
    }
  },
  emits: ['search'],
  methods: {
    onSearch() {
      this.$emit('search', this.searchTerm)
    }
  }
}
</script>