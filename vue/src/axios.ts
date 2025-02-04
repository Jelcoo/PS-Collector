import axios from 'axios';

const instance = axios.create({
    baseURL: import.meta.env.VITE_APP_URL + '/api',
});

export default instance;
