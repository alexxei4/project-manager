import { createRouter, createWebHistory } from 'vue-router'
import ProjectList from '../components/ProjectList.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: ProjectList
    },
    {
      path: '/projects/:id',
      name: 'project-detail',
      component: () => import('../components/ProjectDetail.vue')
    }
  ]
})

export default router
