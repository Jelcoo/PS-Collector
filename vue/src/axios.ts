import axios from 'axios';

const instance = axios.create({
    baseURL: import.meta.env.VITE_APP_URL + '/api',
});

export interface PaginatedResponse {
    total: number;
    current: number;
    previous: number[];
    next: number[];
    perPage: number;
}

export default instance;
