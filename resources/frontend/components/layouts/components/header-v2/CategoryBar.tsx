import { FaHome, FaLeaf, FaStore } from 'react-icons/fa';
import { FaApple, FaBolt, FaFire, FaShirt, FaTag } from 'react-icons/fa6';
import { Link } from 'react-router-dom';
import { translate } from '../../../../utils/translate';

const CategoryBar = () => {
    return (
        <div className="h-[57px] flex items-center overflow-x-auto no-scrollbar whitespace-nowrap">
            <div className="container bg-transparent flex items-center justify-between gap-6 xs:gap-8">
                <Link to="/" className="group inline-flex items-center gap-2">
                    <span className="text-lg text-stone-300 transition-all group-hover:text-theme-secondary">
                        <FaHome />
                    </span>
                    <span className="arm-h3">{translate('home')}</span>
                </Link>
                {window.config.generalSettings.appMode === 'multiVendor' && (
                    <Link
                        to="/shops"
                        className="group inline-flex items-center gap-2"
                    >
                        <span className="text-lg text-stone-300 transition-all group-hover:text-theme-secondary">
                            <FaStore />
                        </span>
                        <span className="arm-h3">{translate('shops')}</span>
                    </Link>
                )}
                <Link
                    to="/campaigns"
                    className="group inline-flex items-center gap-2"
                >
                    <span className="text-lg text-theme-orange transition-all group-hover:text-theme-secondary">
                        <FaBolt />
                    </span>
                    <span className="arm-h3">{translate('Flash Sale')}</span>
                </Link>
                <Link
                    to="/products"
                    className="group inline-flex items-center gap-2"
                >
                    <span className="text-lg text-stone-300 transition-all group-hover:text-theme-secondary">
                        <FaShirt />
                    </span>
                    <span className="arm-h3">{translate('All Products')}</span>
                </Link>
                <Link
                    to="/brands"
                    className="group inline-flex items-center gap-2"
                >
                    <span className="text-lg text-stone-300 transition-all group-hover:text-theme-secondary">
                        <FaApple />
                    </span>
                    <span className="arm-h3">{translate('All Brands')}</span>
                </Link>
                <Link
                    to="/products?sortBy=newest"
                    className="group inline-flex items-center gap-2"
                >
                    <span className="text-lg text-stone-300 transition-all group-hover:text-theme-secondary">
                        <FaLeaf />
                    </span>
                    <span className="arm-h3">{translate('New Arrivals')}</span>
                </Link>
                <Link
                    to="/coupons"
                    className="group inline-flex items-center gap-2"
                >
                    <span className="text-lg text-stone-300 transition-all group-hover:text-theme-secondary">
                        <FaTag />
                    </span>
                    <span className="arm-h3">{translate('Coupons')}</span>
                </Link>
                <Link
                    to="/discounts"
                    className="group inline-flex items-center gap-2"
                >
                    <span className="text-lg text-theme-alert transition-all group-hover:text-theme-secondary">
                        <FaFire />
                    </span>
                    <span className="arm-h3">{translate('Hot Deals')}</span>
                </Link>
            </div>
        </div>
    );
};

export default CategoryBar;
