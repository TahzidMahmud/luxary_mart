import { FaBars, FaHome } from 'react-icons/fa';
import {
    FaAngleDown,
    FaApple,
    FaBolt,
    FaCrown,
    FaFire,
    FaRss,
    FaTag,
} from 'react-icons/fa6';
import { Link } from 'react-router-dom';
import { togglePopup } from '../../../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../../../store/store';
import { translate } from '../../../../utils/translate';

const CategoryBar = () => {
    const dispatch = useAppDispatch();

    return (
        <div className="h-[57px] flex items-center">
            <div className="container flex items-center justify-between gap-10">
                <div>
                    <button
                        className="bg-theme-primary text-white py-2.5 px-4 rounded-md hover:bg-theme-primary/90"
                        onClick={() => dispatch(togglePopup('categories'))}
                    >
                        <span>
                            <FaBars />
                        </span>
                        <span className="pl-4 pr-2.5">
                            {translate('All Categories')}
                        </span>
                        <span className="text-xs opacity-60">
                            <FaAngleDown />
                        </span>
                    </button>
                </div>

                <div className="flex items-center justify-end gap-12">
                    <Link
                        to="/"
                        className="group inline-flex items-center gap-2"
                    >
                        <span className="text-lg text-stone-300 transition-all group-hover:text-theme-secondary">
                            <FaHome />
                        </span>
                        <span className="arm-h3">{translate('home')}</span>
                    </Link>
                    <Link
                        to="#"
                        className="group inline-flex items-center gap-2"
                    >
                        <span className="text-lg text-theme-orange transition-all group-hover:text-theme-secondary">
                            <FaBolt />
                        </span>
                        <span className="arm-h3">
                            {translate('Flash Sale')}
                        </span>
                    </Link>
                    <Link
                        to="#"
                        className="group inline-flex items-center gap-2"
                    >
                        <span className="text-lg text-stone-300 transition-all group-hover:text-theme-secondary">
                            <FaRss />
                        </span>
                        <span className="arm-h3">{translate('blogs')}</span>
                    </Link>
                    <Link
                        to="#"
                        className="group inline-flex items-center gap-2"
                    >
                        <span className="text-lg text-stone-300 transition-all group-hover:text-theme-secondary">
                            <FaApple />
                        </span>
                        <span className="arm-h3">
                            {translate('All Brands')}
                        </span>
                    </Link>
                    <Link
                        to="#"
                        className="group inline-flex items-center gap-2"
                    >
                        <span className="text-lg text-stone-300 transition-all group-hover:text-theme-secondary">
                            <FaCrown />
                        </span>
                        <span className="arm-h3">
                            {translate('All Sellers')}
                        </span>
                    </Link>
                    <Link
                        to="#"
                        className="group inline-flex items-center gap-2"
                    >
                        <span className="text-lg text-stone-300 transition-all group-hover:text-theme-secondary">
                            <FaTag />
                        </span>
                        <span className="arm-h3">{translate('Coupons')}</span>
                    </Link>
                    <Link
                        to="#"
                        className="group inline-flex items-center gap-2"
                    >
                        <span className="text-lg text-theme-alert transition-all group-hover:text-theme-secondary">
                            <FaFire />
                        </span>
                        <span className="arm-h3">
                            {translate('Todays Deal')}
                        </span>
                    </Link>
                </div>
            </div>
        </div>
    );
};

export default CategoryBar;
