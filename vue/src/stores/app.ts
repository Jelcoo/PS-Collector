import { defineStore } from 'pinia';
import axios from '@/axios';

export const useAppStore = defineStore('app', {
    state: () => ({
        turnstileKey: '',
    }),
    actions: {
        init() {
            return new Promise((resolve, reject) => {
                axios
                    .get('/app')
                    .then((res) => {
                        this.turnstileKey = res.data.turnstile_key;
                        resolve(res);
                    })
                    .catch((error) => reject(error));
            });
        },
    },
});
