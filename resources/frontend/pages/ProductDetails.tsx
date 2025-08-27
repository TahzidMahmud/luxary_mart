import { useEffect, useState } from 'react';
import { FaCog, FaCoins, FaHome, FaPiggyBank, FaTruck } from 'react-icons/fa';
import { useDispatch } from 'react-redux';
import { Link, useParams, useSearchParams } from 'react-router-dom';
import { SwiperSlide } from 'swiper/react';
import Breadcrumb from '../components/Breadcrumb';
import Image from '../components/Image';
import Button from '../components/buttons/Button';
import ProductCard from '../components/card/ProductCard';
import ThemeLabel from '../components/card/ThemeLabel';
import Pagination from '../components/pagination/Pagination';
import ProductPreviewContent from '../components/popups/product/ProductPreviewContent';
import ReviewItem from '../components/reviews/ReviewItem';
import ThemeRating from '../components/reviews/ThemeRating';
import {
    ProductPreviewSkeleton,
    ProductPreviewSkeletonMobile,
} from '../components/skeletons/from-svg';
import ProductsSlider from '../components/slider/ProductsSlider';
import SectionTitle from '../components/titles/SectionTitle';
import { useGetProductDetailsQuery } from '../store/features/api/productApi';
import { useLazyGetAllProductReviewsQuery } from '../store/features/api/reviewApi';
import { togglePopup } from '../store/features/popup/popupSlice';
import { IBreadcrumbNavigation } from '../types';
import { numberFormatter, paddedNumber } from '../utils/numberFormatter';
import { translate } from '../utils/translate';
import NotFound from './NotFound';

