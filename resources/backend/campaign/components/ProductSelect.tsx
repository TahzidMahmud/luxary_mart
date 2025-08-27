import React, { ChangeEvent, useState } from 'react';
import Image from '../../react/components/Image';
import Label from '../../react/components/inputs/Label';
import { translate } from '../../react/utils/translate';
import { IProductShort } from '../types';
import Checkbox from './inputs/Checkbox';

interface Props {
    product: IProductShort;
    selectedVariations: number[];
    onVariationSelect: (params: {
        productId: number;
        variationIds: number[];
    }) => void;
}

const ProductSelect = ({
    product,
    selectedVariations = [],
    onVariationSelect,
}: Props) => {
    const [differentOffer, setDifferentOffer] = useState(true);

    const handleDifferentOfferToggle = () => {
        if (differentOffer) {
            // add all
            onVariationSelect({
                productId: product.id,
                variationIds: product.variations.map((v) => v.id),
            });
        }

        setDifferentOffer(!differentOffer);
    };

    const handleSelect = (e: ChangeEvent<HTMLInputElement>) => {
        const checked = e.target.checked;

        if (checked) {
            onVariationSelect({
                productId: product.id,
                variationIds: product.variations.map((v) => v.id),
            });
        } else {
            onVariationSelect({
                productId: product.id,
                variationIds: [],
            });
        }
    };

    const handleVariationSelect = (variationId: number) => {
        const wasSelected = selectedVariations.includes(variationId);

        if (wasSelected) {
            const newVariations = selectedVariations.filter(
                (id) => id !== variationId,
            );
            onVariationSelect({
                productId: product.id,
                variationIds: newVariations,
            });
        } else {
            onVariationSelect({
                productId: product.id,
                variationIds: [...selectedVariations, variationId],
            });
        }
    };

    return (
        <div className="wide-product-card flex items-center gap-8 px-3 lg:px-9 py-3 lg:py-5">
            <div className="py-2 lg:py-7">
                <Checkbox
                    name="productId"
                    value={product.id.toString()}
                    checked={selectedVariations.length > 0}
                    onChange={handleSelect}
                />
            </div>

            <div className="grow">
                <div className="flex gap-5 items-center">
                    <div className="p-1 border border-border rounded-md max-xs:hidden xl:hidden 3xl:inline-block">
                        <Image
                            src={product.thumbnailImage}
                            className="w-[80px] aspect-square "
                        />
                    </div>

                    <div className="grow">
                        <p className="max-w-[300px] text-justify">
                            {product.name}
                        </p>
                    </div>
                </div>

                {product.hasVariation ? (
                    <div className="mt-5">
                        {differentOffer && (
                            <>
                                <label className="flex items-center gap-2">
                                    <span>
                                        {translate('Choose Variations')}
                                    </span>
                                </label>

                                <div className="flex flex-wrap gap-2 mt-4">
                                    {product.variations.map((variation) => (
                                        <button
                                            onClick={() =>
                                                handleVariationSelect(
                                                    variation.id,
                                                )
                                            }
                                            key={variation.id}
                                        >
                                            <Label
                                                className={`border hover:border-theme-secondary ${
                                                    selectedVariations.includes(
                                                        variation.id,
                                                    )
                                                        ? 'bg-theme-secondary-light text-white border-theme-secondary-light'
                                                        : 'bg-background text-muted border-border'
                                                }`}
                                            >
                                                {variation.name}
                                            </Label>
                                        </button>
                                    ))}
                                </div>
                            </>
                        )}

                        <label className="mt-5 flex items-center justify-between">
                            <p>{translate('Different Offer for Variations')}</p>
                            <Checkbox
                                name="product"
                                toggler={true}
                                checked={differentOffer}
                                onChange={handleDifferentOfferToggle}
                            />
                        </label>
                    </div>
                ) : null}
            </div>
        </div>
    );
};

export default ProductSelect;
