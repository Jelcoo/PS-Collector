import { defineStore } from 'pinia';
import axios from '@/axios';
import type { AxiosResponse } from 'axios';

export const useStampStore = defineStore('stamp', {
    actions: {
        create(collectionId: number, name: string, used: boolean, damaged: boolean): Promise<AxiosResponse> {
            console.log(collectionId, name, used, damaged);
            return new Promise((resolve, reject) => {
                axios
                    .post(`/collections/${collectionId}/stamps/create`, {
                        name,
                        used,
                        damaged,
                    })
                    .then((res) => {
                        resolve(res);
                    })
                    .catch((error) => reject(error));
            });
        },
    },
});
