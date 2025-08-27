import { FaHeart } from 'react-icons/fa';
import ProductCard from '../../components/card/ProductCard';
import ProductCardSkeleton from '../../components/skeletons/ProductCardSkeleton';
import { WishlistSkeleton } from '../../components/skeletons/from-svg';
import SectionTitle from '../../components/titles/SectionTitle';
import { useGetWishListQuery } from '../../store/features/api/productApi';
import { paddedNumber } from '../../utils/numberFormatter';
import { translate } from '../../utils/translate';

const Wishlist = () => {
    const { data: wishlist, isLoading } = useGetWishListQuery();

    if (isLoading) return <WishlistSkeleton />;

    return (
        <div className="px-[15px] sm:px-5 lg:px-8 py-4 sm:py-6 md:pt-10 md:pb-7 bg-white border border-zinc-100 rounded-md">
            <SectionTitle
                icon={<FaHeart />}
                title={`${translate('My Wishlist')} (${paddedNumber(
                    wishlist?.length || 0,
                )})`}
                className="mb-6"
            />

            <div className="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4">
                {isLoading ? (
                    Array(8)
                        .fill(0)
                        .map((_, i) => <ProductCardSkeleton key={i} />)
                ) : wishlist?.length === 0 ? (
                    <div className="col-span-4 text-center text-gray-500 ">
                        <p className="mb-2">
                            {translate('No items in your wishlist')}
                        </p>
                    </div>
                ) : (
                    wishlist?.map((item) => (
                        <ProductCard key={item.id} product={item.product} />
                    ))
                )}
            </div>
        </div>
    );
};

export default Wishlist;
