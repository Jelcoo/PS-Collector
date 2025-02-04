import { defineStore } from 'pinia';
import axios from '@/axios';

export const useUserStore = defineStore('user', {
    state: () => ({
        id: 0,
        username: '',
        first_name: '',
        last_name: '',
        email: '',
        created_at: '',
        token: '',
    }),
    getters: {
        fullName: (state) => `${state.first_name} ${state.last_name}`,
        isAuthenticated: (state) => !!state.token,
    },
    actions: {
        login(email: string, password: string) {
            return new Promise((resolve, reject) => {
                axios
                    .post('/auth/login', {
                        email,
                        password,
                    })
                    .then((res) => {
                        this.id = res.data.user.id;
                        this.username = res.data.user.username;
                        this.first_name = res.data.user.first_name;
                        this.last_name = res.data.user.last_name;
                        this.email = res.data.user.email;
                        this.created_at = res.data.user.created_at;
                        this.token = res.data.token;

                        localStorage.setItem('token', res.data.token);

                        resolve(this);
                    })
                    .catch((error) => reject(error));
            });
        },
        autoLogin() {
            const token = localStorage.getItem('token');
            if (!token) {
                return;
            }

            axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;

            return new Promise((resolve, reject) => {
                axios
                    .get('/me')
                    .then((res) => {
                        this.id = res.data.id;
                        this.username = res.data.username;
                        this.first_name = res.data.first_name;
                        this.last_name = res.data.last_name;
                        this.email = res.data.email;
                        this.created_at = res.data.created_at;

                        resolve(this);
                    })
                    .catch((error) => reject(error));
            });
        },
    },
});
