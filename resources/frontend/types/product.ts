import { ICategory } from '.';
import { IBrandShort } from './brand';
import { IShop } from './shop';
import { IUser } from './state';

export interface IProductVariation {
    id: number;
    name: string;
    productId: number;
    code: string | null;
    sku: string;
    stocks: {
        id: number;
        warehouseId: number;
        stockQty: number;
    }[];
    image: string;
    basePrice: number;
    discountedBasePrice: number;
    basePriceWithTax: number;
    discountedBasePriceWithTax: number;
    tax: number;
    dealEnds: string | null;
}

export interface VariationCombination {
    id: number;
    name: string;
    values: {
        id: number;
        name: string;
        image: string;
        variationImage?: string;
        matchVariationCode: `${string | number}:${string | number}`;
    }[];
}
export interface ICartProduct {
    id: number;
    userId: number;
    guestUserId: number;
    productVariationId: number;
    warehouseId: number;
    qty: number;
    product: IProductShort;
    shop: IShop;
    variation: IProductVariation;
}

export interface IPromoCode {
    id: number;
    code: string;
    info: string;
    shopId: number;
    shopSlug: string;
    discountType: 'amount' | 'percentage';
    discountAmount: number;
    isFreeShipping: number;
    startDate: string;
    endDate: string;
    minSpend: number;
    maxDiscountAmount: number | null;
    banner: string;
}

export interface IBadge {
    id: number;
    name: string;
    textColor: string;
    bgColor: string;
}

export interface IProductShort {
    id: number;
    name: string;
    slug: string;
    inWishlist: boolean;
    thumbnailImg: string;
    basePrice: number;
    unit: string;
    stockQty: number;
    hasVariation: number;
    badges: IBadge[];
    tags: { id: number; name: string }[];
    rating: {
        average: number;
        total: number;
    };
}

export interface IProduct extends IProductShort {
    deliveryHours: number;
    codAvailable: boolean;

    hasEmi: 1 | 0;
    emiInfo: string;

    hasWarranty: 1 | 0;
    warrantyInfo: string;

    rootCategory: ICategory;
    categories: ICategory[];

    description: string;
    images: string[];
    shop: IShop;
    brand: IBrandShort;
    promoCode?: IPromoCode;
    relatedProducts: IProductShort[];
    variations: IProductVariation[];
    variationCombinations: VariationCombination[];
}

export interface ICartProduct {
    id: number;
    userId: number;
    guestUserId: number;
    productVariationId: number;
    warehouseId: number;
    qty: number;
    product: IProductShort;
    shop: IShop;
    variation: IProductVariation;
}

export interface IReview {
    id: number;
    userId: number;
    user: IUser;
    rating: number;
    description: string;
    images: {
        id: number;
        image: string;
    }[];
    product: IProductShort;
    shop: IShop;
    createdDate: string;
}
