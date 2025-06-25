// src/stores/appStore.js
import { defineStore } from 'pinia'

export const useAppStore = defineStore('app', {
  state: () => ({
    errors: {}
  }),
  actions: {
    setErrors(errors) {
      this.errors = errors
    },
    clearErrors() {
      this.errors = {}
    }
  }
})