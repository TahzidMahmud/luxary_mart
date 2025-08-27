import { FaLeaf, FaMedal, FaStore, FaSun } from 'react-icons/fa';
import { FaBarsStaggered, FaBoltLightning } from 'react-icons/fa6';
import { Link } from 'react-router-dom';
import { SwiperSlide } from 'swiper/react';
import CategoryCard from '../components/CategoryCard';
import CountDown from '../components/CountDown';
import Image from '../components/Image';
import ProductCard from '../components/card/ProductCard';
import ShopCard from '../components/card/ShopCard';
import ProductSectionSkeleton from '../components/skeletons/ProductSectionSkeleton';
import ProductsSlider from '../components/slider/ProductsSlider';
import ThemeSlider from '../components/slider/ThemeSlider';
import SectionTitle from '../components/titles/SectionTitle';
import {
    useGetAboutUsQuery,
    useGetBannerSlidersQuery,
    useGetCategoryProductsQuery,
    useGetFeaturedCategoriesQuery,
    useGetFeaturedProductsQuery,
    useGetFeaturedShopsQuery,
    useGetFlashSaleProductsQuery,
    useGetFourBannerRowQuery,
    useGetFullWidthBannerQuery,
    useGetMainCategoriesQuery,
    useGetNewArrivalsQuery,
    useGetProductSectionOneQuery,
    useGetProductSectionTwoQuery,
    useGetThreeBannerRowQuery,
    useGetTrendyProductsQuery,
    useGetTwoBannerRowQuery,
    useGetVideosQuery,
} from '../store/features/api/homeApi';
import { togglePopup } from '../store/features/popup/popupSlice';
import { useAppDispatch } from '../store/store';

// ! delete later
export const dummyProduct = {
    id: 1,
    name: 'Samsung S20 Ultra',
    slug: 'samsung-s20-ultra',
    thumbnailImg: '',
    basePrice: 10,
    unit: 'gm',
    stockQty: 60,
    hasVariation: 0,
    rating: {
        average: 0,
        total: 0,
    },
    inWishlist: false,
};

export const dummySeller = {
    id: 1,
    name: 'Admin Shop',
    slug: 'admin-shop',
    shopInfo: null,
    rating: {
        average: 4,
        total: 4.5,
        fiveStarsCount: 0,
        fourStarsCount: 0,
        threeStarsCount: 0,
        twoStarsCount: 0,
        oneStarsCount: 0,
    },
    address: null,
    minOrderAmount: 0,
    defaultShippingCharge: 0,
    logo: '',
    banner: '',
    tagline: '',
};

