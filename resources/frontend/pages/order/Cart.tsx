import { useEffect, useState } from 'react';
import toast from 'react-hot-toast';
import { FaHome, FaTimes } from 'react-icons/fa';
import { Link } from 'react-router-dom';
import Breadcrumb from '../../components/Breadcrumb';
import Button from '../../components/buttons/Button';
import ProductCardWide from '../../components/card/ProductCardWide';
import Checkbox from '../../components/inputs/Checkbox';
import Input from '../../components/inputs/Input';
import { useApplyCouponMutation } from '../../store/features/api/shopApi';
import { useAuth } from '../../store/features/auth/authSlice';
import {
    setCheckoutData,
    useCheckoutData,
} from '../../store/features/checkout/checkoutSlice';
import { togglePopup } from '../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../store/store';
import { ICartProduct } from '../../types/product';
import { getSelectedShopProducts } from '../../utils/getSelectedShopProducts';
import { groupBy } from '../../utils/groupBy';
import { currencyFormatter } from '../../utils/numberFormatter';
import { translate } from '../../utils/translate';

const Cart = () => {
    const dispatch = useAppDispatch();
    const { user, carts, isLoading } = useAuth();
    const { coupons, selectedShops } = useCheckoutData();

    const [groupedCarts, setGroupedCarts] = useState<
        Record<string, ICartProduct[]>
    >({});

    const [subTotal, setSubTotal] = useState(0);
    const [tax, setTax] = useState(0);
    const [discount, setDiscount] = useState(0);
    const [total, setTotal] = useState(0);

    const [applyCoupon, { isLoading: applyingCoupon, originalArgs }] =
        useApplyCouponMutation();

    useEffect(() => {
        if (isLoading) return;

        const groupedByShop = groupBy(carts, ({ shop }) => shop.name);
        setGroupedCarts(groupedByShop);
    }, [carts]);

    useEffect(() => {
        const { total, subTotal, tax, totalDiscount } = getSelectedShopProducts(
            {
                cartProducts: groupedCarts,
                coupons,
                selectedShops,
            },
        );

        setSubTotal(subTotal);
        setTax(tax);
        setDiscount(totalDiscount);
        setTotal(total);
    }, [selectedShops, coupons, groupedCarts]);

    const handleApplyCoupon = async (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        if (!user) {
            dispatch(togglePopup('signin'));
            return;
        }

        const code = (e.target as any).code.value;
        const shopId = +(e.target as any).shopId.value;

        if (!code) {
            return;
        }

        const coupon = await applyCoupon({ code, shopId }).unwrap();
        if (coupon) {
            // add coupon to store
            dispatch(
                setCheckoutData({
                    coupons: [...coupons, coupon],
                }),
            );
        } else {
            toast.error(translate('Coupon not found!'));
        }
    };

    const handleRemoveCoupon = (shopId: number) => {
        dispatch(
            setCheckoutData({
                coupons: coupons.filter((coupon) => coupon.shopId !== shopId),
            }),
        );
    };

    const handleSelectShop = (shopName: string) => {
        dispatch(
            setCheckoutData({
                selectedShops: {
                    ...selectedShops,
                    [shopName]: !selectedShops[shopName],
                },
            }),
        );
    };

    const handleSelectAll = () => {
        dispatch(
            setCheckoutData({
                selectedShops: Object.keys(selectedShops).reduce(
                    (acc, shopName) => {
                        acc[shopName] = !isAllShopSelected;
                        return acc;
                    },
                    {} as Record<string, boolean>,
                ),
            }),
        );
    };

    const isAllShopSelected = Object.values(selectedShops).every((v) => v);

    return (
        <>
            <Breadcrumb
                title={translate('Your Cart')}
                navigation={[
                    {
                        icon: <FaHome />,
                        name: translate('Home'),
                        link: '/',
                    },
                    {
                        name: translate('Cart'),
                    },
                ]}
            />

            <div className="theme-container-card no-style grid grid-cols-1 md:grid-cols-3 mb-12">
                <div className="md:col-span-2 bg-white rounded-md p-3 sm:p-6 md:p-9">
                    {/* <Checkbox
                        label={`${translate('Select All')} (${
                            Object.keys(selectedShops).length
                        } Shops)`}
                        name="select-all"
                        className="mb-2 sm:mb-4"
                        labelClassName="text-zinc-500 text-xs"
                        checked={isAllShopSelected}
                        onChange={handleSelectAll}
                    /> */}

                    <div className="space-y-6">
                        {Object.entries(groupedCarts).map(
                            ([shopName, carts]) => (
                                <div key={shopName}>
                                    <div className="rounded-t-md bg-stone-50 hidden items-center p-2 sm:p-4">
                                        <Checkbox
                                            label={
                                                <span className="text-xs">
                                                    {translate('Seller')}
                                                    <Link
                                                        to={`/shops/${carts[0].shop.slug}`}
                                                        className="text-theme-secondary-light ml-1"
                                                    >
                                                        {shopName}
                                                    </Link>
                                                </span>
                                            }
                                            name="select-shop"
                                            value={shopName}
                                            checked={selectedShops[shopName]}
                                            onChange={() =>
                                                handleSelectShop(shopName)
                                            }
                                        />
                                    </div>

                                    {carts.map((item) => (
                                        <div
                                            className="px-2 sm:px-4 py-0.5"
                                            key={item.id}
                                        >
                                            <ProductCardWide
                                                cartId={item.id}
                                                product={item.product}
                                                variation={item.variation}
                                                quantity={item.qty}
                                                size={
                                                    window.innerWidth < 640
                                                        ? 'sm'
                                                        : 'md'
                                                }
                                            />
                                        </div>
                                    ))}

                                    <div className="px-2 sm:px-4 py-1.5 sm:py-3 grid sm:grid-cols-5">
                                        <form
                                            onSubmit={handleApplyCoupon}
                                            className="col-span-3 flex gap-2.5"
                                        >
                                            <input
                                                type="hidden"
                                                name="shopId"
                                                value={carts[0].shop.id}
                                                onClick={() => {}}
                                            />
                                            <div>
                                                <Input
                                                    name="code"
                                                    placeholder={translate(
                                                        'Add coupon code',
                                                    )}
                                                    className="text-sm"
                                                />
                                            </div>
                                            <Button
                                                variant="primary"
                                                disabled={coupons.some(
                                                    (coupon) =>
                                                        coupon.shopId ===
                                                        carts[0].shop.id,
                                                )}
                                                isLoading={
                                                    applyingCoupon &&
                                                    originalArgs?.shopId ===
                                                        carts[0].shop.id
                                                }
                                            >
                                                {translate('Add')}{' '}
                                                <span className="max-sm:hidden">
                                                    {translate('Coupons')}
                                                </span>
                                            </Button>
                                        </form>

                                        <div className="col-span-2 text-xs flex flex-wrap gap-2 items-center">
                                            {coupons.map((coupon, index) => {
                                                if (
                                                    coupon.shopId !==
                                                    carts[0].shop.id
                                                ) {
                                                    return null;
                                                }

                                                return (
                                                    <button
                                                        key={index}
                                                        className="inline-flex justify-between bg-theme-secondary-light text-white rounded px-3 py-2 leading-none text-start uppercase"
                                                        onClick={() =>
                                                            handleRemoveCoupon(
                                                                coupon.shopId,
                                                            )
                                                        }
                                                    >
                                                        {coupon.code}

                                                        <span className="ml-2">
                                                            <FaTimes />
                                                        </span>
                                                    </button>
                                                );
                                            })}
                                        </div>
                                    </div>
                                </div>
                            ),
                        )}
                    </div>
                </div>
                <div>
                    <div className="bg-white rounded-md overflow-hidden">
                        <h3 className="py-3 px-5 sm:px-6 md:px-8 bg-theme-primary text-white">
                            {translate('Total Cost')}
                        </h3>

                        <div className="px-3 sm:px-8 py-4 space-y-7">
                            <div className="border border-zinc-100 rounded-md divide-y divide-zinc-100 uppercase">
                                <div className="flex items-center justify-between py-3 sm:py-6 px-3 sm:px-8">
                                    <span>{translate('Subtotal')}</span>
                                    <span>{currencyFormatter(subTotal)}</span>
                                </div>
                                <div className="flex items-center justify-between py-3 sm:py-6 px-3 sm:px-8">
                                    <span>{translate('Tax')}</span>
                                    <span>{currencyFormatter(tax)}</span>
                                </div>
                                <div className="flex items-center justify-between py-3 sm:py-6 px-3 sm:px-8">
                                    {translate('Discount')}
                                    <span>{currencyFormatter(discount)}</span>
                                </div>
                                <div className="flex items-center justify-between py-3 sm:py-6 px-3 sm:px-8">
                                    <span>{translate('Total')}</span>
                                    <span>{currencyFormatter(total)}</span>
                                </div>
                            </div>

                            <div className="flex gap-2">
                                <Button
                                    as="link"
                                    to="/products"
                                    className="grow"
                                    size="lg"
                                >
                                    {translate('Shop More')}
                                </Button>
                                {user ? (
                                    <Button
                                        as="link"
                                        to="/checkout"
                                        className="grow"
                                        size="lg"
                                        variant="warning"
                                    >
                                        {translate('Checkout')}
                                    </Button>
                                ) : (
                                    <Button
                                        as="button"
                                        onClick={() =>
                                            dispatch(togglePopup('signin'))
                                        }
                                        className="grow"
                                        size="lg"
                                        variant="warning"
                                    >
                                        {translate('Checkout')}
                                    </Button>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default Cart;
