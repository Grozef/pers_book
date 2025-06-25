<!-- src/components/common/ThePagination.vue -->
<template>
  <b-pagination
    :value="currentPage"
    :total-rows="totalItems"
    :per-page="itemsPerPage"
    @input="onPageChange"
  ></b-pagination>
</template>

<script>
export default {
  name: 'ThePagination',
  props: {
    totalItems: { type: Number, required: true },
    itemsPerPage: { type: Number, required: true },
    currentPage: { type: Number, required: true }
  },
  emits: ['update:currentPage'],
  methods: {
    onPageChange(page) {
      this.$emit('update:currentPage', page)
    }
  }
}
</script>