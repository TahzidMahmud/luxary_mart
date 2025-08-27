import toast from 'react-hot-toast';
import { HiStar } from 'react-icons/hi';
import { LiaTimesSolid } from 'react-icons/lia';
import { Link } from 'react-router-dom';
import {
    useDeleteCartProductMutation,
    useUpdateCartProductMutation,
} from '../../store/features/api/productApi';
import { useAuth } from '../../store/features/auth/authSlice';
import { IProductShort, IProductVariation, IReview } from '../../types/product';
import { isProductAvailable } from '../../utils/checkStock';
import { cn } from '../../utils/cn';
import { currencyFormatter } from '../../utils/numberFormatter';
import { translate } from '../../utils/translate';
import Image from '../Image';
import Counter, { TCounterOrder } from '../inputs/Counter';
import ThemeRating from '../reviews/ThemeRating';

interface Props {
    cartId: number;
    product: IProductShort;
    review?: IReview | null;
    variation?: IProductVariation;
    quantity: number;
    size?: 'sm' | 'md' | 'lg';
    counter?: boolean;
    onlyPrice?: boolean;
    deleteBtn?: boolean;
    /** default `false`. if true it returns review button container is rendered. */
    reviewBtn?: boolean;
    /** default `true`. if order is not delivered then pass false, to hide the button only (not the container). */
    reviewBtnActive?: boolean;
    showTotalPrice?: boolean;
    containerClassName?: string;

    onReviewClick?: () => void;
}

const ProductCardWide = ({
    cartId,
    product,
    review,
    variation,
    quantity,
    size = 'sm',
    counter = true,
    onlyPrice = false,
    deleteBtn = true,
    reviewBtn = false,
    reviewBtnActive = true,
    showTotalPrice = false,
    containerClassName,
    onReviewClick,
}: Props) => {
    const { carts, guestUserId } = useAuth();
    const [deleteCartProduct] = useDeleteCartProductMutation();
    const [updateCartProduct] = useUpdateCartProductMutation();

    const handleQuantityChange = async (action: 'increase' | 'decrease') => {
        if (!variation) return;

        // check if the selected variation is in stock or not
        if (action === 'increase' && !isProductAvailable(variation, carts)) {
            return;
        }

        await updateCartProduct({
            id: cartId,
            action: action === 'decrease' && quantity === 1 ? 'delete' : action,
            guestUserId,
            warehouseId: variation.stocks[0]?.warehouseId!,
        }).unwrap();
    };

    const handleDeleteFromCart = async () => {
        try {
            await deleteCartProduct({
                id: cartId,
                guestUserId,
                zoneId: 1,
            }).unwrap();

            toast.success(translate('Product removed from cart!'));
        } catch (error) {
            toast.error(translate('Something went wrong!'));
        }
    };

    const counterOrder: [TCounterOrder, TCounterOrder, TCounterOrder] =
        size === 'sm'
            ? ['increment', 'count', 'decrement']
            : ['decrement', 'count', 'increment'];

    const basePrice = variation!.basePrice;
    const discountPrice = variation!.discountedBasePrice;

    const totalPrice = variation!.discountedBasePrice * quantity;

    return (
        <div
            className={cn(
                'flex items-center gap-2 sm:gap-5 py-1 sm:py-2.5',
                containerClassName,
            )}
        >
            <div>
                <Link
                    to={`/products/${product.slug}`}
                    className="rounded-md border border-theme-primary-14 p-1"
                >
                    <Image
                        src={variation?.image || product.thumbnailImg}
                        alt={product.name}
                        className={`${
                            size === 'sm'
                                ? 'max-w-[52px]'
                                : 'max-w-[52px] sm:max-w-[100px]'
                        } aspect-square`}
                    />
                </Link>
            </div>

            <div className="grow">
                <h3 className="text-black sm:text-xs mb-2">
                    <Link
                        to={`/products/${product.slug}`}
                        className="hover:text-theme-secondary max-w-[300px] line-clamp-2"
                    >
                        {product.name}
                    </Link>
                </h3>

                <div className="text-theme-secondary text-xs font-bold flex gap-1 items-center">
                    {discountPrice !== basePrice ? (
                        <>
                            {!onlyPrice && (
                                <span className="text-neutral-400 line-through font-normal">
                                    {currencyFormatter(basePrice)}
                                </span>
                            )}
                            <span>{currencyFormatter(discountPrice)}</span>
                        </>
                    ) : (
                        <>{currencyFormatter(basePrice)}</>
                    )}

                    {!counter && (
                        <span className="text-neutral-400 font-normal">
                            x{quantity}
                        </span>
                    )}

                    {variation?.name && (
                        <span className="px-2 py-1 ml-2 text-[9px] sm:text-xs uppercase font-normal text-white leading-none bg-theme-secondary-light rounded">
                            {variation?.name}
                        </span>
                    )}
                </div>
            </div>

            {showTotalPrice && (
                <div className="text-sm text-right font-medium text-neutral-400 w-[60px]">
                    {currencyFormatter(totalPrice)}
                </div>
            )}

            {reviewBtn && (
                <div className="w-[100px] text-right">
                    {reviewBtnActive && (
                        <button type="button" onClick={onReviewClick}>
                            {!review ? (
                                <span className="text-xs text-right text-theme-orange font-bold whitespace-nowrap uppercase">
                                    {translate('Write Review')}
                                </span>
                            ) : (
                                <>
                                    <span className="sm:hidden flex items-center gap-1">
                                        <span className="text-theme-orange text-xl">
                                            <HiStar />
                                        </span>
                                        {review.rating}
                                    </span>
                                    <ThemeRating
                                        readOnly
                                        rating={review.rating}
                                        className="max-sm:hidden"
                                        style={{
                                            maxWidth: 100,
                                        }}
                                    />
                                </>
                            )}
                        </button>
                    )}
                </div>
            )}

            {(counter || deleteBtn) && (
                <div
                    className={`h-full flex items-center justify-end ${
                        size === 'sm' ? 'gap-1' : 'gap-8'
                    }`}
                >
                    {counter && (
                        <Counter
                            value={quantity}
                            order={counterOrder}
                            min={0}
                            max={product.stockQty}
                            size={size}
                            orientation={
                                size === 'sm' ? 'vertical' : 'horizontal'
                            }
                            onDecrement={() => handleQuantityChange('decrease')}
                            onIncrement={() => handleQuantityChange('increase')}
                        />
                    )}
                    {deleteBtn && (
                        <button className="p-1" onClick={handleDeleteFromCart}>
                            <LiaTimesSolid />
                        </button>
                    )}
                </div>
            )}
        </div>
    );
};

export default ProductCardWide;
