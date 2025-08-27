import axios from 'axios';
import { STORAGE_KEYS } from '../types';
import { ILocallyStoredUserAddress } from '../types/checkout';
import { API_URL } from './env';

const userLocation = JSON.parse(
    localStorage.getItem(STORAGE_KEYS.USER_LOCATION_KEY) || 'null',
) as ILocallyStoredUserAddress | null;
const zoneId = userLocation?.area?.zone_id;

const localAxios = axios.create({
    baseURL: API_URL,
    headers: {
        'Content-Type': 'multipart/form-data',
        ZoneId: zoneId,
        'Accept-Language': localStorage.getItem('i18nextLng'),
    },
});

// Set the authorization token in the header if available
const token = localStorage.getItem(STORAGE_KEYS.AUTH_KEY);
if (token) {
    localAxios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
}

export default localAxios;
