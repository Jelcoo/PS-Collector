import { defineStore } from 'pinia';
import axios from '@/axios';
import type { AxiosResponse } from 'axios';
import type { Stamp } from './types';

export const useStampStore = defineStore('stamp', {
    actions: {
        getStamp(id: number, withFields: string[] = []): Promise<AxiosResponse<Stamp>> {
            return new Promise((resolve, reject) => {
                axios
                    .get(`/stamps/${id}`, {
                        params: {
                            with: withFields.join(','),
                        },
                    })
                    .then((res) => {
                        resolve(res);
                    })
                    .catch((error) => reject(error));
            });
        },

        create(collectionId: number, name: string, used: boolean, damaged: boolean): Promise<AxiosResponse> {
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
