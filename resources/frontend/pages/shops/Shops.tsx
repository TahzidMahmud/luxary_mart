import { useEffect } from 'react';
import { FaHome, FaStore } from 'react-icons/fa';
import { useSearchParams } from 'react-router-dom';
import { SwiperSlide } from 'swiper/react';
import Breadcrumb from '../../components/Breadcrumb';
import ShopCard from '../../components/card/ShopCard';
import SearchForm from '../../components/inputs/SearchForm';
import Pagination from '../../components/pagination/Pagination';
import { CategorySkeleton } from '../../components/skeletons/from-svg';
import ThemeSlider from '../../components/slider/ThemeSlider';
import SectionTitle from '../../components/titles/SectionTitle';
import { useGetFeaturedShopsQuery } from '../../store/features/api/homeApi';
import { useLazyGetShopsQuery } from '../../store/features/api/shopApi';
import { IGetsQueryParams } from '../../types';
import { translate } from '../../utils/translate';

const Shops = () => {
    const [searchParams] = useSearchParams();
    const { data: featuredShopsRes, isLoading: isFeaturedShopsLoading } =
        useGetFeaturedShopsQuery();
    const [getShops, { data, isLoading }] = useLazyGetShopsQuery();

    const filters: IGetsQueryParams = {
        page: searchParams.get('page') || 1,
        query: searchParams.get('query') || '',
    };

    useEffect(() => {
        getShops(filters, true);
    }, [filters]);

    const shops = data?.result.data;

    return (
        <>
            <Breadcrumb
                title={translate('All Shops')}
                navigation={[
                    {
                        icon: <FaHome />,
                        name: translate('Home'),
                        link: '/',
                    },
                    {
                        name: translate('shops'),
                    },
                ]}
            />

            <section className="mt-6">
                <div className="theme-container-card">
                    <SectionTitle
                        title={translate('Featured Shops')}
                        icon={<FaStore />}
                        className="mb-9"
                    />

                    <ThemeSlider
                        slidesPerView={2}
                        spaceBetween={14}
                        breakpoints={{
                            480: {
                                slidesPerView: 3,
                            },
                            640: {
                                slidesPerView: 4,
                            },
                            768: {
                                slidesPerView: 6,
                            },
                        }}
                    >
                        {isFeaturedShopsLoading
                            ? [...Array(6)].map((_, i) => (
                                  <SwiperSlide key={i}>
                                      <CategorySkeleton
                                          key={i}
                                          className="animate-pulse"
                                      />
                                  </SwiperSlide>
                              ))
                            : featuredShopsRes?.shops?.map((shop) => (
                                  <SwiperSlide key={shop.id}>
                                      <ShopCard shop={shop} key={shop.id} />
                                  </SwiperSlide>
                              ))}
                    </ThemeSlider>
                </div>
            </section>

            <div className="theme-container-card no-style flex items-center justify-between mt-7">
                <div className="w-full max-w-[280px]">
                    <SearchForm
                        searchOnType
                        suggestions={false}
                        path="/shops"
                        placeholder={translate('Search The Shops')}
                    />
                </div>

                <div></div>
            </div>

            <section className="mt-6">
                <div className="theme-container-card !py-10">
                    <div className="grid grid-cols-2 xs:grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-[14px]">
                        {isLoading ? (
                            [...Array(18)].map((_, i) => (
                                <SwiperSlide key={i}>
                                    <CategorySkeleton
                                        key={i}
                                        className="animate-pulse"
                                    />
                                </SwiperSlide>
                            ))
                        ) : !shops?.length ? (
                            <div className="col-span-6 text-center text-gray-500 ">
                                <p className="my-10">
                                    {translate('No shops found')}
                                </p>
                            </div>
                        ) : (
                            shops?.map((shop) => (
                                <ShopCard shop={shop} key={shop.id} />
                            ))
                        )}
                    </div>

                    {data && data.result.meta.last_page > 1 && (
                        <Pagination pagination={data.result.meta} />
                    )}
                </div>
            </section>
        </>
    );
};

export default Shops;
