import { ICategoryShort } from '../../../types';
import {
    BannerSlider,
    IGetAboutUs,
    IGetBannerRow,
    IGetCategoryProducts,
    IGetFeaturedBrands,
    IGetFeaturedCategories,
    IGetFeaturedProducts,
    IGetFeaturedShops,
    IGetFlashSaleProducts,
    IGetFooter,
    IGetFullWidthBanner,
    IGetMainCategories,
    IGetProductSectionOne,
    IGetProductSectionTwo,
    IGetTrendyProducts,
} from '../../../types/home';
import { IProductShort } from '../../../types/product';
import { apiSlice } from './apiSlice';

const userApi = apiSlice.injectEndpoints({
    endpoints: (builder) => ({
        getBannerSliders: builder.query<BannerSlider[], void>({
            query: () => '/homepage/sliders',
            transformResponse: (res: any) => res.result,
        }),

        getVideos: builder.query<string[], void>({
            query: () => '/homepage/videos',
            transformResponse: (res: any) => res.result,
        }),

        getFeaturedCategories: builder.query<IGetFeaturedCategories, void>({
            query: () => '/homepage/featured-categories',
            transformResponse: (res: any) => res.result,
        }),

        getFeaturedProducts: builder.query<IGetFeaturedProducts, void>({
            query: () => '/homepage/featured-products',
            transformResponse: (res: any) => res.result,
        }),

        getFlashSaleProducts: builder.query<IGetFlashSaleProducts, void>({
            query: () => '/homepage/flash-sale-products',
            transformResponse: (res: any) => res.result,
        }),

        getProductSectionOne: builder.query<IGetProductSectionOne, void>({
            query: () => '/homepage/product-section-one',
            transformResponse: (res: any) => res.result,
        }),

        getFullWidthBanner: builder.query<IGetFullWidthBanner[], void>({
            query: () => '/homepage/full-width-banner',
            transformResponse: (res: any) => res.result,
        }),

        getProductSectionTwo: builder.query<IGetProductSectionTwo, void>({
            query: () => '/homepage/product-section-two',
            transformResponse: (res: any) => res.result,
        }),

        getFourBannerRow: builder.query<IGetBannerRow[], void>({
            query: () => '/homepage/four-banners-row',
            transformResponse: (res: any) => res.result,
        }),

        getFeaturedShops: builder.query<IGetFeaturedShops, void>({
            query: () => '/homepage/featured-shops',
            transformResponse: (res: any) => res.result,
        }),

        getTwoBannerRow: builder.query<IGetBannerRow[], void>({
            query: () => '/homepage/two-banners-row',
            transformResponse: (res: any) => res.result,
        }),

        getNewArrivals: builder.query<IProductShort[], void>({
            query: () => '/homepage/new-arrivals',
            transformResponse: (res: any) => res.result,
        }),

        getFeaturedBrands: builder.query<IGetFeaturedBrands, void>({
            query: () => '/homepage/featured-brands',
            transformResponse: (res: any) => res.result,
        }),

        getTrendyProducts: builder.query<IGetTrendyProducts, void>({
            query: () => '/homepage/trendy-products',
            transformResponse: (res: any) => res.result,
        }),

        getThreeBannerRow: builder.query<IGetBannerRow[], void>({
            query: () => '/homepage/three-banners-row',
            transformResponse: (res: any) => res.result,
        }),

        getCategoryProducts: builder.query<IGetCategoryProducts[], void>({
            query: () => `/homepage/category-products`,
            transformResponse: (res: any) => res.result,
        }),

        getAboutUs: builder.query<IGetAboutUs, void>({
            query: () => '/homepage/about-us',
            transformResponse: (res: any) => res.result,
        }),

        getAllCategories: builder.query<
            {
                categories: ICategoryShort[];
            },
            void
        >({
            query: () => '/homepage/categories',
            transformResponse: (res: any) => res.result,
        }),

        getMainCategories: builder.query<IGetMainCategories, void>({
            query: () => '/homepage/main-categories',
            transformResponse: (res: any) => res.result,
        }),

        getFooter: builder.query<IGetFooter, void>({
            query: () => '/homepage/footer',
            transformResponse: (res: any) => res.result,
        }),
    }),
});

export const {
    useGetBannerSlidersQuery,
    useGetVideosQuery,
    useGetFeaturedCategoriesQuery,
    useGetFeaturedProductsQuery,
    useGetFlashSaleProductsQuery,
    useGetProductSectionOneQuery,
    useGetFullWidthBannerQuery,
    useGetProductSectionTwoQuery,
    useGetFourBannerRowQuery,
    useGetFeaturedShopsQuery,
    useGetTwoBannerRowQuery,
    useGetNewArrivalsQuery,
    useGetFeaturedBrandsQuery,
    useGetTrendyProductsQuery,
    useGetThreeBannerRowQuery,
    useGetCategoryProductsQuery,
    useGetAboutUsQuery,
    useGetAllCategoriesQuery,
    useGetMainCategoriesQuery,
    useGetFooterQuery,
} = userApi;
