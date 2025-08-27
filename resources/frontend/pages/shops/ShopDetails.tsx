import { Link, useParams } from 'react-router-dom';
import { SwiperSlide } from 'swiper/react';
import ProductCard from '../../components/card/ProductCard';
import ShopHomeSkeleton from '../../components/skeletons/shop/ShopHomeSkeleton';
import ThemeSlider from '../../components/slider/ThemeSlider';
import SectionTitle from '../../components/titles/SectionTitle';
import { useGetShopSectionsQuery } from '../../store/features/api/shopApi';
import {
    IBoxedWidthBanner,
    IFullWidthBanner,
    IShopProductsSection,
} from '../../types/shop';
import { translate } from '../../utils/translate';

const ShopDetails = () => {
    const params = useParams<{ shopSlug: string }>();
    const { data: shopSectionsRes, isFetching } = useGetShopSectionsQuery(
        params.shopSlug!,
    );
    const shopSections = shopSectionsRes?.result;

    if (isFetching) {
        return <ShopHomeSkeleton />;
    }

    const getBoxedWidthBannerSection = (section: IBoxedWidthBanner) => (
        <section key={section.type}>
            <div className="theme-container-card">
                <div className="grid xs:grid-cols-2 gap-3">
                    <ThemeSlider
                        loop
                        autoplay
                        arrows={false}
                        slidesPerView={1}
                        spaceBetween={3}
                    >
                        {section.values.box1Banners.map((banner) => (
                            <SwiperSlide key={banner}>
                                <Link
                                    to={section.values.box1Link}
                                    className="block"
                                >
                                    <img
                                        src={banner}
                                        alt=""
                                        className="w-full rounded-md"
                                    />
                                </Link>
                            </SwiperSlide>
                        ))}
                    </ThemeSlider>
                    <ThemeSlider
                        loop
                        autoplay
                        arrows={false}
                        slidesPerView={1}
                        spaceBetween={3}
                    >
                        {section.values.box2Banners.map((banner) => (
                            <SwiperSlide key={banner}>
                                <Link
                                    to={section.values.box2Link}
                                    className="w-full"
                                >
                                    <img
                                        src={banner}
                                        alt=""
                                        className="w-full"
                                    />
                                </Link>
                            </SwiperSlide>
                        ))}
                    </ThemeSlider>
                </div>
            </div>
        </section>
    );

    const getProductsSection = (section: IShopProductsSection) => (
        <section key={section.type}>
            <div className="theme-container-card">
                <SectionTitle
                    title={section.title}
                    linkText={translate('All Products')}
                    link={`/shops/${params.shopSlug}/products`}
                    className="mb-9"
                />

                <div className="grid grid-cols-2 xs:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-5">
                    {section.values.map((product) => (
                        <ProductCard product={product} key={product.id} />
                    ))}
                </div>
            </div>
        </section>
    );

    const getFullWidthBannerSection = (section: IFullWidthBanner) => (
        <section key={section.type}>
            <ThemeSlider loop autoplay arrows={false} slidesPerView={1}>
                {section.values.banners.map((banner) => (
                    <SwiperSlide key={banner}>
                        <Link to={section.values.link} className="w-full block">
                            <img
                                src={banner}
                                alt=""
                                className="w-full rounded-md"
                            />
                        </Link>
                    </SwiperSlide>
                ))}
            </ThemeSlider>
        </section>
    );

    return (
        <div className="space-y-5 sm:space-y-7 md:space-y-10">
            {shopSections?.map((section) => {
                switch (section.type) {
                    case 'boxed-width-banner':
                        return getBoxedWidthBannerSection(section);
                    case 'products':
                        return getProductsSection(section);
                    case 'full-width-banner':
                        return getFullWidthBannerSection(section);
                    default:
                        return null;
                }
            })}
            <section>
                <div className="theme-container-card">
                    <SectionTitle
                        title={translate('Just For You')}
                        linkText={translate('All Products')}
                        link={`/shops/${params.shopSlug}/products`}
                        className="mb-9"
                    />

                    <div className="grid grid-cols-2 xs:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-5">
                        {shopSectionsRes?.justForYouProducts.map((product) => (
                            <ProductCard product={product} key={product.id} />
                        ))}
                    </div>
                </div>
            </section>
        </div>
    );
};

export default ShopDetails;
