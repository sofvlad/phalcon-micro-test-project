import { createRouter, createWebHistory } from 'vue-router'
import CategoryView from './views/CategoryView.vue'

const routes = [
    {
        path: '/',
        name: 'all_products',
        component: CategoryView,
    },
    {
        path: '/category/:code',
        name: 'category_products',
        component: CategoryView,
        props: true,
    }
]

const router = createRouter({
    history: createWebHistory(),
    routes
})

export default router
