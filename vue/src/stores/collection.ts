import { defineStore } from 'pinia';
import axios, { type PaginatedResponse } from '@/axios';
import type { Collection } from './types';

export const useCollectionStore = defineStore('collection', {
    state: () => ({
        collections: [] as Collection[],
        pages: {} as PaginatedResponse,
    }),
    actions: {
        getCollections(withFields: string[]) {
            return new Promise((resolve, reject) => {
                axios
                    .get('/collections', {
                        params: {
                            with: withFields.join(','),
                        },
                    })
                    .then((res) => {
                        this.collections = res.data.data;
                        this.pages = res.data.pages;
                        resolve(res);
                    })
                    .catch((error) => reject(error));
            });
        },
    },
});
