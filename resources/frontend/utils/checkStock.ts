import toast from 'react-hot-toast';
import { ICartProduct, IProductVariation } from '../types/product';
import { translate } from './translate';

/**
 * Check if the selected variation is available in stock or not
 * if not, show error toast and return false
 * if yes, return true
 * @param selectedVariation IProductVariation
 * @param carts ICartProduct[]
 */
export const isProductAvailable = (
    selectedVariation: IProductVariation,
    carts: ICartProduct[],
) => {
    if (!selectedVariation.stocks.length) {
        toast.error(translate('Out of stock'));
        return false;
    }

    // find the quantity already added to cart of the selected variation
    const productCartQuantity = carts.find(
        (cartProduct) => cartProduct.variation.id === selectedVariation?.id,
    );

    const maximumAddableQuantity =
        (selectedVariation?.stocks[0]?.stockQty || 1) -
        (productCartQuantity?.qty || 0);

    // check if the selected variation is in stock or not
    if (selectedVariation?.stocks[0]?.stockQty < 1) {
        toast.error(translate('Out of stock'));
        return false;
    } else if (maximumAddableQuantity < 1) {
        toast.error(translate('Maximum quantity added to cart'));
        return false;
    }

    return true;
};
