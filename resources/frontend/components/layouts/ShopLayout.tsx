import { FaCommentDots } from 'react-icons/fa6';
import { NavLink, Outlet, useNavigate, useParams } from 'react-router-dom';
import NotFound from '../../pages/NotFound';
import { useCreateConversationMutation } from '../../store/features/api/chatApi';
import { useGetShopDetailsQuery } from '../../store/features/api/shopApi';
import { useAuth } from '../../store/features/auth/authSlice';
import { togglePopup } from '../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../store/store';
import { translate } from '../../utils/translate';
import Image from '../Image';
import Button from '../buttons/Button';
import SearchForm from '../inputs/SearchForm';
import ThemeRating from '../reviews/ThemeRating';
import ShopHeaderSkeleton from '../skeletons/shop/ShopHeaderSkeleton';
import ShopHomeSkeleton from '../skeletons/shop/ShopHomeSkeleton';

const shopProfileLinks = [
    {
        urlRegex: /\/shops\/(.+)\/products/, // match /shops/:shopSlug/products
        preloader: (
            <>
                <ShopHeaderSkeleton />
                <Outlet />
            </>
        ),
    },
    {
        urlRegex: /\/shops\/(.+)\/profile/, // match /shops/:shopSlug/profile
        preloader: (
            <>
                <ShopHeaderSkeleton />
                <Outlet />
            </>
        ),
    },
    {
        urlRegex: /\/shops\/(.+)/, // match /shops/:shopSlug
        preloader: (
            <>
                <ShopHeaderSkeleton />
                <ShopHomeSkeleton />
            </>
        ),
    },
];

const ShopLayout = () => {
    const dispatch = useAppDispatch();
    const { user } = useAuth();
    const navigate = useNavigate();
    const params = useParams<{ shopSlug: string }>();
    const { data: shop, isLoading } = useGetShopDetailsQuery(params.shopSlug!);
    const [createChat] = useCreateConversationMutation();

    const activeLink = shopProfileLinks.find((item) => {
        const regex = new RegExp(`^${item.urlRegex.source}$`);
        return location.pathname.match(regex);
    });

    const handleChat = async () => {
        if (!user) {
            dispatch(togglePopup('signin'));
            return;
        }

        if (!shop?.id) return;

        await createChat({ shopId: shop.id });
        navigate('/chats');
    };

    if (isLoading) {
        return activeLink?.preloader;
    }

    if (!shop) {
        return <NotFound />;
    }

    return (
        <>
            <div className="theme-container-card no-style">
                <div className="relative">
                    <img
                        src={shop?.banner}
                        alt=""
                        className="h-[150px] lg:h-[300px] bg-gray-500"
                    />

                    <div className="bg-white px-4 lg:pr-20 lg:pl-28 py-4 md:h-20 flex max-md:flex-col gap-4 md:items-center justify-between">
                        <div className="flex items-center gap-4 md:gap-8 h-10 md:h-20">
                            <div className="h-[100px] lg:h-[158px] aspect-square self-end inline-flex items-center justify-center p-2 bg-white border border-zinc-100 md:mb-5">
                                <Image src={shop?.logo} alt="" />
                            </div>

                            <div>
                                <h1 className="font-public-sans text-base md:text-xl text-black">
                                    {shop?.name}
                                </h1>
                                <p className="text-zinc-500 text-xs">
                                    {shop?.tagline}
                                </p>
                            </div>
                        </div>

                        <div className="flex items-center justify-between max-md:w-full">
                            <div className="flex items-center">
                                <span className="text-zinc-500">
                                    {translate('rating')}
                                </span>
                                <span className="text-black text-lg sm:text-[28px] ml-2.5 mr-1.5">
                                    {shop?.rating.average.toFixed(1)}
                                </span>
                                <ThemeRating
                                    readOnly
                                    rating={shop?.rating.average}
                                    totalReviews={shop?.rating.total}
                                />
                            </div>

                            <Button
                                variant="secondary"
                                className="h-9 ml-6"
                                onClick={handleChat}
                            >
                                <span>
                                    <FaCommentDots />
                                </span>
                                {translate('Chat now')}
                            </Button>
                        </div>
                    </div>
                </div>

                <div className="flex gap-3 max-md:flex-col items-center justify-between py-4 max-md:px-4">
                    <ul className="max-md:w-full flex max-md:justify-between gap-5 md:gap-14">
                        <li>
                            <NavLink
                                end
                                to={`/shops/${params.shopSlug}`}
                                className={(props) =>
                                    `border-b-2 pb-1 ${
                                        props.isActive
                                            ? 'text-theme-primary border-theme-secondary-light'
                                            : 'border-transparent'
                                    }`
                                }
                            >
                                {translate('home')}
                            </NavLink>
                        </li>
                        <li>
                            <NavLink
                                end
                                to={`/shops/${params.shopSlug}/products`}
                                className={(props) =>
                                    `border-b-2 pb-1 ${
                                        props.isActive
                                            ? 'text-theme-primary border-theme-secondary-light'
                                            : 'border-transparent'
                                    }`
                                }
                            >
                                {translate('All Products')}
                            </NavLink>
                        </li>
                        <li>
                            <NavLink
                                end
                                to={`/shops/${params.shopSlug}/offers`}
                                className={(props) =>
                                    `border-b-2 pb-1 ${
                                        props.isActive
                                            ? 'text-theme-primary border-theme-secondary-light'
                                            : 'border-transparent'
                                    }`
                                }
                            >
                                {translate('offers')}
                            </NavLink>
                        </li>
                        <li>
                            <NavLink
                                to={`/shops/${params.shopSlug}/profile`}
                                className={(props) =>
                                    `border-b-2 pb-1 ${
                                        props.isActive
                                            ? 'text-theme-primary border-theme-secondary-light'
                                            : 'border-transparent'
                                    }`
                                }
                            >
                                {translate('profile')}
                            </NavLink>
                        </li>
                    </ul>

                    <div className="md:max-w-[310px] w-full">
                        <SearchForm
                            name="query"
                            path={`/shops/${params.shopSlug}/products`}
                            suggestions={false}
                            placeholder={translate('Search Products')}
                        />
                    </div>
                </div>
            </div>

            <Outlet />
        </>
    );
};

export default ShopLayout;
