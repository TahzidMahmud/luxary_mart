import {
    FaHeart,
    FaPowerOff,
    FaShoppingCart,
    FaStar,
    FaUser,
} from 'react-icons/fa';
import { FaGear } from 'react-icons/fa6';
import { NavLink, Outlet } from 'react-router-dom';
import { apiSlice } from '../../../store/features/api/apiSlice';
import { useAuth, userLoggedOut } from '../../../store/features/auth/authSlice';
import { useAppDispatch } from '../../../store/store';
import { cn } from '../../../utils/cn';
import { translate } from '../../../utils/translate';
import Avatar from '../../Avatar';
import {
    DashboardReviewsSkeleton,
    DashboardSkeleton,
    OrderDetailsSkeleton,
    WishlistSkeleton,
} from '../../skeletons/from-svg';

interface Props {
    className?: string;
}

export const profileLinks = [
    {
        icon: <FaUser />,
        name: 'dashboard', // lang key for translation
        urlRegex: /\/dashboard/,
        url: '/dashboard',
        showInSidebar: true,
        preloader: <DashboardSkeleton />,
    },
    {
        icon: <FaShoppingCart />,
        name: 'my_orders',
        urlRegex: /\/orders/,
        url: '/orders',
        showInSidebar: true,
        preloader: <Outlet />, // just to avoid here, because /orders page is already handing the preloader
    },
    {
        icon: <FaShoppingCart />,
        name: 'order_details',
        urlRegex: /\/orders\/.+/,
        url: '/orders/:orderId',
        showInSidebar: false,
        preloader: <OrderDetailsSkeleton />,
    },
    {
        icon: <FaHeart />,
        name: 'wishlist',
        urlRegex: /\/wishlist/,
        url: '/wishlist',
        showInSidebar: true,
        preloader: <WishlistSkeleton />,
    },
    // {
    //     icon: <FaCommentDots />,
    //     name: 'chats',
    //     urlRegex: /\/chats/,
    //     url: '/chats',
    //     showInSidebar: true,
    //     preloader: <DashboardSkeleton />,
    // },
    {
        icon: <FaStar />,
        name: 'my_reviews',
        urlRegex: /\/reviews/,
        url: '/reviews',
        showInSidebar: true,
        preloader: <DashboardReviewsSkeleton />,
    },
    {
        icon: <FaGear />,
        name: 'settings',
        urlRegex: /\/settings/,
        url: '/settings',
        showInSidebar: true,
        preloader: <DashboardSkeleton />,
    },
];

const DashboardSidebar = ({ className }: Props) => {
    const dispatch = useAppDispatch();
    const { user } = useAuth();

    if (!user) {
        return <></>;
    }

    return (
        <div
            className={cn(
                'bg-white rounded-md pt-10 sticky top-[calc(70px+20px)]',
                className,
            )}
        >
            <div className="flex flex-col items-center">
                <Avatar name={user!.name} avatar={user!.avatar} size="xxl" />
                <h2 className="text-base font-public-sans mt-2.5">
                    {user!.name}
                </h2>
                <p className="mt-px text-zinc-500 text-xs">
                    {user?.phone || user?.email}
                </p>
            </div>

            <ul className="mt-4 divide-y divide-theme-primary-14">
                {profileLinks.map(
                    (item) =>
                        item.showInSidebar && (
                            <li key={item.name}>
                                <NavLink
                                    to={item.url}
                                    className={({ isActive }) =>
                                        cn(
                                            `option-dropdown__option gap-5 !px-8 !py-3.5 rounded-none`,
                                            {
                                                active: isActive,
                                            },
                                        )
                                    }
                                >
                                    <span className="text-lg text-stone-300">
                                        {item.icon}
                                    </span>
                                    <span>{translate(item.name)}</span>
                                </NavLink>
                            </li>
                        ),
                )}
                <li>
                    <button
                        className="option-dropdown__option gap-5 !px-8 !py-3.5 rounded-none"
                        onClick={() => {
                            dispatch(userLoggedOut());
                            dispatch(apiSlice.util.resetApiState());
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
    );
};

export default DashboardSidebar;
