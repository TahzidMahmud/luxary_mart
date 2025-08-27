import { FaCartShopping, FaHeart, FaRightLeft } from 'react-icons/fa6';
import { Link } from 'react-router-dom';
import { togglePopup } from '../../../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../../../store/store';
import SearchForm from '../../../inputs/SearchForm';

const Logobar = () => {
    const dispatch = useAppDispatch();

    return (
        <div className="h-[70px] flex items-center justify-center shadow-theme text-zinc-500 sticky top-0 bg-white z-[2]">
            <div className="container grid grid-cols-12 items-center">
                <div className="col-span-3">
                    <Link to="/">
                        <img src="/images/logo.png" alt="" />
                    </Link>
                </div>
                <div className="col-span-5">
                    <SearchForm
                        name="search"
                        placeholder="Search for products, brands and categories..."
                    />
                </div>
                <div className="col-span-4 flex items-center justify-end gap-5">
                    <Link to="#" className="inline-flex items-center gap-2">
                        <span className="text-[20px] text-theme-secondary">
                            <FaRightLeft />
                        </span>
                        <span className="arm-h4">Compare</span>
                    </Link>
                    <Link to="#" className="inline-flex items-center gap-2">
                        <span className="text-[20px] text-theme-secondary">
                            <FaHeart />
                        </span>
                        <span className="arm-h4">Wishlist</span>
                    </Link>
                    <button
                        className="inline-flex items-center gap-2"
                        onClick={() => dispatch(togglePopup('cart'))}
                    >
                        <span className="text-[20px] text-theme-secondary">
                            <FaCartShopping />
                        </span>
                        <span className="arm-h4">
                            <span className="text-black">$10.10</span> (1 Item)
                        </span>
                    </button>
                </div>
            </div>
        </div>
    );
};

export default Logobar;
