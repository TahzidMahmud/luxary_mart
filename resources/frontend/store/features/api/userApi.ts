import { IAuthPayload } from '../../../types/payload';
import { objectToFormData } from '../../../utils/ObjectFormData';
import { apiSlice } from './apiSlice';

const userApi = apiSlice
    .enhanceEndpoints({
        addTagTypes: ['User'],
    })
    .injectEndpoints({
        endpoints: (builder) => ({
            subscribe: builder.mutation<any, string>({
                query: (email) => ({
                    url: '/subscribe',
                    method: 'POST',
                    body: objectToFormData({ email }),
                }),
            }),

            updateProfileInfo: builder.mutation<
                any,
                { name: string; phone: string; avatar?: File }
            >({
                query: (body) => ({
                    url: '/user/update-info',
                    method: 'POST',
                    body: objectToFormData(body),
                }),
            }),

            updatePassword: builder.mutation<
                any,
                { password: string; passwordConfirmation: string }
            >({
                query: (body) => ({
                    url: '/user/update-password',
                    method: 'POST',
                    body: objectToFormData(body),
                }),
            }),

            // forget password apis
            sendValidationCode: builder.mutation<any, IAuthPayload>({
                query: (body) => ({
                    url: '/auth/resend-code',
                    method: 'POST',
                    body: objectToFormData(body),
                }),
            }),

            verifyCode: builder.mutation<any, IAuthPayload>({
                query: (body) => ({
                    url: '/auth/verify/code',
                    method: 'POST',
                    body: objectToFormData(body),
                }),
            }),
            resetPassword: builder.mutation<any, IAuthPayload>({
                query: (body) => ({
                    url: '/auth/password/reset',
                    method: 'POST',
                    body: objectToFormData(body),
                }),
            }),
        }),
    });

export const {
    useSubscribeMutation,
    useUpdateProfileInfoMutation,
    useUpdatePasswordMutation,

    useSendValidationCodeMutation,
    useVerifyCodeMutation,
    useResetPasswordMutation,
} = userApi;
