import { useEffect, useState } from 'react';
import {
    FaBars,
    FaHeart,
    FaPowerOff,
    FaSearch,
    FaShoppingCart,
    FaUser,
} from 'react-icons/fa';
import { FaCartShopping, FaCircleUser, FaGear } from 'react-icons/fa6';
import { Link, useNavigate } from 'react-router-dom';
import { apiSlice } from '../../../../store/features/api/apiSlice';
import { useLazyGetWishListQuery } from '../../../../store/features/api/productApi';
import {
    useAuth,
    userLoggedOut,
} from '../../../../store/features/auth/authSlice';
import {
    closePopup,
    togglePopup,
    usePopup,
} from '../../../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../../../store/store';
import { paddedNumber } from '../../../../utils/numberFormatter';
import { translate } from '../../../../utils/translate';
import Avatar from '../../../Avatar';
import SearchForm from '../../../inputs/SearchForm';
import LocationToggler from './LocationToggler';

const Logobar = () => {
    const navigate = useNavigate();
    const { popup } = usePopup();
    const { user, carts } = useAuth();
    const dispatch = useAppDispatch();
    const [getWishlist, { data: wishlist }] = useLazyGetWishListQuery();

    const [totalProductInCart, setTotalProductInCart] = useState(0);

    const avatarDropdownMenu = [
        {
            icon: <FaUser />,
            name: 'Dashboard',
            url: '/dashboard',
        },
        {
            icon: <FaShoppingCart />,
            name: 'My Orders',
            url: '/orders',
        },
        {
            icon: <FaHeart />,
            name: 'Wishlist',
            url: '/wishlist',
        },
        {
            icon: <FaGear />,
            name: 'Settings',
            url: '/settings',
        },
    ];

    useEffect(() => {
        let totalProducts = 0;

        carts.forEach((item) => {
            totalProducts += item.qty;
        });

        setTotalProductInCart(totalProducts);
    }, [carts]);

    // get wishlist if user is logged in
    useEffect(() => {
        if (user) {
            getWishlist(undefined, true);
        }
    }, [user]);

    // open wishlist page if user is logged in
    // otherwise open signin popup
    const handleWishlistClick = () => {
        if (user) {
            navigate('/wishlist');
        } else {
            dispatch(togglePopup('signin'));
        }
    };

    const handleSearchClick = () => {
        if (popup === 'search') {
            dispatch(closePopup());
        } else {
            dispatch(
                togglePopup({
                    popup: 'search',
                    popupProps: {
                        overlay: false,
                    },
                }),
            );
        }
    };

    return (
        <div className="h-[60px] md:h-[70px] flex items-center justify-center text-zinc-500 sticky top-0 bg-white z-[3] shadow-[0_4px_6px_0_#00000017]">
            <div className="container flex items-center justify-between gap-8 sm:gap-[50px]">
                <div className="flex items-center gap-5 lg:gap-14">
                    <button
                        className="lg:hidden text-lg text-theme-secondary-light"
                        onClick={() => dispatch(togglePopup('categories'))}
                    >
                        <FaBars />
                    </button>

                    <Link to="/">
                        <img
                            src={window.config.generalSettings.logo}
                            alt=""
                            className="max-xs:max-w-[100px] max-sm:max-w-[140px]"
                        />
                    </Link>
                    <button
                        className="hidden lg:flex items-center bg-theme-primary text-white py-2.5 px-4 rounded-md hover:bg-theme-primary/90"
                        onClick={() => dispatch(togglePopup('categories'))}
                    >
                        <span>
                            <FaBars />
                        </span>
                        <span className="hidden xl:inline-block pl-4 pr-1">
                            {translate('All Categories')}
                        </span>
                    </button>
                </div>
                <div className="grow max-md:hidden">
                    <SearchForm
                        placeholder="Search for products, brands and categories..."
                        searchOnType={false}
                        submittableButton={true}
                    />
                </div>
                <div className="hidden lg:flex items-center justify-end gap-7">
                    <button
                        className="inline-flex flex-col items-center gap-1 text-2xl text-theme-primary relative"
                        onClick={handleWishlistClick}
                    >
                        <FaHeart />

                        {wishlist?.length ? (
                            <span className="flex items-center justify-center absolute top-0 right-0 -translate-y-1/2 translate-x-2/3 text-xs h-5 min-w-[20px] bg-white rounded-full shadow-lg">
                                {paddedNumber(wishlist?.length)}
                            </span>
                        ) : null}
                    </button>

                    <button
                        className="inline-flex flex-col items-center gap-1 text-2xl text-theme-primary relative"
                        onClick={() => dispatch(togglePopup('cart'))}
                    >
                        <FaCartShopping />

                        {totalProductInCart ? (
                            <span className="flex items-center justify-center absolute top-0 right-0 -translate-y-1/2 translate-x-2/3 text-xs h-5 min-w-[20px] bg-white rounded-full shadow-lg">
                                {paddedNumber(totalProductInCart)}
                            </span>
                        ) : null}
                    </button>

                    {user ? (
                        <div className="option-dropdown" tabIndex={0}>
                            <div className="option-dropdown__toggler no-style no-arrow p-0 h-auto flex items-center gap-2 bg-white text-inherit min-w-0">
                                <Avatar
                                    name={user.name}
                                    avatar={user.avatar}
                                    size="sm"
                                    className="w-6 h-6"
                                />

                                <h5 className="arm-h4">
                                    {user.name.split(' ')[0]}
                                </h5>
                            </div>

                            <div className="option-dropdown__options w-[170px] top-[calc(100%+20px)] left-auto right-0 rounded-none p-0">
                                <ul>
                                    {avatarDropdownMenu.map((item) => (
                                        <li key={item.name}>
                                            <Link
                                                to={item.url}
                                                className="option-dropdown__option rounded-none"
                                            >
                                                <span className="text-lg text-stone-300">
                                                    {item.icon}
                                                </span>
                                                <span>{item.name}</span>
                                            </Link>
                                        </li>
                                    ))}
                                    <li>
                                        <button
                                            className="option-dropdown__option rounded-none"
                                            onClick={() => {
                                                dispatch(userLoggedOut());
                                                dispatch(
                                                    apiSlice.util.resetApiState(),
                                                );
                                            }}
                                        >
                                            <span className="text-lg text-red-400">
                                                <FaPowerOff />
                                            </span>
                                            <span className="text-red-400">
                                                {translate('logout')}
                                            </span>
                                        </button>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    ) : (
                        <button
                            onClick={() => dispatch(togglePopup('signin'))}
                            className="inline-flex items-center gap-2"
                        >
                            <span className="text-2xl text-theme-primary">
                                <FaCircleUser />
                            </span>
                            <span>{translate('login')}</span>
                        </button>
                    )}
                </div>

                <div className="inline-flex lg:hidden text-end gap-4 text-base">
                    <LocationToggler />

                    <button
                        onClick={handleSearchClick}
                        className="h-10 w-10 inline-flex items-center justify-center bg-theme-primary text-white text-lg rounded"
                    >
                        <FaSearch />
                    </button>
                </div>
            </div>
        </div>
    );
};

export default Logobar;
