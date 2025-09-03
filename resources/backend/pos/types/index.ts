declare global {
    interface Window {
        config: {
            countries: ICountry[];
            posCartGroup: IPosCartGroup;
            posWarehouseId?: number;
        };
    }
}

export interface IFilters {
    page: number;
    warehouseId: number | string | null;
    brandId: number | string | null;
    categoryId: number | string | null;
    searchKey: string | null;
}

export interface IFilterData {
    warehouses: {
        id: number;
        name: string;
    }[];
    brands: {
        id: number;
        name: string;
    }[];
    categories: {
        id: number;
        name: string;
    }[];
}

export interface IPosProductVariation {
    id: number;
    productId: number;
    product: {
        id: number;
        name: string;
        thumbnailImg: string;
    };
    name: string;
    code: string;
    sku: string;
    stocks: {
        id: number;
        warehouseId: number;
        stockQty: number;
    }[];
    combinations: {
        id: number;
        name: string;
    }[];
    image: string;
    basePrice: number;
    discountedBasePrice: number;
    basePriceWithTax: number;
    discountedBasePriceWithTax: number;
    tax: number;
}


export interface ICountry {
    id: number;
    code: string;
    name: string;
}
export interface IState {
    id: number;
    country_id: number;
    name: string;
    is_active: number;
}
export interface ICity {
    id: number;
    name: string;
}
export interface IArea {
    id: number;
    name: string;
}

export interface IPosCartGroup {
    id?: number;
    posCartGroupId?: number;
    onHold?: boolean;
    customerId?: number;
    customer?: ICustomer;
    shippingAddressId?: number;
    discount?: number;
    shippingCharge?: number;
    advance?: number;
    paymentMethod?: string;
    orderReceivingDate?: string;
    orderShipmentDate?: string;
    note?: string;
    createdAt?: string;

    posCarts: {
        id: number;
        productVariationId: number;
        warehouseId: number;
        qty: number;
        product: IPosProductVariation['product'];
        variation: {
            id: number;
            productId: number;
            product: IPosProductVariation['product'];
            name: string;
            code: string;
            sku: string;
            stocks: {
                id: number;
                warehouseId: number;
                stockQty: number;
            }[];
            combinations: {
                id: number;
                name: string;
            }[];
            image: string;
            basePrice: number;
            discountedBasePrice: number;
            basePriceWithTax: number;
            discountedBasePriceWithTax: number;
            tax: number;
        };
    }[];
}

export interface IAddress {
    id: number;
    userId: number;

    countryId: number;
    country: ICountry;

    stateId: number;
    state: IState;

    cityId: number;
    city: ICity;

    areaId: number;
    area: IArea & { zone_id: number };

    address: string;
    fullAddress: string;
    type: string;
    direction: string;
    postalCode: string;
    isDefault: number;
}
export interface ICustomer {
    id: number;
    name: string;
    phone: string;
    addresses:IAddress[];
}
