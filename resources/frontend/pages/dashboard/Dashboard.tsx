import { BsArrowRight } from 'react-icons/bs';
import { FaHeart, FaShoppingCart, FaStar } from 'react-icons/fa';
import { Link } from 'react-router-dom';
import AddAddressCard from '../../components/card/AddAddressCard';
import AddressCard from '../../components/card/AddressCard';
import { useGetWishListQuery } from '../../store/features/api/productApi';
import { useGetAllReviewOfUserQuery } from '../../store/features/api/reviewApi';
import { useAuth } from '../../store/features/auth/authSlice';
import { useGetAddressesQuery } from '../../store/features/checkout/checkoutApi';
import { currencyFormatter, paddedNumber } from '../../utils/numberFormatter';
import { translate } from '../../utils/translate';

const Dashboard = () => {
    const { user, guestUserId, carts, dashboardInfo } = useAuth();
    const { data: addressRes } = useGetAddressesQuery();
    const { data: wishlist } = useGetWishListQuery();
    const { data: reviewRes } = useGetAllReviewOfUserQuery({
        userId: user?.id || guestUserId!,
    });
    const addresses = addressRes?.addresses;

    return (
        <div className="p-3 sm:p-6 lg:p-11 bg-white border border-zinc-100 rounded-md">
            <div className="grid md:grid-cols-3">
                <div className="relative rounded-md bg-[#57AFA4] px-4 md:px-7 py-4 md:pt-8 md:pb-6 overflow-hidden">
                    {/* round shape */}
                    <span className="absolute bottom-0 left-0 -translate-x-[20%] translate-y-[33%] h-[166px] aspect-square rounded-full bg-[rgba(105,194,184,0.35)]"></span>

                    {/* icon */}
                    <span className="absolute top-6 right-7 h-[50px] w-[50px] flex items-center justify-center text-xl rounded-full bg-[#68C3B7] text-[#279587]">
                        <FaShoppingCart />
                    </span>

                    <div className="relative z-[1]">
                        <p className="text-[#085B50] text-sm font-public-sans uppercase">
                            {translate('Total Orders')}
                        </p>
                        <p className="text-white text-2xl md:text-[35px] font-semibold">
                            {paddedNumber(dashboardInfo?.totalOrders)}
                        </p>
                        <time className="arm-h4 text-white">
                            {translate('Last')} {dashboardInfo?.timeline.count}{' '}
                            {dashboardInfo?.timeline.unit}
                        </time>
                    </div>

                    <div className="relative z-[1] text-right">
                        <Link
                            to="/orders"
                            className="inline-flex items-center gap-2 text-xs text-white mt-1.5"
                        >
                            {translate('View Orders')}
                            <span className="text-xl">
                                <BsArrowRight />
                            </span>
                        </Link>
                    </div>
                </div>
                <div className="relative rounded-md bg-[#74C1FD] px-4 md:px-7 py-4 md:pt-8 md:pb-6 overflow-hidden">
                    {/* round shape */}
                    <span className="absolute bottom-0 left-0 -translate-x-[20%] translate-y-[33%] h-[166px] aspect-square rounded-full bg-[#8ED6FF59]"></span>

                    {/* icon */}
                    <span className="absolute top-6 right-7 h-[50px] w-[50px] flex items-center justify-center text-xl rounded-full bg-[#98D2FF] text-[#45ADFF]">
                        <FaShoppingCart />
                    </span>

                    <div className="relative z-[1]">
                        <p className="text-[#004F8E] text-sm font-public-sans uppercase">
                            {translate('Total Expense')}
                        </p>
                        <p className="text-white text-2xl md:text-[35px] font-semibold">
                            {currencyFormatter(dashboardInfo?.totalExpense)}
                        </p>
                        <time className="arm-h4 text-white">
                            {translate('Last')} {dashboardInfo?.timeline.count}{' '}
                            {dashboardInfo?.timeline.unit}
                        </time>
                    </div>

                    <div className="relative z-[1] text-right">
                        <Link
                            to="/orders"
                            className="inline-flex items-center gap-2 text-xs text-white mt-1.5"
                        >
                            {translate('View Orders')}
                            <span className="text-xl">
                                <BsArrowRight />
                            </span>
                        </Link>
                    </div>
                </div>
                <div className="relative rounded-md bg-[#FF7E06] px-4 md:px-7 py-4 md:pt-8 md:pb-6 overflow-hidden">
                    {/* round shape */}
                    <span className="absolute bottom-0 left-0 -translate-x-[20%] translate-y-[33%] h-[166px] aspect-square rounded-full bg-[#FFA26E59]"></span>

                    {/* icon */}
                    <span className="absolute top-6 right-7 h-[50px] w-[50px] flex items-center justify-center text-xl rounded-full bg-[#FFAA5C] text-[#F57702]">
                        <FaShoppingCart />
                    </span>

                    <div className="relative z-[1]">
                        <p className="text-[#723700] text-sm font-public-sans uppercase">
                            {translate('Total Products')}
                        </p>
                        <p className="text-white text-2xl md:text-[35px] font-semibold">
                            {paddedNumber(dashboardInfo?.totalProducts)}
                        </p>
                        <time className="arm-h4 text-white">
                            {translate('last')} {dashboardInfo?.timeline.count}{' '}
                            {dashboardInfo?.timeline.unit}
                        </time>
                    </div>

                    <div className="relative z-[1] text-right">
                        <Link
                            to="/orders"
                            className="inline-flex items-center gap-2 text-xs text-white mt-1.5"
                        >
                            {translate('View Orders')}
                            <span className="text-xl">
                                <BsArrowRight />
                            </span>
                        </Link>
                    </div>
                </div>
            </div>

            <hr className="border-theme-primary-14 mt-10 mb-8" />

            <div className="grid md:grid-cols-3 uppercase">
                <Link
                    to="/wishlist"
                    className="px-5 py-6 flex items-center gap-3 border border-zinc-100 rounded-md text-sm hover:bg-zinc-100 hover:border-theme-secondary-light"
                >
                    <span className="h-[50px] w-[50px] rounded-full bg-zinc-300 text-white text-xl inline-flex items-center justify-center">
                        <FaHeart />
                    </span>
                    <div>
                        <span className="">{translate('my wishlist')}</span>
                    </div>
                    {wishlist?.length ? (
                        <span className="font-public-sans ml-auto bg-theme-secondary-light text-white rounded-md px-2.5 py-[3px]">
                            {paddedNumber(wishlist?.length, 2)}
                        </span>
                    ) : null}
                </Link>
                <Link
                    to="/reviews"
                    className="px-5 py-6 flex items-center gap-3 border border-zinc-100 rounded-md text-sm hover:bg-zinc-100 hover:border-theme-secondary-light"
                >
                    <span className="h-[50px] w-[50px] rounded-full bg-zinc-300 text-white text-xl inline-flex items-center justify-center">
                        <FaStar />
                    </span>
                    <div>
                        <span className="">{translate('my reviews')}</span>
                    </div>
                    <span className="font-public-sans ml-auto bg-theme-secondary-light text-white rounded-md px-2.5 py-[3px]">
                        {reviewRes?.reviews.meta.total}
                    </span>
                </Link>
                <Link
                    to="/cart"
                    className="px-5 py-6 flex items-center gap-3 border border-zinc-100 rounded-md text-sm hover:bg-zinc-100 hover:border-theme-secondary-light"
                >
                    <span className="h-[50px] w-[50px] rounded-full bg-zinc-300 text-white text-xl inline-flex items-center justify-center">
                        <FaStar />
                    </span>
                    <div>
                        <span className="">{translate('product in cart')}</span>
                    </div>
                    <span className="font-public-sans ml-auto bg-theme-secondary-light text-white rounded-md px-2.5 py-[3px]">
                        {paddedNumber(carts?.length, 2)}
                    </span>
                </Link>
            </div>

            <h4 className="font-public-sans text-sm mt-10 mb-5 uppercase">
                {translate('Saved Address')}
            </h4>

            <div className="">
                <div className="grid sm:grid-cols-2 gap-3 text-xs">
                    {addresses?.map((address) => (
                        <AddressCard address={address} key={address.id} />
                    ))}

                    <AddAddressCard />
                </div>
            </div>
        </div>
    );
};

export default Dashboard;
