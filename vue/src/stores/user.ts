import { defineStore } from 'pinia';
import axios, { type GenericMessageResponse } from '@/axios';
import type { AxiosResponse } from 'axios';
import { useCollectionStore } from './collection';

export const useUserStore = defineStore('user', {
    state: () => ({
        id: 0,
        username: '',
        first_name: '',
        last_name: '',
        email: '',
        created_at: '',
        token: localStorage.getItem('token') || '',
    }),
    getters: {
        fullName: (state) => `${state.first_name} ${state.last_name}`,
        isAuthenticated: (state) => !!state.token,
    },
    actions: {
        register(
            username: string,
            first_name: string,
            last_name: string,
            email: string,
            password: string,
            turnstileToken: string,
        ) {
            return new Promise((resolve, reject) => {
                axios
                    .post('/auth/register', {
                        username,
                        first_name,
                        last_name,
                        email,
                        password,
                        'cf-turnstile-response': turnstileToken,
                    })
                    .then((res) => {
                        this.resetStores();
                        this.setUserResponse(res);
                        this.token = res.data.token;
                        localStorage.setItem('token', res.data.token);
                        axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.token;
                        resolve(res);
                    })
                    .catch((error) => reject(error));
            });
        },
        login(email: string, password: string, turnstileToken: string) {
            return new Promise((resolve, reject) => {
                axios
                    .post('/auth/login', {
                        email,
                        password,
                        'cf-turnstile-response': turnstileToken,
                    })
                    .then((res) => {
                        this.resetStores();
                        this.setUserResponse(res);
                        this.token = res.data.token;
                        localStorage.setItem('token', res.data.token);
                        axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.token;
                        resolve(res);
                    })
                    .catch((error) => reject(error));
            });
        },
        autoLogin() {
            if (!this.token) {
                return;
            }

            axios.defaults.headers.common['Authorization'] = 'Bearer ' + this.token;

            return new Promise((resolve, reject) => {
                axios
                    .get('/me')
                    .then((res) => {
                        this.resetStores();
                        this.setUserResponse(res);
                        resolve(res);
                    })
                    .catch((error) => {
                        if (error.response.status === 401) {
                            this.logout();
                        }
                        reject(error);
                    });
            });
        },
        resetPassword(
            token: string | undefined,
            email: string,
            password: string,
            turnstileToken: string,
        ): Promise<AxiosResponse<GenericMessageResponse>> {
            return new Promise((resolve, reject) => {
                axios
                    .post('/auth/reset-password', {
                        token,
                        email,
                        password,
                        'cf-turnstile-response': turnstileToken,
                    })
                    .then((res) => {
                        resolve(res);
                    })
                    .catch((error) => reject(error));
            });
        },
        setUserResponse(res: AxiosResponse) {
            this.id = res.data.user.id;
            this.username = res.data.user.username;
            this.first_name = res.data.user.first_name;
            this.last_name = res.data.user.last_name;
            this.email = res.data.user.email;
            this.created_at = res.data.user.created_at;
        },
        logout() {
            localStorage.removeItem('token');
            axios.defaults.headers.common['Authorization'] = '';
            this.resetStores();
        },
        resetStores() {
            this.$reset();
            useCollectionStore().$reset();
        },
    },
});
