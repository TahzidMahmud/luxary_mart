import { createApi, fetchBaseQuery } from '@reduxjs/toolkit/query/react';
import { STORAGE_KEYS } from '../../../types';
import { ILocallyStoredUserAddress } from '../../../types/checkout';
import { API_URL } from '../../../utils/env';

export const apiSlice = createApi({
    reducerPath: 'api',
    baseQuery: fetchBaseQuery({
        baseUrl: API_URL,
        prepareHeaders: (headers) => {
            const token = localStorage.getItem(STORAGE_KEYS.AUTH_KEY);

            if (token) {
                headers.set('Authorization', `Bearer ${token}`);
            }
            headers.set('Accept-Language', localStorage.getItem('i18nextLng')!);

            // set Zone-Id header
            // if user location is set
            const userLocation = JSON.parse(
                localStorage.getItem(STORAGE_KEYS.USER_LOCATION_KEY) || 'null',
            ) as ILocallyStoredUserAddress | null;

            if (userLocation?.area?.zone_id) {
                headers.set(
                    'Zone-Id',
                    String(userLocation?.area?.zone_id || null),
                );
            }

            return headers;
        },
    }),
    tagTypes: ['Cart', 'Order'],
    endpoints: (builder) => ({}),
});
