import Overlay from '../layouts/components/Overlay';
import ConfirmationPopup from './ConfirmationPopup';
import DashboardSidebarModal from './DashboardSidebarModal';
import ReviewModal from './ReviewModal';
import SearchPopup from './SearchPopup';
import AddressModal from './address/AddressModal';
import UserLocationSelection from './address/UserLocationSelection';
import Signin from './auth/Signin';
import Signup from './auth/Signup';
import ForgetPasswordPopup from './auth/forget-password';
import UserVerificationPopup from './auth/user-verification';
import CartSidebar from './cart/CartSidebar';
import CategorySidebar from './category/CategorySidebar';
import CouponDetails from './coupon/CouponDetails';
import ProductPreviewModal from './product/ProductPreviewModal';

const Popups = () => {
    return (
        <>
            <CategorySidebar />
            <CartSidebar />
            <DashboardSidebarModal />
            <ProductPreviewModal />
            <CouponDetails />
            <ReviewModal />
            <SearchPopup />
            <AddressModal />
            <UserLocationSelection />
            <ForgetPasswordPopup />
            <UserVerificationPopup />
            <ConfirmationPopup />

            <Signin />
            <Signup />

            {/* common overlay */}
            <Overlay />
        </>
    );
};

export default Popups;
