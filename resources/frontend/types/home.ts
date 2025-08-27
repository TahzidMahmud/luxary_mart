import { ICategoryShort } from '.';
import { IBrandShort } from './brand';
import { ICampaign } from './campaign';
import { IProductShort } from './product';
import { IShop } from './shop';

export interface BannerSlider {
    image: string;
    url: string;
}

export interface IGetFeaturedCategories {
    categories: ICategoryShort[];
    showFeaturedCategories: '0' | '1';
}

export interface IGetFeaturedProducts {
    products: IProductShort[];
    showHomeFeaturedProducts: '0' | '1';
    homeFeaturedProductBanner: string;
    homeFeaturedProductLink: string;
}

export interface IGetFlashSaleProducts {
    showHomeFlashSale: '0' | '1';
    flashSaleCampaign: ICampaign | null;
    flashSaleProducts: IProductShort[];
}

export interface IGetProductSectionOne {
    products: IProductShort[];
    showHomeProductSectionOne: '0' | '1';
    homeProductSectionOneTitle: string;
}

export interface IGetFullWidthBanner {
    image: string;
    url: string;
}

export interface IGetProductSectionTwo {
    products: IProductShort[];
    showHomeProductSectionTwo: '0' | '1';
    homeProductSectionTwoTitle: string;
}

export interface IGetFeaturedShops {
    shops: IShop[];
    showFeaturedShops: '0' | '1';
}

export interface IGetBannerRow {
    image: string;
    url: string;
}

export interface IGetFeaturedBrands {
    brands: IBrandShort[];
    showFeaturedBrands: '0' | '1';
}

export interface IGetTrendyProducts {
    showHomeTrendyProducts: '0' | '1';
    products: IProductShort[];
}

export interface IGetCategoryProducts {
    category: ICategoryShort;
    products: IProductShort[];
    image: string;
    url: string;
}

export interface IGetAboutUs {
    showHomeAboutUsSection: '0' | '1';
    homeAboutUsTitle: string;
    homeAboutUsSubTitle: string;
    homeAboutUsText: string;
    homeAboutUsImage: string;
}

export interface IGetMainCategories {
    categories: ICategoryShort[];
    showHomeMainCategories: '0' | '1';
}

export interface IGetFooter {
    homeFooterLogo: string;
    homeFooterCustomerSupport: string;
    homeFooterAddress: string;
    homeFooterEmail: string;

    showHomeFacebookLink: '0' | '1';
    homeFooterFacebookLink: string;

    showHomeTwitterLink: '0' | '1';
    homeFooterTwitterLink: string;

    showHomeInstagramLink: '0' | '1';
    homeFooterInstagramLink: string;

    showHomeYoutubeLink: '0' | '1';
    homeFooterYoutubeLink: string;

    showHomeLinkedInLink: '0' | '1';
    homeFooterLinkedInLink: string;

    quickLinkPages: {
        title: string;
        url: string;
    }[];
    topCategories: ICategoryShort[];
    popularTags: string[];
    defaultPages: {
        id: number;
        title: string;
        slug: string;
    }[];
    showHomeFooterPlayStoreLink: '0' | '1';
    homeFooterPlayStoreLink: string;

    showHomeFooterAppStoreLink: '0' | '1';
    homeFooterAppStoreLink: string;

    homeFooterCopyrightText: string;
    homeFooterSecuredPayments: string;
}