const ProductDetails = () => {
    const dispatch = useDispatch();
    const [searchParams] = useSearchParams();
    const [starPercentage, setStarPercentage] = useState([0, 0, 0, 0, 0]);
    const [activeDetailsTab, setActiveDetailsTab] = useState<
        'description' | 'review'
    >('description');
    const { slug } = useParams<{ slug: string }>();
    const { data: product, isLoading } = useGetProductDetailsQuery(slug!);
    const [getProductReviews, { data: reviewsRes }] =
        useLazyGetAllProductReviewsQuery();

    const reviews = reviewsRes?.reviews.data;
    const reviewPagination = reviewsRes?.reviews.meta;
    const summary = reviewsRes?.summary;

    useEffect(() => {
        if (!product) return;
        getProductReviews({
            productId: product.id,
            page: searchParams.get('page') || 1,
            limit: searchParams.get('limit') || 10,
        });
    }, [product]);

    useEffect(() => {
        if (!summary) return;

        const starPercentage = [
            summary.fiveStarsCount,
            summary.fourStarsCount,
            summary.threeStarsCount,
            summary.twoStarsCount,
            summary.oneStarsCount,
        ].map((count) => {
            return (count! / summary.total) * 100 || 0;
        });

        setStarPercentage(starPercentage);
    }, [summary]);

    if (isLoading) {
        return (
            <section className="mt-5">
                <div className="theme-container-card">
                    <div className="grid lg:grid-cols-4">
                        <div className="lg:col-span-3">
                            <ProductPreviewSkeleton className="mx-auto max-md:hidden" />
                            <ProductPreviewSkeletonMobile className="md:hidden w-full" />
                        </div>
                    </div>
                </div>
            </section>
        );
    }

    if (!product) {
        return <NotFound />;
    }

    const breadCrumbNavigationArray: IBreadcrumbNavigation[] = [
        {
            icon: <FaHome />,
            name: translate('home'),
            link: '/',
        },
        {
            name: translate('Products'),
            link: '/products',
        },
    ];

    if (product.rootCategory) {
        breadCrumbNavigationArray.push({
            name: product.rootCategory?.name,
            link: `/categories/${product.rootCategory?.slug}`,
        });
    }
    breadCrumbNavigationArray.push({
        name: product.name,
    });

    return (
        <>
            <Breadcrumb
                className="max-sm:hidden"
                navigation={breadCrumbNavigationArray}
            />

            <section className="mt-5">
                <div className="theme-container-card">
                    <div className="grid lg:grid-cols-4">
                        <div className="lg:col-span-3">
                            <ProductPreviewContent slug={product.slug} />
                        </div>
                        <div className="border border-zinc-100 rounded-md p-4">
                            {window.config.generalSettings.appMode ===
                                'multiVendor' && (
                                <div className="mb-4">
                                    <h5 className="text-[11px] text-neutral-400 font-public-sans mb-3 uppercase">
                                        {translate('Seller')}
                                    </h5>

                                    <div className="flex items-center gap-2 bg-stone-50 rounded-md border border-zinc-100 py-[19px] px-[15px]">
                                        <div className="bg-white w-[85px]">
                                            <Link
                                                to={`/shops/${product.shop.slug}`}
                                                className="h-full block"
                                            >
                                                <Image
                                                    src={product.shop.logo}
                                                    alt={product.shop.name}
                                                    className="h-full"
                                                />
                                            </Link>
                                        </div>
                                        <div className="">
                                            <h4 className="font-public-sans text-sm">
                                                <Link
                                                    to={`/shops/${product.shop.slug}`}
                                                >
                                                    {product.shop.name}
                                                </Link>
                                            </h4>
                                            <ThemeRating
                                                rating={
                                                    product.shop.rating.average
                                                }
                                                totalReviews={
                                                    product.shop.rating.total
                                                }
                                                className="mt-[7px] mb-[11px]"
                                            />
                                            <Button
                                                as={'link'}
                                                to={`/shops/${product.shop.slug}`}
                                                variant="secondary"
                                            >
                                                {translate('Visit Shop')}
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                            )}

                            <h5 className="text-[11px] text-neutral-400 font-public-sans mb-3 uppercase">
                                {translate('Service')}
                            </h5>

                            <div className="space-y-2">
                                <div className="flex items-center gap-5 bg-stone-50 rounded-md border border-zinc-100 px-4 py-3">
                                    <div className="text-2xl text-neutral-400">
                                        <FaTruck />
                                    </div>
                                    <div>
                                        <h5 className="text-neutral-400 text-xs">
                                            {translate('Estimated Delivery')}
                                        </h5>
                                        <p className="text-neutral-700 text-sm">
                                            {product.deliveryHours}
                                        </p>
                                    </div>
                                </div>
                                <div className="flex items-center gap-5 bg-stone-50 rounded-md border border-zinc-100 px-4 py-3">
                                    <div className="text-2xl text-neutral-400">
                                        <FaCoins />
                                    </div>
                                    <div>
                                        <h5 className="text-neutral-400 text-xs">
                                            {translate('Cash On Delivery')}
                                        </h5>
                                        <p className="text-neutral-700 text-sm">
                                            {product.codAvailable
                                                ? translate('Available')
                                                : translate('Not Available')}
                                        </p>
                                    </div>
                                </div>
                                <div className="flex items-center gap-5 bg-stone-50 rounded-md border border-zinc-100 px-4 py-3">
                                    <div className="text-2xl text-neutral-400">
                                        <FaPiggyBank />
                                    </div>
                                    <div>
                                        <h5 className="text-neutral-400 text-xs">
                                            {translate('EMI Facility')}
                                        </h5>
                                        <p className="text-neutral-700 text-sm">
                                            {product.hasEmi
                                                ? product.emiInfo
                                                : translate('Not Available')}
                                        </p>
                                    </div>
                                </div>
                                <div className="flex items-center gap-5 bg-stone-50 rounded-md border border-zinc-100 px-4 py-3">
                                    <div className="text-2xl text-neutral-400">
                                        <FaCog />
                                    </div>
                                    <div>
                                        <h5 className="text-neutral-400 text-xs">
                                            {translate('Warranty')}
                                        </h5>
                                        <p className="text-neutral-700 text-sm">
                                            {product.hasWarranty
                                                ? product.warrantyInfo
                                                : translate('Not Available')}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {product.promoCode ? (
                                <>
                                    <h5 className="text-[11px] text-neutral-400 font-public-sans mb-2 mt-4 uppercase">
                                        {translate('Coupon Code')}
                                    </h5>

                                    <button
                                        onClick={() =>
                                            dispatch(
                                                togglePopup({
                                                    popup: 'coupon-details',
                                                    popupProps: {
                                                        couponCode:
                                                            product.promoCode!
                                                                .code,
                                                    },
                                                }),
                                            )
                                        }
                                    >
                                        <ThemeLabel
                                            label={{
                                                text: product.promoCode.code,
                                                variant: 'warning',
                                            }}
                                        />
                                    </button>
                                </>
                            ) : null}
                        </div>
                    </div>
                </div>
            </section>

            <section className="mt-10">
                <div className="theme-container-card">
                    <div className="border-b flex gap-6 mb-5">
                        <Button
                            variant="link"
                            onClick={() => setActiveDetailsTab('description')}
                            className={`border-b-2 border-transparent rounded-none px-0 ${
                                activeDetailsTab === 'description'
                                    ? 'border-theme-primary'
                                    : ''
                            }`}
                        >
                            {translate('Description')}
                        </Button>
                        <Button
                            variant="link"
                            onClick={() => setActiveDetailsTab('review')}
                            className={`border-b-2 border-transparent rounded-none px-0 ${
                                activeDetailsTab === 'review'
                                    ? 'border-theme-primary'
                                    : ''
                            }`}
                        >
                            {translate('Customer Review')}
                        </Button>
                    </div>

                    {activeDetailsTab === 'description' ? (
                        <div
                            dangerouslySetInnerHTML={{
                                __html: product.description,
                            }}
                        ></div>
                    ) : null}

                    {activeDetailsTab === 'review' ? (
                        <>
                            <div className="flex max-sm:flex-col gap-8 sm:gap-[64px]">
                                <div className="pr-12 border-r border-theme-primary-14">
                                    <h4 className="text-sm font-public-sans mb-4 sm:mb-9 uppercase">
                                        {translate('Summary')}
                                    </h4>

                                    <div className="flex gap-6">
                                        <div>
                                            <h4 className="text-3xl mb-1.5">
                                                {summary?.average.toFixed(2)}
                                            </h4>
                                            <ThemeRating
                                                readOnly
                                                rating={summary?.average!}
                                            />
                                        </div>

                                        <div>
                                            <h4 className="text-3xl mb-1.5">
                                                {paddedNumber(summary?.total)}
                                            </h4>
                                            <p className="arm-h4 text-zinc-500">
                                                {translate('Total Reviews')}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div className="grow space-y-2.5">
                                    {starPercentage.map((percentage, index) => (
                                        <div
                                            className="flex items-center gap-2 max-w-[530px] "
                                            key={index}
                                        >
                                            <span className="w-10 text-black/[.6]">
                                                {5 - index} {translate('Star')}
                                            </span>
                                            <span className="grow inline-flex bg-black/5 rounded-sm overflow-hidden h-2">
                                                <span
                                                    className="bg-theme-orange h-2 inline-block"
                                                    style={{
                                                        width: `${percentage}%`,
                                                    }}
                                                ></span>
                                            </span>
                                            <span className="w-10 text-black/[.38]">
                                                {numberFormatter(percentage)}%
                                            </span>
                                        </div>
                                    ))}
                                </div>
                            </div>

                            <hr className="mt-9 mb-7" />

                            <div>
                                <h4 className="text-sm font-public-sans mb-7 uppercase">
                                    {translate('Reviews')}
                                </h4>

                                {reviews?.map((review) => (
                                    <div key={review.id}>
                                        <ReviewItem review={review} />
                                        <hr className="mt-4 mb-8" />
                                    </div>
                                ))}

                                <Pagination pagination={reviewPagination} />
                            </div>
                        </>
                    ) : null}
                </div>
            </section>

            <section className="mt-6">
                <div className="theme-container-card">
                    <SectionTitle
                        title={translate('Related Products')}
                        className="mb-8"
                        link={
                            product.rootCategory
                                ? `/categories/${product.rootCategory?.slug}`
                                : '/products'
                        }
                        linkText={translate('All Products')}
                    />

                    <ProductsSlider>
                        {product.relatedProducts.map((product) => (
                            <SwiperSlide key={product.id}>
                                <ProductCard product={product} />
                            </SwiperSlide>
                        ))}
                    </ProductsSlider>
                </div>
            </section>
        </>
    );
};

export default ProductDetails;
