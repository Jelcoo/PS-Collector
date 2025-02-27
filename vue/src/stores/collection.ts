import { defineStore } from 'pinia';
import axios, { type MeiliResponse, type PaginatedResponse } from '@/axios';
import { type Collection, type Stamp } from './types';
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

        search(id: number, query: string): Promise<AxiosResponse<MeiliResponse<Stamp>>> {
            return new Promise((resolve, reject) => {
                axios
                    .get(`/collections/${id}/search`, {
                        params: {
                            query,
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
                    .post('/collections', {
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
                    .put(`/collections/${id}`, {
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
                    .delete(`/collections/${id}`)
                    .then((res) => {
                        resolve(res);
                    })
                    .catch((error) => reject(error));
            });
        },

        addMember(id: number, username: string) {
            return new Promise((resolve, reject) => {
                axios
                    .post(`/collections/${id}/members`, {
                        username,
                    })
                    .then((res) => {
                        resolve(res);
                    })
                    .catch((error) => reject(error));
            });
        },

        removeMember(id: number, userId: number) {
            return new Promise((resolve, reject) => {
                axios
                    .delete(`/collections/${id}/members/${userId}`)
                    .then((res) => {
                        resolve(res);
                    })
                    .catch((error) => reject(error));
            });
        },
    },
});
