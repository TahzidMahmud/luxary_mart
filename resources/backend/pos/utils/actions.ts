import QueryString from 'qs';
import { IPaginatedResponse } from '../../react/types';
import { objectToFormData } from '../../react/utils/ObjectFormData';
import {
    IAddress,
    IArea,
    ICity,
    ICustomer,
    IFilterData,
    IFilters,
    IPosCartGroup,
    IPosProductVariation,
    IState,
} from '../types';

export const getPosProductsVariations = async (params: IFilters) => {
    const query = QueryString.stringify(params);

    const response = await fetch(`/admin/api/pos/product-variations?${query}`);
    const body = await response.json();
    return body.result as Promise<IPaginatedResponse<IPosProductVariation[]>>;
};

export const getPosFilterData = async () => {
    const response = await fetch('/admin/api/pos/filter-data');
    return (await response.json()).result as IFilterData;
};

export const addToPosCartGroup = async (body: FormData) => {
    const response = await fetch('/admin/api/pos/add-to-cart', {
        method: 'POST',
        body,
    });
    return (await response.json()).result as IPosCartGroup;
};

/**
 * increase or decrease the quantity of a product in the cart
 */
export const updatePosCartItem = async (body: FormData) => {
    const response = await fetch('/admin/api/pos/cart/update', {
        method: 'POST',
        body,
    });
    return (await response.json()).result.posCarts as IPosCartGroup['posCarts'];
};

export const addCustomer = async (body: FormData) => {
    const response = await fetch('/admin/api/pos/customer', {
        method: 'POST',
        body,
    });
    return (await response.json()).result as {
        customers: ICustomer[];
        newCustomer: ICustomer;
    };
};

export const getCustomerFilter = async () => {
    const response = await fetch('/admin/api/pos/customers');
    return (await response.json()).result as ICustomer[];
};

export const getStates = async (countryId: number) => {
    const response = await fetch(`/admin/api/pos/get-states/${countryId}`);
    return (await response.json()).result as IState[];
};

export const getCities = async (stateId: number) => {
    const response = await fetch(`/admin/api/pos/get-cities/${stateId}`);
    return (await response.json()).result as ICity[];
};

export const getAreas = async (cityId: number) => {
    const response = await fetch(`/admin/api/pos/get-areas/${cityId}`);
    return (await response.json()).result as IArea[];
};

export const holdOrder = async (posCartGroupId: number) => {
    const response = await fetch(`/admin/api/pos/cart/hold`, {
        method: 'POST',
        body: objectToFormData({ posCartGroupId }),
    });
    return (await response.json()).result as IPosCartGroup;
};

export const getHoldOrders = async () => {
    const response = await fetch(`/admin/api/pos/cart/hold`);
    return (await response.json()).result as IPosCartGroup[];
};

export const addAddress = async (body: FormData) => {
    const response = await fetch('/admin/api/pos/address', {
        method: 'POST',
        body,
    });
    return (await response.json()).result.addresses as IAddress;
};

export const getAddresses = async (customerId: number) => {
    const response = await fetch(
        `/admin/api/pos/customers/address/${customerId}`,
    );
    return (await response.json()).result.addresses as IAddress[];
};

export const deleteHoldCart = async (posCartGroupId: number) => {
    const response = await fetch(
        `/admin/api/pos/cart/hold/delete/${posCartGroupId}`,
        {
            method: 'POST',
            body: objectToFormData({ posCartGroupId }),
        },
    );
    return (await response.json()).result;
};

export const submitOrder = async (body: FormData) => {
    const response = await fetch(`/admin/api/pos/cart/submit-order`, {
        method: 'POST',
        body,
    });

    if (!response.ok) {
        throw new Error((await response.json()).message);
    }

    return (await response.json()).result as {
        orderId: number;
    };
};
