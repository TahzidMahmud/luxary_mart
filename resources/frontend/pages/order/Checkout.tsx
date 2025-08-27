import { useEffect, useState } from 'react';
import toast from 'react-hot-toast';
import { FaHome } from 'react-icons/fa';
import { useDispatch } from 'react-redux';
import { Link, useNavigate, useSearchParams } from 'react-router-dom';
import { object, string } from 'yup';
import Breadcrumb from '../../components/Breadcrumb';
import Button from '../../components/buttons/Button';
import ProductCardWide from '../../components/card/ProductCardWide';
import CustomerDetailsForm from '../../components/order/CustomerDetailsForm';
import PaymentMethodForm from '../../components/order/PaymentMethodForm';
import SelectAddress from '../../components/order/SelectAddress';
import { useCreateOrderMutation } from '../../store/features/api/orderApi';
import { useLazyGetCartProductsQuery } from '../../store/features/api/productApi';
import { useAuth } from '../../store/features/auth/authSlice';
import {
    setCheckoutData,
    useCheckoutData,
} from '../../store/features/checkout/checkoutSlice';
import { IOrderPayload } from '../../types/checkout';
import { ICartProduct } from '../../types/product';
import { getSelectedShopProducts } from '../../utils/getSelectedShopProducts';
import { currencyFormatter } from '../../utils/numberFormatter';
import { translate } from '../../utils/translate';

export const customerDetailsSchema = object().shape({
    name: string().required('Name is required'),
    phone: string().required('Phone is required'),
    alternatePhone: string(),
    note: string(),
});

export type TStep = 'details' | 'shipping' | 'payment';
const steps: TStep[] = ['details', 'shipping', 'payment'];
const stepQueryKey = 'checkoutStep';

