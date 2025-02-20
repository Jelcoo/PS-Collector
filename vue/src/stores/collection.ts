import { defineStore } from 'pinia';
import axios, { type PaginatedResponse } from '@/axios';
import { type Collection } from './types';
import type { AxiosResponse } from 'axios';

export const useCollectionStore = defineStore('collection', {
    state: () => ({
        data: [] as Collection[],
        pages: {} as PaginatedResponse,
    }),
    actions: {
        getCollections(withFields: string[], page: number = 1) {
            return new Promise((resolve, reject) => {
                axios
                    .get('/collections', {
                        params: {
                            with: withFields.join(','),
                            page,
                        },
                    })
                    .then((res) => {
                        this.data = res.data.data;
                        this.pages = res.data.pages;
                        resolve(res);
                    })
                    .catch((error) => reject(error));
            });
        },

        getCollection(id: number, withFields: string[] = []): Promise<AxiosResponse<Collection>> {
            return new Promise((resolve, reject) => {
                axios
                    .get(`/collections/${id}`, {
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

        create(name: string, access: string): Promise<AxiosResponse> {
            return new Promise((resolve, reject) => {
                axios
                    .post('/collections/create', {
                        name,
                        access,
                    })
                    .then((res) => {
                        resolve(res);
                    })
                    .catch((error) => reject(error));
            });
        },

        update(id: number, name: string): Promise<AxiosResponse> {
            return new Promise((resolve, reject) => {
                axios
                    .post(`/collections/${id}/update`, {
                        name,
                    })
                    .then((res) => {
                        resolve(res);
                    })
                    .catch((error) => reject(error));
            });
        },

        delete(id: number) {
            return new Promise((resolve, reject) => {
                axios
                    .post(`/collections/${id}/delete`)
                    .then((res) => {
                        resolve(res);
                    })
                    .catch((error) => reject(error));
            });
        },

        addMember(id: number, username: string) {
            return new Promise((resolve, reject) => {
                axios
                    .post(`/collections/${id}/add-member`, {
                        username,
                    })
                    .then((res) => {
                        resolve(res);
                    })
                    .catch((error) => reject(error));
            });
        },
    },
});
