import { ReactNode } from 'react';
import { ICountry } from './checkout';

declare global {
    interface Window {
        config: {
            generalSettings: {
                appName: string;
                appMode: 'singleVendor' | 'multiVendor';
                rootUrl: string;
                apiUrl: string;
                demoMode: string;
                logo: string;
            };
            countries: ICountry[];

            currency: {
                code: string;
                symbol: {
                    position:
                        | 'symbol_first'
                        | 'amount_first'
                        | 'symbol_space'
                        | 'amount_space';
                    show: string;
                };
                thousandSeparator: string | null;
                numOfDecimals: number;
                decimalSeparator: string | null;
            };

            defaultLang: ILanguage;

            languages: ILanguage[];
            paymentMethods: IPaymentMethod[];
            socialLogins: {
                name: 'Google' | 'Facebook';
                url: string;
            }[];
        };
    }
}

export interface IPaymentMethod {
    name: string;
    value: string;
}

export interface ILanguage {
    name: string;
    code: string;
    flag: string;
    is_rtl: number;
}

export enum STORAGE_KEYS {
    AUTH_KEY = 'arm-auth-key',
    GUEST_ID_KEY = 'guestUserId',
    AUTH_VERIFICATION_EMAIL_KEY = 'arm-registered-email-key',
    USER_LOCATION_KEY = 'user-location',
}

export type Variant =
    | 'primary'
    | 'secondary'
    | 'success'
    | 'warning'
    | 'danger'
    | 'info'
    | 'light'
    | 'dark'
    | 'link'
    | 'no-color';

export type Size = 'sm' | 'md' | 'lg';

export interface IPaginationMeta {
    current_page: number;
    from: number;
    last_page: number;
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
    path: string;
    per_page: number;
    to: number;
    total: number;
}

export interface IResponse<T = unknown> {
    success: boolean;
    status: number;
    message: string;
    result: {
        data: T;
        meta: IPaginationMeta;
    };
}

export interface IGetsQueryParams {
    query?: string | null;
    page?: string | number | null;
    limit?: string | number | null;
}

export interface URLFilterParams extends IGetsQueryParams {
    categorySlug?: string | null;
    brandSlug?: string | null;
    shopSlug?: string | null;

    sortBy?: string | null;
    couponCode?: string | null;
    minPrice?: string | null;
    maxPrice?: string | null;
    sizeIds?: string[] | null;
    brandIds?: string[] | null;
    categoryIds?: string[] | null;
    variationValueIds?: string[] | null;
    discounted?: boolean;
}

export interface ICategoryShort {
    id: number;
    name: string;
    slug: string;
    thumbnailImage: string;
    children_categories: ICategory[];
    parent_category: ICategory | null;
}

export interface ICategory extends ICategoryShort {
    parent_id: number;
    level: number;
    sorting_order_level: number;
    icon: string;
    total_sale_count: number;
    meta_title: string;
    meta_image: string;
    meta_description: string;
    created_by: number;
    created_at: string;
    updated_at: string;
    updated_by: number;
    deleted_at: string;
    deleted_by: number;
    category_translations: ICategoryTranslation[];
}

export interface ICategoryTranslation {
    id: number;
    category_id: number;
    lang_key: string;
    name: string;
    thumbnailImage: string;
    meta_title: string;
    meta_image: string;
    meta_description: string;
    created_by: number;
    created_at: string;
    updated_at: string;
    updated_by: number;
    deleted_at: string;
    deleted_by: number;
}

export type Impressions = 'positive' | 'negative' | 'neutral';

export interface IPage {
    id: number;
    title: string;
    slug: string;
    content: string;
    metaTile: string;
    metaDescription: string;
    metaKeywords: string;
    metaImage: string;
}

export interface IBreadcrumbNavigation {
    icon?: ReactNode;
    name: string;
    link?: string;
}
