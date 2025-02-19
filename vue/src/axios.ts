import axios from 'axios';

const instance = axios.create({
    baseURL: import.meta.env.VITE_APP_URL + '/api',
});

export interface PaginatedResponse {
    totalPages: number;
    currentPage: number;
    previousPages: number[];
    nextPages: number[];
    totalRecords: number;
    recordsPerPage: number;
    recordsThisPage: number;
}

export interface GenericMessageResponse {
    message: string;
}

export default instance;
