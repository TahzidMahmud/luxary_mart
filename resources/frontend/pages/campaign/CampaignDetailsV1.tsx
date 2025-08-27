import { FaHome } from 'react-icons/fa';
import { useParams, useSearchParams } from 'react-router-dom';
import Breadcrumb from '../../components/Breadcrumb';
import CountDown from '../../components/CountDown';
import ProductCard from '../../components/card/ProductCard';
import Pagination from '../../components/pagination/Pagination';
import ProductCardSkeleton from '../../components/skeletons/ProductCardSkeleton';
import SectionTitle from '../../components/titles/SectionTitle';
import { useGetCampaignDetailsQuery } from '../../store/features/api/campaignApi';
import { translate } from '../../utils/translate';

const CampaignDetailsV1 = () => {
    const params = useParams<{ slug: string }>();
    const [searchParams] = useSearchParams();
    const { data: campaignRes, isLoading } = useGetCampaignDetailsQuery({
        slug: params.slug!,
        page: searchParams.get('page') || 1,
        limit: searchParams.get('limit') || 10,
    });

    const campaign = campaignRes?.campaign;
    const products = campaignRes?.products.data;
    const pagination = campaignRes?.products.meta;

    return (
        <>
            <Breadcrumb
                navigation={[
                    { name: translate('Home'), link: '/', icon: <FaHome /> },
                    { name: translate('Campaigns'), link: '/campaigns' },
                    { name: campaign?.name || '' },
                ]}
                className="mb-0"
            />

            <section>
                <img
                    src={campaign?.banner}
                    alt={campaign?.name}
                    className="h-[300px] w-full object-cover bg-gray-300"
                />
            </section>

            <section className="mt-10 text-center">
                <div className="theme-container-card">
                    <h3 className="arm-h2">{campaign?.name}</h3>
                    <p className="my-4 max-w-[700px] mx-auto">
                        {campaign?.shortDescription}
                    </p>

                    <CountDown
                        label="This Deal Ends In"
                        className="sm:mt-7 justify-center"
                        date={Number(campaign?.endDate) * 1000}
                    />
                </div>
            </section>

            <section className="mt-9">
                <div className="theme-container-card">
                    <SectionTitle
                        title={campaign?.name || ''}
                        className="mb-7"
                    />

                    <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
                        {isLoading ? (
                            Array.from({ length: 5 }).map((_, index) => (
                                <ProductCardSkeleton key={index} />
                            ))
                        ) : !products?.length ? (
                            <div className="col-span-5 py-5 text-center">
                                {translate('No Product Found')}
                            </div>
                        ) : (
                            products?.map((product) => (
                                <ProductCard
                                    product={product}
                                    key={product.id}
                                />
                            ))
                        )}
                    </div>

                    <Pagination pagination={pagination} className="mt-6 pt-5" />
                </div>
            </section>
        </>
    );
};

export default CampaignDetailsV1;
