import { useState } from 'react';
import toast from 'react-hot-toast';
import { FaHeart } from 'react-icons/fa';
import { useDispatch } from 'react-redux';
import { Link } from 'react-router-dom';
import {
    useAddToCartMutation,
    useAddToWishListMutation,
    useDeleteWishListMutation,
    useLazyGetProductDetailsQuery,
} from '../../store/features/api/productApi';
import { useAuth } from '../../store/features/auth/authSlice';
import { togglePopup } from '../../store/features/popup/popupSlice';
import { IProductShort } from '../../types/product';
import { isProductAvailable } from '../../utils/checkStock';
import { cn } from '../../utils/cn';
import { currencyFormatter } from '../../utils/numberFormatter';
import { translate } from '../../utils/translate';
import Image from '../Image';
import Button from '../buttons/Button';
import IconButton from '../buttons/IconButton';
import ThemeLabel from './ThemeLabel';

interface Props {
    product: IProductShort;
}

const ProductCard = ({ product: p }: Props) => {
    const { user, userLocation, carts, isLoading } = useAuth();
    const dispatch = useDispatch();
    // keep track of product in state to update wishlist icon
    const [product, setProduct] = useState<IProductShort>(p);
    const [addToWishList, { isLoading: addingToWishlist }] =
        useAddToWishListMutation();
    const [deleteFromWishList, { isLoading: deletingFromWishlist }] =
        useDeleteWishListMutation();
    const [addToCart, { isLoading: addingToCart }] = useAddToCartMutation();
    const [getProductDetails, { isLoading: gettingProduct }] =
        useLazyGetProductDetailsQuery();

    const handleProductPreview = () => {
        dispatch(
            togglePopup({
                popup: 'product-preview',
                size: 'lg',
                popupProps: {
                    slug: product.slug,
                },
            }),
        );
    };

    const handleAddToCart = async () => {
        // check user location is set or not
        if (!userLocation?.area?.zone_id) {
            dispatch(
                togglePopup({
                    popup: 'user-location',
                    popupProps: {
                        redirectOnSuccess: `/products/${product.slug}`,
                    },
                }),
            );
            return;
        }

        // if product has variations, show product preview popup
        // else fetch the product and add it to cart with 1st (and only) variation id
        if (product.hasVariation) {
            handleProductPreview();
            return;
        }

        const productDetails = await getProductDetails(product.slug).unwrap();

        // check if the selected variation is in stock or not
        if (!isProductAvailable(productDetails.variations[0]!, carts)) {
            return;
        }

        try {
            await addToCart({
                productVariationId: productDetails.variations[0].id,
                qty: 1,
                warehouseId:
                    productDetails.variations[0].stocks[0]?.warehouseId,
            }).unwrap();
            toast.success(translate('Added to cart'));
        } catch (err: any) {
            toast.error(err.data.message);
        }
    };

    const handleAddToWishList = async () => {
        if (isLoading || !user) {
            dispatch(togglePopup('signin'));
            return;
        }

        try {
            await addToWishList(product.id).unwrap();
            setProduct((prev) => ({ ...prev, inWishlist: true }));
            toast.success(translate('Added to wishlist'));
        } catch (err: any) {
            toast.error(err.data.message);
        }
    };

    const handleRemoveFromWishList = async () => {
        try {
            await deleteFromWishList(product.id).unwrap();
            setProduct((prev) => ({ ...prev, inWishlist: false }));
            toast.success(translate('Removed from wishlist'));
        } catch (err: any) {
            toast.error(err.data.message);
        }
    };

    return (
        <div className="relative flex flex-col border border-zinc-100 bg-white transition-all rounded-md overflow-hidden h-full hover:shadow-xl">
            <div className="absolute top-5 left-5 flex flex-col items-start gap-2">
                {product.stockQty < 1 ? (
                    <ThemeLabel
                        label={{
                            text: translate('Out of stock'),
                            variant: 'no-color',
                        }}
                        bgColor={'#a6a6a6'}
                        textColor={'#fff'}
                    />
                ) : null}

                {product.badges.map((item) => (
                    <ThemeLabel
                        label={{
                            text: item.name,
                            variant: 'no-color',
                        }}
                        bgColor={item.bgColor}
                        textColor={item.textColor}
                        key={item.id}
                    />
                ))}
            </div>

            <IconButton
                as="button"
                isLoading={addingToWishlist || deletingFromWishlist}
                className={cn(`absolute top-3 right-3`, {
                    'text-theme-alert': product.inWishlist,
                })}
                onClick={
                    product.inWishlist
                        ? handleRemoveFromWishList
                        : handleAddToWishList
                }
            >
                <FaHeart />
            </IconButton>

            <Link
                to={`/products/${product.slug}`}
                className="aspect-square bg-white flex items-center justify-center"
            >
                <Image
                    src={product.thumbnailImg}
                    alt={product.name}
                    className="w-full h-full"
                />
            </Link>

            <div className="grow px-3 sm:px-5 pb-3 sm:pb-5 pt-3 flex flex-col">
                <h3 className="grow font-public-sans text-[#191C1F] mb-1 text-center">
                    <Link
                        to={`/products/${product.slug}`}
                        className="line-clamp-2 hover:text-theme-secondary-light"
                    >
                        {product.name}
                    </Link>
                </h3>
                <div className="text-theme-secondary text-sm sm:text-lg font-bold flex gap-1 items-center justify-center">
                    {currencyFormatter(product.basePrice)}
                </div>

                <Button
                    size="sm"
                    className="w-full mt-1 sm:mt-3 max-sm:h-8 h-10 text-xs whitespace-nowrap uppercase"
                    onClick={handleAddToCart}
                    isLoading={gettingProduct || addingToCart}
                    disabled={product.stockQty < 1}
                >
                    {translate('Add to cart')}
                </Button>
            </div>
        </div>
    );
};

export default ProductCard;
