import { createRouter, createWebHistory } from 'vue-router';
import HomeView from '../views/HomeView.vue';
import NotFoundView from '@/views/NotFoundView.vue';
import { useUserStore } from '@/stores/user';

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'home',
            component: HomeView,
        },
        {
            path: '/account',
            name: 'account',
            component: HomeView,
            meta: { requiresAuth: true },
        },
        {
            path: '/auth',
            name: 'auth',
            meta: { isGuest: true },
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

router.beforeEach((to, from, next) => {
    const store = useUserStore();
    console.log(store.isAuthenticated);
    if (to.meta.requiresAuth && !store.isAuthenticated) {
        next({ name: 'auth.login' });
    } else if (store.isAuthenticated && to.meta.isGuest) {
        next({ name: 'home' });
    } else {
        next();
    }
});

export default router;
