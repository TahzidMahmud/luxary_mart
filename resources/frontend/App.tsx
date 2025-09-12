import { Suspense, useEffect } from 'react';
import { useDispatch } from 'react-redux';
import { Route, Routes, useLocation } from 'react-router-dom';
import DashboardLayout from './components/layouts/DashboardLayout';
import PrimaryLayout from './components/layouts/PrimaryLayout';
import ProtectedLayout from './components/layouts/ProtectedLayout';
import ShopLayout from './components/layouts/ShopLayout';
import PaymentGateway from './components/order/PaymentGateway';
import Brands from './pages/Brands';
import Coupons from './pages/Coupons';
import Discounts from './pages/Discounts';
import DynamicPage from './pages/DynamicPage';
import Home from './pages/Home';
import NotFound from './pages/NotFound';
import ProductDetails from './pages/ProductDetails';
import CampaignDetails from './pages/campaign/CampaignDetailsV1';
import CampaignDetailsV2 from './pages/campaign/CampaignDetailsV2';
import Campaigns from './pages/campaign/Campaigns';
import ChatHistory from './pages/dashboard/ChatHistory';
import Dashboard from './pages/dashboard/Dashboard';
import OrderDetails from './pages/dashboard/OrderDetails';
import Orders from './pages/dashboard/Orders';
import Reviews from './pages/dashboard/Reviews';
import Settings from './pages/dashboard/Settings';
import Wishlist from './pages/dashboard/Wishlist';
import Cart from './pages/order/Cart';
import Checkout from './pages/order/Checkout';
import OrderFailed from './pages/order/OrderFailed';
import OrderSuccess from './pages/order/OrderSuccess';
import OrderTracking from './pages/order/OrderTracking';
import Brand from './pages/search-filter/Brand';
import Category from './pages/search-filter/Category';
import Search from './pages/search-filter/Search';
import SellerSignup from './pages/seller/Signup';
import SellerSignupSuccess from './pages/seller/SignupSuccess';
import ShopDetails from './pages/shops/ShopDetails';
import ShopOffers from './pages/shops/ShopOffers';
import ShopProducts from './pages/shops/ShopProducts';
import ShopProfile from './pages/shops/ShopProfile';
import Shops from './pages/shops/Shops';
import { useAuth } from './store/features/auth/authSlice';
import {
    setCheckoutData,
    useCheckoutData,
} from './store/features/checkout/checkoutSlice';
import { translate } from './utils/translate';

const App = () => {
    const dispatch = useDispatch();
    const { carts, isLoading } = useAuth();
    const { selectedShops } = useCheckoutData();
    const location = useLocation();

    // Scroll to top if path changes
    useEffect(() => {
        if (location.state?.scrollToTop === false) return;

        window.scrollTo({
            top: 0,
            left: 0,
            behavior: 'instant',
        });
    }, [location.pathname, location.search]);

    // update selected shop state when cart is changed
    useEffect(() => {
        if (isLoading) return;

        // find all shops
        const shops: Record<string, boolean> = {};
        carts.forEach(({ shop }) => {
            // if shop is already selected, then keep it true
            // else false
            shops[shop.name] =
                selectedShops[shop.name] === false ? false : true;
        });
        dispatch(
            setCheckoutData({
                selectedShops: shops,
            }),
        );
    }, [carts]);

    // if appMode is singleVendor, then hide the seller related pages
    const { appMode } = window.config.generalSettings;

    return (
        <Routes>
            <Route element={<PrimaryLayout />}>
                <Route path="/" element={<Home />} />
                <Route path="/products/:slug" element={<ProductDetails />} />

                <Route path="/brands" element={<Brands />} />
                {/* start::uses search and filter */}
                <Route path="/brands/:brandSlug" element={<Brand />} />
                <Route
                    path="/categories/:categorySlug"
                    element={<Category />}
                />
                <Route path="/products" element={<Search />} />
                {/* end::uses search and filter */}

                {appMode === 'multiVendor' ? (
                    <>
                        <Route path="/shops" element={<Shops />} />
                        <Route
                            path="/shops/:shopSlug"
                            element={
                                <Suspense
                                    fallback={<>{translate('loading')}</>}
                                >
                                    <ShopLayout />
                                </Suspense>
                            }
                        >
                            <Route index element={<ShopDetails />} />
                            <Route path="products" element={<ShopProducts />} />
                            <Route path="offers" element={<ShopOffers />} />
                            <Route path="profile" element={<ShopProfile />} />
                        </Route>
                    </>
                ) : null}

                {appMode === 'multiVendor' ? (
                    <>
                        {/* seller registration */}
                        <Route
                            path="/seller/signup"
                            element={<SellerSignup />}
                        />
                        <Route
                            path="/seller/signup/success"
                            element={<SellerSignupSuccess />}
                        />
                    </>
                ) : null}

                <Route path="/discounts" element={<Discounts />} />
                <Route path="/coupons" element={<Coupons />} />
                <Route path="/campaigns" element={<Campaigns />} />
                <Route path="/campaigns/:slug" element={<CampaignDetails />} />
                <Route
                    path="/campaigns/:slug/v2"
                    element={<CampaignDetailsV2 />}
                />

                {/* dashboard pages */}
                <Route element={<DashboardLayout />}>
                    <Route path="/dashboard" element={<Dashboard />} />
                    <Route path="/orders" element={<Orders />} />
                    <Route
                        path="/orders/:orderCode"
                        element={<OrderDetails />}
                    />
                    <Route path="/wishlist" element={<Wishlist />} />
                    <Route path="/chats" element={<ChatHistory />} />
                    <Route path="/reviews" element={<Reviews />} />
                    <Route path="/settings" element={<Settings />} />
                </Route>

                <Route element={<ProtectedLayout />}>
                    <Route path="/payment" element={<PaymentGateway />} />
                    <Route path="/orders/failed" element={<OrderFailed />} />
                </Route>
                <Route path="/checkout" element={<Checkout />} />
                 <Route
                    path="/orders/success/:orderCode"
                    element={<OrderSuccess />}
                />

                <Route path="/cart" element={<Cart />} />
                <Route path="/orders/track" element={<OrderTracking />} />
                <Route
                    path="/orders/track/:orderCode"
                    element={<OrderTracking />}
                />

                <Route path="/pages/:slug" element={<DynamicPage />} />

                <Route path="/*" element={<NotFound />} />
            </Route>
        </Routes>
    );
};

export default App;
