import { createRouter, createWebHistory } from 'vue-router'
import CategoryView from './views/CategoryView.vue'
import LoginView from "./views/LoginView.vue";

const routes = [
    {
        path: '/login',
        name: 'login',
        component: LoginView,
    },
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
