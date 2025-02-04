import { createRouter, createWebHistory } from 'vue-router';
import HomeView from '../views/HomeView.vue';
import NotFoundView from '@/views/NotFoundView.vue';

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'home',
            component: HomeView,
        },
        {
            path: '/auth',
            name: 'auth',
            children: [
                {
                    path: 'login',
                    name: 'auth.login',
                    component: () => import('@/views/auth/LoginView.vue'),
                },
            ],
        },
        {
            path: '/:catchAll(.*)',
            name: '404',
            component: NotFoundView,
        },
    ],
});

export default router;
