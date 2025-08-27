import axios from 'axios';
import { setupCache } from 'axios-cache-interceptor';

declare global {
    interface Window {
        backendApiUrl: string;
    }
}

export const axiosInstance = setupCache(
    axios.create({
        baseURL: window.backendApiUrl,
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        },
    }),
    {
        cacheTakeover: false, // don't send request if cache is available
    },
);
