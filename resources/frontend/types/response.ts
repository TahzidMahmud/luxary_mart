import { ICartProduct, IProductShort } from './product';
import { IDashboardInfo, IUser } from './state';

export interface AuthResponse {
    success: boolean;
    authStatus: string;
    access_token: string;
    token_type: string;
    expires_at: string;
    user: IUser;
    dashboardInfo: IDashboardInfo;
    carts: ICartProduct[];
    countries: unknown[];
    message: string;
}

export interface IWishListResponse {
    id: number;
    userId: number;
    product: IProductShort;
}
