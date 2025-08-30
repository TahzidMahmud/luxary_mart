import React, { useState } from 'react';
import toast from 'react-hot-toast';
import { useSearchParams } from 'react-router-dom';
import {
    currencyFormatter,
    numberFormatter,
} from '../../../frontend/utils/numberFormatter';
import Image from '../../react/components/Image';
import Spinner from '../../react/components/loader/Spinner';
import { objectToFormData } from '../../react/utils/ObjectFormData';
import { cn } from '../../react/utils/cn';
import { IPosCartGroup, IPosProductVariation } from '../types';
import { addToPosCartGroup, updatePosCartItem } from '../utils/actions';

interface Props {
    posCartGroup?: IPosCartGroup;
    posProduct: IPosProductVariation;
    onSuccess?: (posCartGroup: IPosCartGroup) => void;
}

const ProductCard = ({ posCartGroup, posProduct, onSuccess }: Props) => {
    const [searchParams] = useSearchParams();
    const [isLoading, setIsLoading] = useState(false);

    const handleAddToCart = async () => {
        if (posProduct.stocks[0]?.stockQty <= 0) {
            toast.error('Product is out of stock');
            return;
        }

        setIsLoading(true);

        const productInCart = posCartGroup?.posCarts.find(
            (cart) => cart.productVariationId === posProduct.id,
        );
        // if product is already in cart, increase the quantity
        // else add the product to cart
        if (productInCart) {
            updatePosCartItem(
                objectToFormData({
                    id: productInCart.id,
                    action: 'increase',
                }),
            ).then((posCarts) =>
                onSuccess?.({
                    posCartGroupId: posCartGroup!.posCartGroupId,
                    posCarts,
                }),
            );
        } else {
            const newPosCartGroup = await addToPosCartGroup(
                objectToFormData({
                    firstCartProduct: posCartGroup?.posCartGroupId ? null : 1,
                    posCartGroupId: posCartGroup?.posCartGroupId || null,
                    productVariationId: posProduct.id,
                    warehouseId: searchParams.get('warehouseId') || null,
                    qty: 1,
                }),
            );
            onSuccess?.({
                posCartGroupId:
                    posCartGroup?.posCartGroupId ||
                    newPosCartGroup.posCartGroupId,
                posCarts: newPosCartGroup.posCarts,
            });
        }

        setIsLoading(false);
    };

    return (
        <div className="border border-border rounded-md overflow-hidden">
            <div className="group relative flex flex-col hover:border hover:border-theme-secondary hover:rounded">
                <button
                    className="absolute inset-0 z-[1] bg-theme-primary/90 text-white flex items-center justify-center flex-col gap-5 opacity-0 group-hover:opacity-10"
                    onClick={handleAddToCart}
                    disabled={isLoading}
                >
                    {isLoading ? (
                        <div className="text-5xl">
                            <Spinner className="w-[50px] h-[50px]" />
                        </div>
                    ) : (
                        <>
                            {/* <div className="relative w-20 h-20">
                                <div className="w-1 bg-white absolute left-1/2 top-0 bottom-0 -translate-x-1/2"></div>
                                <div className="h-1 bg-white absolute top-1/2 left-0 right-0 -translate-y-1/2"></div>
                            </div>
                            <span className="text-lg">
                                {translate('Add Product')}
                            </span> */}
                        </>
                    )}
                </button>


                <Image
                    className="aspect-square"
                    src={posProduct.image}
                    alt="Product Image"
                />

                <div className="grow px-3 sm:px-5 py-3 flex flex-col">
                    <h3 className="grow font-public-sans mt-1 sm:mt-2 mb-1 line-clamp-2">
                        {posProduct.product.name}
                    </h3>

                    <div className="flex gap-1 flex-wrap items-start pb-2">
                        {posProduct.combinations.map((combination) => (
                            <span
                                key={combination.id}
                                className="bg-theme-yellow text-black px-2 py-1 rounded text-xs"
                            >
                                {combination.name}
                            </span>
                        ))}
                    </div>

                    <p className="text-theme-secondary text-sm sm:text-[15px] font-bold flex gap-1 items-center">
                        {currencyFormatter(posProduct.discountedBasePrice)}
                    </p>

                    <p className="text-[12px] mt-1 flex gap-1 items-center">
                         SKU: {posProduct.sku}
                    </p>
                    <p
                        className={cn(
                            'text-center px-2 py-1 text-white rounded-md mt-2',
                            {
                                'bg-emerald-500':
                                    posProduct.stocks[0]?.stockQty > 0,
                                'bg-red-500':
                                    posProduct.stocks[0]?.stockQty <= 0,
                            },
                        )}
                    >
                        In Stock:
                        {numberFormatter(posProduct.stocks[0]?.stockQty)}
                    </p>
                </div>
            </div>

            <a
                href={`/admin/products/real-pictures/${posProduct.product.id}`}
                target="_blank"
                className="py-2 flex items-center justify-center gap-2 border-t border-border hover:text-white hover:bg-theme-primary"
            >
                Real Picture
            </a>
        </div>
    );
};

export default ProductCard;