const Home = () => {
    const dispatch = useAppDispatch();
    const { data: bannerSlider, isLoading: isBannerSliderLoading } =
        useGetBannerSlidersQuery();
    const videosRes = useGetVideosQuery();
    const featuredCategoryRes = useGetFeaturedCategoriesQuery();
    const featuredProductRes = useGetFeaturedProductsQuery();
    const flashSaleProductRes = useGetFlashSaleProductsQuery();
    const productSectionOneRes = useGetProductSectionOneQuery();
    const fullWidthBannerRes = useGetFullWidthBannerQuery();
    const productSectionTwoRes = useGetProductSectionTwoQuery();
    const fourBannerRowRes = useGetFourBannerRowQuery();
    const featuredShopsRes = useGetFeaturedShopsQuery();
    const twoBannerRowRes = useGetTwoBannerRowQuery();
    const newArrivalsRes = useGetNewArrivalsQuery();
    const trendyProductsRes = useGetTrendyProductsQuery();
    const threeBannerRowRes = useGetThreeBannerRowQuery();
    const categoryProductsRes = useGetCategoryProductsQuery();
    const aboutUsRes = useGetAboutUsQuery();
    const mainCategoriesRes = useGetMainCategoriesQuery();

    return (
        <div className="space-y-3 sm:space-y-6 lg:space-y-12">
            {/* start::banner */}
            <div className="theme-container-card no-style" id="banner">
                {isBannerSliderLoading ? (
                    <div className="h-[250px] xl:h-[450px] skeleton"></div>
                ) : (
                    <ThemeSlider
                        slidesPerView={1}
                        spaceBetween={5}
                        arrowClassNames={{
                            prev: 'left-3 md:left-0 translate-x-0 md:-translate-x-1/2',
                            next: 'right-3 md:right-0 translate-x-0 md:translate-x-1/2',
                        }}
                    >
                        {bannerSlider?.map((slider, idx) => (
                            <SwiperSlide key={idx}>
                                <Link to={slider.url} className="w-full h-full">
                                    <img
                                        src={slider.image}
                                        alt=""
                                        className="w-full h-[250px] xl:h-[450px] rounded-md"
                                    />
                                </Link>
                            </SwiperSlide>
                        ))}
                    </ThemeSlider>
                )}
            </div>
            {/* end::banner */}

            <section>
                <div className="theme-container-card grid grid-cols-2 md:grid-cols-4 md:[&_*:not(:first-child)]:pl-5 md:divide-x divide-theme-primary">
                    <div className="flex gap-3 items-center">
                        <img src="/public/images/icons/star-badge.png" alt="" />
                        <p>Imported Products</p>
                    </div>
                    <div className="flex gap-3 items-center">
                        <img
                            src="/public/images/icons/delivery-truck.png"
                            alt=""
                        />
                        <p>Fast Delivery</p>
                    </div>
                    <div className="flex gap-3 items-center">
                        <img src="/public/images/icons/parcel.png" alt="" />
                        <p>Affordable Price</p>
                    </div>
                    <div className="flex gap-3 items-center">
                        <img src="/public/images/icons/money.png" alt="" />
                        <p>Cash on Delivery</p>
                    </div>
                </div>
            </section>

            <section>
                <div className="container grid md:grid-cols-2">
                    {videosRes.data?.map((video) => (
                        <div>
                            <iframe
                                src={`https://www.youtube.com/embed/${video}`}
                                className="w-full aspect-video"
                            ></iframe>
                        </div>
                    ))}
                </div>
            </section>

            {/* start::featured-category */}
            {featuredCategoryRes.isLoading ? (
                <ProductSectionSkeleton />
            ) : featuredCategoryRes.data?.showFeaturedCategories === '1' ? (
                <section
                    className="featured-category-section xs"
                    id="featured-categories"
                >
                    <div className="theme-container-card">
                        <SectionTitle
                            title="Featured Categories"
                            icon={<FaBarsStaggered />}
                            linkText="All Categories"
                            onClick={() => dispatch(togglePopup('categories'))}
                            className="mb-5"
                        />

                        <ThemeSlider
                            arrows
                            slidesPerView={3}
                            spaceBetween={11}
                            breakpoints={{
                                490: {
                                    slidesPerView: 3,
                                },
                                576: {
                                    slidesPerView: 5,
                                },
                                768: {
                                    slidesPerView: 7,
                                },
                            }}
                        >
                            {featuredCategoryRes.data?.categories.map(
                                (category) => (
                                    <SwiperSlide key={category.id}>
                                        <CategoryCard
                                            title={category.name}
                                            to={`/categories/${category.slug}`}
                                            image={category.thumbnailImage}
                                        />
                                    </SwiperSlide>
                                ),
                            )}
                        </ThemeSlider>
                    </div>
                </section>
            ) : null}
            {/* start::featured-category */}

            {/* start::featured-products */}
            {featuredProductRes.isLoading ? (
                <ProductSectionSkeleton withCategoryImage />
            ) : featuredProductRes.data?.showHomeFeaturedProducts === '1' ? (
                <section
                    className="featured-products-section"
                    id="featured-products"
                >
                    <div className="theme-container-card">
                        <SectionTitle
                            title="Featured Products"
                            icon={<FaSun />}
                            linkText="All Products"
                            link={
                                featuredProductRes.data.homeFeaturedProductLink
                            }
                            className="lg:mb-9"
                        />

                        <div className="grid sm:grid-cols-12 gap-2.5 mt-4 lg:mt-8">
                            <div
                                className="sm:col-span-5 md:col-span-4 aspect-square rounded-md bg-cover bg-center w-full h-full"
                                style={{
                                    backgroundImage: `url(${featuredProductRes.data.homeFeaturedProductBanner})`,
                                }}
                            ></div>
                            <ThemeSlider
                                containerClass="sm:col-span-7 md:col-span-8"
                                slidesPerView={2}
                                spaceBetween={10}
                                breakpoints={{
                                    320: {
                                        slidesPerView: 2,
                                        spaceBetween: 5,
                                    },
                                    400: {
                                        slidesPerView: 2,
                                        spaceBetween: 10,
                                    },
                                    992: {
                                        slidesPerView: 3,
                                    },
                                }}
                            >
                                {featuredProductRes.data.products.map(
                                    (product) => (
                                        <SwiperSlide key={product.id}>
                                            <ProductCard product={product} />
                                        </SwiperSlide>
                                    ),
                                )}
                            </ThemeSlider>
                        </div>
                    </div>
                </section>
            ) : null}
            {/* start::featured-products */}

            {flashSaleProductRes.isLoading ? (
                <ProductSectionSkeleton />
            ) : flashSaleProductRes.data?.showHomeFlashSale === '1' &&
              flashSaleProductRes.data.flashSaleCampaign ? (
                <section className="" id="flash-sale-products">
                    <Link
                        to={`/campaigns/${flashSaleProductRes.data.flashSaleCampaign?.slug}`}
                        className="flex justify-center bg-[#FFD46E] h-[385px]"
                    >
                        <img
                            src={
                                flashSaleProductRes.data.flashSaleCampaign
                                    ?.banner
                            }
                            alt=""
                            className="w-full max-h-[390px]"
                        />
                    </Link>

                    <div className="theme-container-card p-8 relative z-[1] -mt-[90px]">
                        <div className="flex justify-between gap-4 mb-9">
                            <SectionTitle
                                className="mb-0"
                                title={
                                    flashSaleProductRes.data.flashSaleCampaign
                                        ?.name
                                }
                                icon={
                                    <span className="text-theme-orange">
                                        <FaBoltLightning />
                                    </span>
                                }
                            />

                            <CountDown
                                className="mt-0"
                                label="This Deal Ends In"
                                classNames={{
                                    label: 'max-xs:hidden',
                                }}
                                date={
                                    +flashSaleProductRes.data.flashSaleCampaign
                                        ?.endDate * 1000
                                }
                            />
                        </div>

                        <ProductsSlider withDefaultBreakpoints>
                            {flashSaleProductRes.data.flashSaleProducts.map(
                                (product) => (
                                    <SwiperSlide key={product.id}>
                                        <ProductCard product={product} />
                                    </SwiperSlide>
                                ),
                            )}
                        </ProductsSlider>
                    </div>
                </section>
            ) : null}

            {/* start::product section 1 */}
            {productSectionOneRes.isLoading ? (
                <ProductSectionSkeleton />
            ) : productSectionOneRes.data?.showHomeProductSectionOne === '1' ? (
                <section className="" id="product-section-1">
                    <div className="theme-container-card">
                        <SectionTitle
                            title={
                                productSectionOneRes.data
                                    .homeProductSectionOneTitle
                            }
                            icon={<FaMedal />}
                            className="mb-9"
                        />

                        <ProductsSlider>
                            {productSectionOneRes.data.products.map(
                                (product) => (
                                    <SwiperSlide key={product.id}>
                                        <ProductCard product={product} />
                                    </SwiperSlide>
                                ),
                            )}
                        </ProductsSlider>
                    </div>
                </section>
            ) : null}
            {/* start::product section 1 */}

            {fullWidthBannerRes.isLoading ? (
                <div className="h-[230px] skeleton"></div>
            ) : fullWidthBannerRes.data?.length ? (
                <div
                    className="theme-container-card no-style"
                    id="full-width-banner"
                >
                    <ThemeSlider
                        spaceBetween={5}
                        arrowClassNames={{
                            prev: 'left-3 md:left-0 translate-x-0 md:-translate-x-1/2',
                            next: 'right-3 md:right-0 translate-x-0 md:translate-x-1/2',
                        }}
                    >
                        {fullWidthBannerRes.data?.map((banner, idx) => (
                            <SwiperSlide key={idx}>
                                <Link to={banner.url} className="w-full">
                                    <img
                                        src={banner.image}
                                        alt=""
                                        className="rounded-md w-full h-[230px]"
                                        key={idx}
                                    />
                                </Link>
                            </SwiperSlide>
                        ))}
                    </ThemeSlider>
                </div>
            ) : null}

            {/* start::product section 2 */}
            {productSectionTwoRes.data?.showHomeProductSectionTwo === '1' ? (
                <section className="" id="product-section-2">
                    <div className="theme-container-card">
                        <SectionTitle
                            title={
                                productSectionTwoRes.data
                                    .homeProductSectionTwoTitle
                            }
                            icon={<FaMedal />}
                            linkText="All Products"
                            link="/products"
                            className="mb-9"
                        />

                        <ProductsSlider>
                            {productSectionTwoRes.data.products.map(
                                (product) => (
                                    <SwiperSlide key={product.id}>
                                        <ProductCard product={product} />
                                    </SwiperSlide>
                                ),
                            )}
                        </ProductsSlider>
                    </div>
                </section>
            ) : null}
            {/* start::Best Sellers */}

            {fourBannerRowRes.data?.length ? (
                <div className="theme-container-card no-style grid md:grid-cols-2 gap-[5px]">
                    {fourBannerRowRes.data?.map((banner, idx) => (
                        <Link to={banner.url} key={idx}>
                            <img
                                src={banner.image}
                                alt=""
                                className="rounded-md w-full"
                            />
                        </Link>
                    ))}
                </div>
            ) : null}

            {featuredShopsRes.data?.showFeaturedShops === '1' &&
            window.config.generalSettings.appMode == 'multiVendor' ? (
                <section className="">
                    <div className="theme-container-card">
                        <SectionTitle
                            title="Featured Shops"
                            icon={<FaStore />}
                            linkText="View All Shops"
                            link="/shops"
                            className="mb-9"
                        />

                        <ProductsSlider
                            spaceBetween={14}
                            breakpoints={{
                                1024: {
                                    slidesPerView: 6,
                                },
                            }}
                        >
                            {featuredShopsRes.data.shops.map((shop) => (
                                <SwiperSlide key={shop.id}>
                                    <ShopCard shop={shop} />
                                </SwiperSlide>
                            ))}
                        </ProductsSlider>
                    </div>
                </section>
            ) : null}

            {twoBannerRowRes.data?.length ? (
                <div className="theme-container-card no-style grid md:grid-cols-2 gap-3">
                    {twoBannerRowRes.data?.map((banner, idx) => (
                        <Link to={banner.url} key={idx}>
                            <img
                                src={banner.image}
                                alt=""
                                className="rounded-md w-full"
                            />
                        </Link>
                    ))}
                </div>
            ) : null}

            {newArrivalsRes.data?.length ? (
                <section className="">
                    <div className="theme-container-card">
                        <SectionTitle
                            title="New Arrivals"
                            icon={<FaLeaf />}
                            linkText="All Products"
                            link="/products?sortBy=newest"
                            className="mb-9"
                        />

                        <ProductsSlider>
                            {newArrivalsRes.data?.map((product) => (
                                <SwiperSlide key={product.id}>
                                    <ProductCard product={product} />
                                </SwiperSlide>
                            ))}
                        </ProductsSlider>
                    </div>
                </section>
            ) : null}
            {trendyProductsRes.data?.showHomeTrendyProducts ? (
                <section className="">
                    <div className="theme-container-card">
                        <SectionTitle
                            title="Trendy Products"
                            icon={<FaLeaf />}
                            linkText="All Products"
                            link="/products?sortBy=newest"
                            className="mb-9"
                        />

                        <ProductsSlider>
                            {trendyProductsRes.data.products?.map((product) => (
                                <SwiperSlide key={product.id}>
                                    <ProductCard product={product} />
                                </SwiperSlide>
                            ))}
                        </ProductsSlider>
                    </div>
                </section>
            ) : null}

            {threeBannerRowRes.data?.length ? (
                <div className="theme-container-card no-style grid md:grid-cols-2 gap-[5px]">
                    {threeBannerRowRes.data?.map((banner, idx) => (
                        <Link to={banner.url} key={idx}>
                            <img src={banner.image} alt="" className="w-full" />
                        </Link>
                    ))}
                </div>
            ) : null}

            {/* start::category product sections */}
            {categoryProductsRes.data?.map((item) => (
                <section className="" key={item.category.id}>
                    <div className="theme-container-card">
                        <SectionTitle
                            title={item.category.name}
                            linkText="All Products"
                            link={`/categories/${item.category.slug}`}
                        />
                        <div className="flex max-md:flex-col gap-2.5 mt-8">
                            <img
                                src={item.image}
                                alt=""
                                className="rounded-md max-md:hidden"
                            />
                            <ThemeSlider
                                slidesPerView={2}
                                spaceBetween={10}
                                breakpoints={{
                                    320: {
                                        slidesPerView: 2,
                                        spaceBetween: 5,
                                    },
                                    992: {
                                        slidesPerView: 3,
                                    },
                                    1280: {
                                        slidesPerView: 4,
                                    },
                                }}
                            >
                                {item.products.map((product) => (
                                    <SwiperSlide key={product.id}>
                                        <ProductCard product={product} />
                                    </SwiperSlide>
                                ))}
                            </ThemeSlider>
                        </div>
                    </div>
                </section>
            ))}
            {/* end::category product sections */}

            {aboutUsRes.data?.showHomeAboutUsSection === '1' ? (
                <div className="theme-container-card no-style px-0">
                    <section className="bg-theme-secondary-light text-white rounded-md">
                        <div className="grid lg:grid-cols-12 max-w-[1000px] px-5 mx-auto">
                            <div className="lg:col-span-7 py-[56px]">
                                <h2 className="arm-h2">
                                    {aboutUsRes.data.homeAboutUsTitle}
                                </h2>
                                <p className="uppercase">
                                    {aboutUsRes.data.homeAboutUsSubTitle}
                                </p>

                                <p className="leading-[19px] text-justify mt-4">
                                    {aboutUsRes.data.homeAboutUsText}
                                </p>
                            </div>
                            <div className="max-lg:hidden col-span-5 relative">
                                <img
                                    src={aboutUsRes.data.homeAboutUsImage}
                                    alt=""
                                    className="absolute bottom-0 left-0 max-w-full max-h-[calc(100%+30px)]"
                                />
                            </div>
                        </div>
                    </section>
                </div>
            ) : null}

            {mainCategoriesRes.data?.showHomeMainCategories === '1' ? (
                <section>
                    <div className="theme-container-card grid sm:grid-cols-2 lg:grid-cols-4 gap-0 sm:gap-4">
                        {mainCategoriesRes.data.categories.map((category) => (
                            <div
                                className="py-5 md:py-8 max-lg:border-b lg:border-r border-theme-primary/[.14] pr-2.5 last:border-none"
                                key={category.id}
                            >
                                <h3 className="font-public-sans mb-2.5">
                                    <Link
                                        to={`/categories/${category.slug}`}
                                        className="flex items-center gap-2 text-zinc-500"
                                    >
                                        <span>
                                            <Image
                                                src={category.thumbnailImage}
                                                alt=""
                                                className="rounded-full"
                                                height={20}
                                                width={20}
                                            />
                                        </span>
                                        {category.name}
                                    </Link>
                                </h3>

                                {category.children_categories.map(
                                    (child, idx) => (
                                        <span key={child.id}>
                                            <Link
                                                to={`/categories/${child.slug}`}
                                                className="text-neutral-400 text-sm hover:text-theme-secondary-light"
                                            >
                                                {child.name}
                                            </Link>
                                            {idx !==
                                            category.children_categories
                                                .length -
                                                1 ? (
                                                <span className="mx-2 h-full border-r border-gray-300"></span>
                                            ) : null}
                                        </span>
                                    ),
                                )}
                            </div>
                        ))}
                    </div>
                </section>
            ) : null}
        </div>
    );
};

export default Home;
