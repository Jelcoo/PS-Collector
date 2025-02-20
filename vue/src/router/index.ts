import { createRouter, createWebHistory } from 'vue-router';
import { useUserStore } from '@/stores/user';

const router = createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: [
        {
            path: '/',
            name: 'home',
            component: () => import('@/views/HomeView.vue'),
        },
        {
            path: '/collections',
            children: [
                {
                    path: '',
                    name: 'collections',
                    component: () => import('@/views/collections/CollectionsOverview.vue'),
                },
                {
                    path: 'create',
                    name: 'collections.create',
                    component: () => import('@/views/collections/CollectionCreateView.vue'),
                    meta: { requiresAuth: true },
                },
                {
                    path: ':id(\\d+)',
                    children: [
                        {
                            path: '',
                            name: 'collection',
                            component: () => import('@/views/collections/CollectionView.vue'),
                        },
                        {
                            path: 'edit',
                            name: 'collection.edit',
                            component: () => import('@/views/collections/CollectionEditView.vue'),
                            meta: { requiresAuth: true },
                        },
                    ],
                },
            ],
        },
        {
            path: '/account',
            name: 'account',
            component: () => import('@/views/account/AccountView.vue'),
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
                {
                    path: 'register',
                    name: 'auth.register',
                    component: () => import('@/views/auth/RegisterView.vue'),
                },
                {
                    path: 'password',
                    name: 'auth.password',
                    component: () => import('@/views/auth/PasswordView.vue'),
                },
            ],
        },
        {
            path: '/:catchAll(.*)',
            name: '404',
            component: () => import('@/views/status/NotFoundView.vue'),
        },
    ],
});

router.beforeEach((to, from, next) => {
    const store = useUserStore();
    if (to.meta.requiresAuth && !store.isAuthenticated) {
        next({ name: 'auth.login' });
    } else if (store.isAuthenticated && to.meta.isGuest) {
        next({ name: 'home' });
    } else {
        next();
    }
});

export default router;