const Checkout = () => {
    const [searchParams, setSearchParams] = useSearchParams();
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const [createOrder, { isLoading: creatingOrder }] =
        useCreateOrderMutation();
    const [getCartProducts] = useLazyGetCartProductsQuery();
    const { user, carts } = useAuth();
    const {
        customerDetails,
        shippingAddress,
        billingAddress,
        paymentMethod,
        coupons,
        selectedShops,
        shippingCharge,
    } = useCheckoutData();

    const currentStep = Number(searchParams.get(stepQueryKey) || 1);

    const [subTotal, setSubTotal] = useState(0);
    const [tax, setTax] = useState(0);
    const [discount, setDiscount] = useState(0);
    const [total, setTotal] = useState(0);

    // cart products grouped by shop
    const [selectedShopProducts, setSelectedShopsProducts] = useState<
        Record<string, ICartProduct[]>
    >({});

    useEffect(() => {
        // check if checkout data is already available in the state
        if (
            customerDetails.name ||
            customerDetails.email ||
            customerDetails.phone
        ) {
            return;
        }

        // get user data and set it to checkout details state
        dispatch(
            setCheckoutData({
                customerDetails: {
                    name: user?.name || '',
                    email: user?.email || '',
                    phone: user?.phone || '',
                    alternatePhone: '',
                    note: '',
                },
            }),
        );
    }, [user]);

    useEffect(() => {
        // calculate total, subtotal, tax, discount from selected shop products
        const { total, subTotal, tax, totalDiscount } = getSelectedShopProducts(
            {
                cartProducts: selectedShopProducts,
                coupons,
                selectedShops,
            },
        );

        setSubTotal(subTotal);
        setTax(tax);
        setDiscount(totalDiscount);
        setTotal(total + shippingCharge);
    }, [selectedShops, coupons, selectedShopProducts]);

    useEffect(() => {
        // find selected shops and their products
        const selectedShopProducts: Record<string, ICartProduct[]> = {};

        Object.keys(selectedShops).forEach((shopName) => {
            if (!selectedShops[shopName]) return;

            const products = carts.filter(
                (cart) => cart.shop.name === shopName,
            );
            selectedShopProducts[shopName] = products;
        });

        setSelectedShopsProducts(selectedShopProducts);
    }, [selectedShops, shippingCharge, carts]);

    useEffect(() => {
        // check if any shop is selected
        // if no shop is selected, navigate to cart page
        if (!Object.values(selectedShops).includes(true)) {
            navigate('/cart');
            toast('Please select at least one shop', {
                icon: '🛒',
            });
            return;
        }
    }, [selectedShops]);

    const handleNext = async () => {
        if (currentStep === 1) {
            try {
                // validate customer details before going to step 2
                await customerDetailsSchema.validate(customerDetails, {
                    abortEarly: false,
                });
            } catch (err: any) {
                toast.error(err.errors[0]);
                return;
            }
        } else if (currentStep === 2 && !shippingAddress) {
            // check if shipping address is selected
            toast.error('Please select a shipping address');
            return;
        }

        // if step is less than total steps, go to next step
        // else submit order
        if (currentStep < steps.length) {
            searchParams.set(stepQueryKey, String(currentStep + 1));
            setSearchParams(searchParams);
        } else {
            // last step: check payment method is selected
            if (!paymentMethod) {
                toast.error('Please select a payment method');
                return;
            }
            handleSubmitOrder();
        }
    };

    const handlePrev = () => {
        if (currentStep > 1) {
            searchParams.set(stepQueryKey, String(currentStep - 1));
            setSearchParams(searchParams);
        }
    };

    const handleSubmitOrder = async () => {
        // get selected shop ids from selectedShopProducts in an array
        const selectedShopIds = Object.values(selectedShopProducts)
            .map((products) => products[0].shop.id)
            .filter((id, i, arr) => arr.indexOf(id) === i); // remove duplicate shop ids

        // get selected cart ids from selectedShopProducts
        const selectedCartIds = Object.values(selectedShopProducts)
            .map((products) => products.map((product) => product.id))
            .flat(1);

        const payload: IOrderPayload = {
            ...customerDetails,
            shippingAddressId: shippingAddress!.id,
            billingAddressId: billingAddress?.id || shippingAddress!.id,
            shopIds: selectedShopIds,
            couponCodes: coupons.map((coupon) => coupon.code),
            cartIds: selectedCartIds,
            paymentMethod,
        };

        try {
            const orderGroup = await createOrder(payload).unwrap();
            getCartProducts(); // refresh cart products

            if (orderGroup.goToPayment) {
                navigate('/payment', {
                    state: orderGroup.orderCode,
                });
                return;
            }

            navigate(`/orders/success/${orderGroup.orderCode}`);
        } catch (err: any) {
            toast.error(err.data.message);
            navigate(`/orders/failed`);
        }
    };

    const stepObj: {
        [key in TStep]: JSX.Element | JSX.Element[];
    } = {
        details: <CustomerDetailsForm />,
        shipping: <SelectAddress />,
        payment: <PaymentMethodForm />,
    };

    return (
        <>
            <Breadcrumb
                title={translate('Checkout')}
                navigation={[
                    { icon: <FaHome />, name: translate('Home'), link: '/' },
                    { name: translate('Cart'), link: '/cart' },
                    { name: translate('Checkout') },
                ]}
            />

            <div className="theme-container-card no-style grid lg:grid-cols-3 mb-12">
                <div className="lg:col-span-2">
                    <div className="bg-white rounded-md p-3 sm:p-6 md:p-9">
                        <ul className="flex max-w-[546px] items-center justify-between mb-6 mx-auto">
                            {steps.map((item, i) => (
                                <li
                                    key={item}
                                    className={`flex items-center relative ${
                                        i !== 0 && 'grow justify-end'
                                    }`}
                                >
                                    {i !== 0 && (
                                        <span
                                            className={`absolute top-[13px] sm:top-4 md:top-[22px] right-[22px] h-0.5 sm:h-1 w-full ${
                                                i + 1 <= currentStep
                                                    ? 'bg-theme-secondary-light'
                                                    : 'bg-zinc-300'
                                            }`}
                                        ></span>
                                    )}
                                    <div className="flex flex-col items-center gap-1 sm:gap-2.5">
                                        <span
                                            className={`relative z-[1] w-7 sm:w-9 md:w-11 aspect-square flex items-center justify-center rounded-full leading-none text-white ${
                                                i + 1 <= currentStep
                                                    ? 'bg-theme-secondary-light'
                                                    : 'bg-zinc-300'
                                            }`}
                                        >
                                            {i + 1}
                                        </span>
                                        <span className="text-xs text-neutral-400 font-public-sans uppercase">
                                            {item}
                                        </span>
                                    </div>
                                </li>
                            ))}
                        </ul>

                        {stepObj[steps[currentStep - 1]]}

                        <div className="flex justify-between mt-7">
                            {currentStep === 1 ? (
                                <Button
                                    as="link"
                                    to="/cart"
                                    className="!px-5"
                                    size="lg"
                                    onClick={handlePrev}
                                >
                                    {translate('Back To Cart')}
                                </Button>
                            ) : (
                                <Button
                                    className="!px-5"
                                    size="lg"
                                    onClick={handlePrev}
                                >
                                    {translate('Previous Step')}
                                </Button>
                            )}

                            <Button
                                variant="warning"
                                className="!px-5"
                                size="lg"
                                onClick={handleNext}
                                isLoading={creatingOrder}
                                disabled={
                                    creatingOrder ||
                                    (currentStep === steps.length &&
                                        !window.config.paymentMethods.length)
                                }
                            >
                                {currentStep === steps.length
                                    ? translate('Place Order')
                                    : translate('Next Step')}
                            </Button>
                        </div>
                    </div>
                </div>

                <div>
                    <div className="bg-white rounded-md overflow-hidden">
                        <h3 className="py-3 px-4 sm:px-5 md:px-8 bg-theme-primary text-white">
                            {translate('Order Summary')}
                        </h3>

                        <div className="">
                            {Object.entries(selectedShopProducts).map(
                                ([shopName, carts]) => (
                                    <div key={shopName}>
                                        <div className="hidden items-center justify-between text-[11px] font-public-sans border-b border-theme-primary-14 h-8 sm:h-12 px-4 bg-stone-50">
                                            <h5 className="text-neutral-400 uppercase">
                                                {translate('Seller')}
                                            </h5>
                                            <Link
                                                to="#"
                                                className="text-theme-secondary-light"
                                            >
                                                {shopName}
                                            </Link>
                                        </div>

                                        {carts.map((item) => (
                                            <div
                                                className="px-2 sm:px-4 border-b border-theme-primary-14"
                                                key={item.id}
                                            >
                                                <ProductCardWide
                                                    cartId={item.id}
                                                    product={item.product}
                                                    variation={item.variation}
                                                    quantity={item.qty}
                                                    size="sm"
                                                    counter={false}
                                                    deleteBtn={false}
                                                    onlyPrice={true}
                                                />
                                            </div>
                                        ))}
                                    </div>
                                ),
                            )}
                        </div>

                        <div className="px-3 sm:px-8 py-4 space-y-7">
                            <div className="border border-zinc-100 rounded-md divide-y divide-zinc-100 uppercase">
                                <div className="flex items-center justify-between py-3 sm:py-6 px-3 sm:px-6 md:px-8">
                                    <span>{translate('Subtotal')}</span>
                                    <span>{currencyFormatter(subTotal)}</span>
                                </div>
                                <div className="flex items-center justify-between py-3 sm:py-6 px-3 sm:px-6 md:px-8">
                                    <span>{translate('tax')}</span>
                                    <span>{currencyFormatter(tax)}</span>
                                </div>
                                <div className="flex items-center justify-between py-3 sm:py-6 px-3 sm:px-6 md:px-8">
                                    <span>{translate('discount')}</span>
                                    <span>{currencyFormatter(discount)}</span>
                                </div>
                                <div className="flex items-center justify-between py-3 sm:py-6 px-3 sm:px-6 md:px-8">
                                    <span>{translate('Shipping Charge')}</span>
                                    <span>
                                        {currencyFormatter(shippingCharge)}
                                    </span>
                                </div>
                                <div className="flex items-center justify-between py-3 sm:py-6 px-3 sm:px-6 md:px-8">
                                    <span>{translate('Total')}</span>
                                    <span>{currencyFormatter(total)}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default Checkout;
