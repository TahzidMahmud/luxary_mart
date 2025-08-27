import {
    IAddress,
    IArea,
    ICity,
    ICountry,
    IState,
} from '../../../types/checkout';
import { IAddressPayload } from '../../../types/payload';
import { objectToFormData } from '../../../utils/ObjectFormData';
import { apiSlice } from '../api/apiSlice';

const checkoutApi = apiSlice
    .enhanceEndpoints({
        addTagTypes: [
            'Address',
            'Country',
            'State',
            'City',
            'Area',
            'ShippingCharges',
        ],
    })
    .injectEndpoints({
        endpoints: (builder) => ({
            // manage addresses (add edit update delete || get country, state, area)
            getAddresses: builder.query<
                { addresses: IAddress[]; countries: ICountry[] },
                void
            >({
                query: () => '/user/addresses',
                providesTags: ['Address'],
                transformResponse: (res: any) => res.result,
            }),

            addAddress: builder.mutation<IAddress, Partial<IAddressPayload>>({
                query: (address) => ({
                    url: '/user/address',
                    method: 'POST',
                    body: objectToFormData(address),
                }),
                invalidatesTags: ['Address'],
                transformResponse: (res: any) => res.result,
            }),

            updateAddress: builder.mutation<
                IAddress,
                Partial<IAddressPayload> & { id: number }
            >({
                query: (address) => ({
                    url: `/user/address/update`,
                    method: 'POST',
                    body: objectToFormData(address),
                }),
                invalidatesTags: ['Address'],
                transformResponse: (res: any) => res.result,
            }),

            deleteAddress: builder.mutation<
                { message: string },
                { id: number }
            >({
                query: (body) => ({
                    url: `/user/address/delete`,
                    method: 'POST',
                    body: objectToFormData(body),
                }),
                invalidatesTags: ['Address'],
                transformResponse: (res: any) => res.result,
            }),

            getStates: builder.query<IState[], { countryId: number }>({
                query: ({ countryId }) => `/get-states/${countryId}`,
                providesTags: (_res, _err, { countryId }) => [
                    { type: 'State', id: countryId },
                ],
                transformResponse: (res: any) => res.result,
            }),

            getCities: builder.query<ICity[], { stateId: number }>({
                query: ({ stateId }) => `/get-cities/${stateId}`,
                providesTags: (_res, _err, { stateId }) => [
                    { type: 'City', id: stateId },
                ],
                transformResponse: (res: any) => res.result,
            }),

            getAreas: builder.query<IArea[], { cityId: number }>({
                query: ({ cityId }) => `/get-areas/${cityId}`,
                providesTags: (_res, _err, { cityId }) => [
                    { type: 'Area', id: cityId },
                ],
                transformResponse: (res: any) => res.result,
            }),

            getShippingCharge: builder.query<
                number,
                {
                    addressId: number;
                    shopIds: number[];
                    coupons: string[];
                }
            >({
                query: (body) => {
                    return {
                        url: `checkout/get-shipping-charge`,
                        method: 'POST',
                        body: objectToFormData(body),
                    };
                },
                providesTags: ['ShippingCharges'],
                transformResponse: (res: any) => res.result,
            }),
        }),
    });

export const {
    useAddAddressMutation,
    useDeleteAddressMutation,
    useGetAddressesQuery,

    useLazyGetAddressesQuery,
    useLazyGetAreasQuery,
    useLazyGetCitiesQuery,
    useLazyGetShippingChargeQuery,
    useLazyGetStatesQuery,
    useUpdateAddressMutation,
} = checkoutApi;
