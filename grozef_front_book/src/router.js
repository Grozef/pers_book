// src/router.js
import { createRouter, createWebHistory } from 'vue-router'
import VideoView from './views/VideoView.vue'
import PublisherView from './views/PublisherView.vue'
import ImageView from './views/ImageView.vue'
import UserView from './views/UserView.vue'
import BookView from './views/BookView.vue'
// import LoginView from './views/LoginView.vue'
import { useAuthStore } from '@/stores/authStore'

const routes = [
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/LoginView.vue')
},
  {
    path: '/videos',
    name: 'Videos',
    component: VideoView,
    meta: { requiresAuth: true }
  },
  {
    path: '/publishers',
    name: 'Publishers',
    component: PublisherView,
    meta: { requiresAuth: true }
  },
  {
    path: '/images',
    name: 'Images',
    component: ImageView,
    meta: { requiresAuth: true }
  },
  {
    path: '/users',
    name: 'Users',
    component: UserView,
    meta: { requiresAuth: true }
  },
  {
    path: '/books',
    name: 'Books',
    component: BookView,
    meta: { requiresAuth: true }
  },
  { path: '/', redirect: '/videos' }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const authStore = useAuthStore()
  authStore.initializeAuth()
  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    next({ name: 'Login' })
  } else if (to.name === 'Login' && authStore.isAuthenticated) {
    next({ name: 'Videos' })
  } else {
    next()
  }
})

export default router