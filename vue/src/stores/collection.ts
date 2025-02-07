import { defineStore } from 'pinia';
import axios, { type PaginatedResponse } from '@/axios';
import type { Collection } from './types';

export const useCollectionStore = defineStore('collection', {
    state: () => ({
        collections: {
            data: [] as Collection[],
            pages: {} as PaginatedResponse,
        },
        currentCollection: {
            data: {} as Collection,
            loading: false,
        },
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
                        this.collections.data = res.data.data;
                        this.collections.pages = res.data.pages;
                        resolve(res);
                    })
                    .catch((error) => reject(error));
            });
        },

        getCollection(id: number, withFields: string[]) {
            this.currentCollection.loading = true;
            return new Promise((resolve, reject) => {
                axios
                    .get(`/collections/${id}`, {
                        params: {
                            with: withFields.join(','),
                        },
                    })
                    .then((res) => {
                        this.currentCollection.data = res.data;
                        this.currentCollection.loading = false;
                        resolve(res);
                    })
                    .catch((error) => reject(error));
            });
        },
    },
});
