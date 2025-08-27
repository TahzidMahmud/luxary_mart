import { FaHome } from 'react-icons/fa';
import Breadcrumb from '../../components/Breadcrumb';
import CountDown from '../../components/CountDown';
import ProductCard from '../../components/card/ProductCard';
import SectionTitle from '../../components/titles/SectionTitle';
import { translate } from '../../utils/translate';
import { dummyProduct } from '../Home';

const CampaignDetailsV2 = () => {
    return (
        <>
            <Breadcrumb
                title="Mega Campaigns"
                navigation={[
                    { name: translate('home'), link: '/', icon: <FaHome /> },
                    { name: 'Campaigns', link: '/campaigns' },
                    { name: 'Mega Campaigns' },
                ]}
            />

            <section>
                <div className="theme-container-card no-style grid grid-cols-2">
                    <div>
                        <img
                            src="/images/campaigns/1.png"
                            alt=""
                            className="w-full rounded-md"
                        />
                    </div>
                    <div className="flex flex-col justify-center bg-white rounded-md px-12">
                        <h1 className="arm-h2 mb-2.5">
                            TOP AND TUNICS MEGA FEST
                        </h1>
                        <p className="text-sm">
                            ARM Ecommerce Bangladesh is a prominent online
                            shopping site headquartered in Bangladesh, with a
                            presence across various Asian countries, including
                            Singapore
                        </p>

                        <CountDown
                            label="This Deal Ends In"
                            date={'2024-01-05T12:12:41.909Z'}
                            className="sm:mt-7"
                        />
                    </div>
                </div>
            </section>

            <section className="mt-9">
                <div className="theme-container-card">
                    <SectionTitle title="Men’s Product" className="mb-7" />

                    <div className="grid grid-cols-5">
                        <ProductCard product={dummyProduct} />
                        <ProductCard product={dummyProduct} />
                        <ProductCard product={dummyProduct} />
                        <ProductCard product={dummyProduct} />
                        <ProductCard product={dummyProduct} />
                    </div>
                </div>
            </section>

            <section className="mt-9">
                <div className="theme-container-card">
                    <SectionTitle title="Women’s Product" className="mb-7" />

                    <div className="grid grid-cols-5">
                        <ProductCard product={dummyProduct} />
                        <ProductCard product={dummyProduct} />
                        <ProductCard product={dummyProduct} />
                        <ProductCard product={dummyProduct} />
                        <ProductCard product={dummyProduct} />
                    </div>
                </div>
            </section>
        </>
    );
};

export default CampaignDetailsV2;
