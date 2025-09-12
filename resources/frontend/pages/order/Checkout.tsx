import { useEffect, useState } from 'react';
import toast from 'react-hot-toast';
import { FaHome } from 'react-icons/fa';
import { useDispatch } from 'react-redux';
import { Link, useNavigate, useSearchParams } from 'react-router-dom';
import { object, string } from 'yup';
import { sendOTP } from '../../../backend/react/utils/actions';
import Breadcrumb from '../../components/Breadcrumb';
import Button from '../../components/buttons/Button';
import ProductCardWide from '../../components/card/ProductCardWide';
import CustomerDetailsForm from '../../components/order/CustomerDetailsForm';
import PaymentMethodForm from '../../components/order/PaymentMethodForm';
import SelectAddress from '../../components/order/SelectAddress';
import SimpleAddressForm from '../../components/order/SimpleAddressForm';
import {
    useCreateManualOrderMutation,
    useCreateOrderMutation,
} from '../../store/features/api/orderApi';
import { useLazyGetCartProductsQuery } from '../../store/features/api/productApi';
import { useAuth } from '../../store/features/auth/authSlice';
import {
    setCheckoutData,
    useCheckoutData,
} from '../../store/features/checkout/checkoutSlice';
import { togglePopup } from '../../store/features/popup/popupSlice';
import { IManualOrderPayload } from '../../types/checkout';
import { ICartProduct } from '../../types/product';
import { getSelectedShopProducts } from '../../utils/getSelectedShopProducts';
import { currencyFormatter } from '../../utils/numberFormatter';
import { translate } from '../../utils/translate';

export const customerDetailsSchema = object().shape({
    name: string().required('Name is required'),
    email: string().email('Invalid email'),
    phone: string().required('Phone is required'),
    alternatePhone: string(),
    note: string(),
});

export type TStep = 'details' | 'shipping' | 'payment';
const steps: TStep[] = ['details'];
const stepQueryKey = 'checkoutStep';

const Checkout = () => {
    const [searchParams, setSearchParams] = useSearchParams();
    const dispatch = useDispatch();

    const navigate = useNavigate();
    const [createOrder, { isLoading: creatingOrder }] =
        useCreateOrderMutation();
    const [createManualOrder, { isLoading: creatingMnualOrder }] =
        useCreateManualOrderMutation();
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
        isVerified,
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
    const checkIfNotProduction=()=>{
         return window.config.generalSettings.env =="local"?true:false;
    }
    const [showFullForm, setShowFullForm] = useState(()=>{
        return window.config.generalSettings.env =="local"?checkIfNotProduction:isVerified;

    });
    console.log(showFullForm, window.config.generalSettings.env =="local"?checkIfNotProduction:isVerified);
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
        if(!checkIfNotProduction()){
            setShowFullForm(isVerified);
            dispatch(setCheckoutData({ isVerified: true }));

        }
    }, [isVerified]);
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
        try {
            // validate customer details before going to step 2
            await customerDetailsSchema.validate(customerDetails, {
                abortEarly: false,
            });
        } catch (err: any) {
            toast.error(err.errors[0]);
            return;
        }
        if (currentStep === 1 && !shippingAddress) {
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

    const sendOTPVerification = async () => {
        try {
            await customerDetailsSchema.validate(customerDetails, {
                abortEarly: false,
            });

            await sendOTP({
                name: customerDetails.name,
                phone: customerDetails.phone,
            });
            const expiryTime = Date.now() + 15 * 60 * 1000; // 15 minutes from now

            dispatch(setCheckoutData({
                otpExpiryTimestamp: expiryTime,
            }));
            toast.success('OTP sent to your phone');
            dispatch(togglePopup('otp-verification'));
        } catch (err: any) {
            toast.error(err.errors?.[0] || 'Failed to send OTP');
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
        if (
            shippingAddress &&
            shippingAddress.stateId &&
            shippingAddress.cityId &&
            shippingAddress.address
        ) {
            const payload: IManualOrderPayload = {
                ...customerDetails,
                shippingAddress: shippingAddress,
                billingAddress: billingAddress,
                shopIds: selectedShopIds,
                couponCodes: coupons.map((coupon) => coupon.code),
                cartIds: selectedCartIds,
                paymentMethod,
            };
            try {
                const orderGroup = await createManualOrder(payload).unwrap();
                getCartProducts(); // refresh cart products
                navigate(`/orders/success/${orderGroup.orderCode}`);
            } catch (err: any) {
                toast.error(err.data.message);
                navigate(`/orders/failed`);
            }
        } else {
            toast.error('Please Fill Address Form');
        }
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

            <div className="container no-style grid lg:grid-cols-3 mb-12">
                <div className="lg:col-span-2">
                    <div className="bg-white rounded-card p-3 sm:p-6 md:p-9">
                        <CustomerDetailsForm />

                        {showFullForm && (
                            <>
                                <div className="rounded-card border border-zinc-100 mt-8">
                                    <h3 className="py-3.5 px-4 sm:px-5 md:px-7 bg-stone-50 uppercase">
                                        SHIPPING ADDRESS
                                    </h3>
                                    <div className=" px-3 md:px-6 xl:px-9">
                                        <SimpleAddressForm />
                                    </div>
                                </div>

                                <div className="mt-3">
                                    <PaymentMethodForm />
                                </div>
                            </>
                        )}

                        <div className="flex justify-between mt-7">
                            {currentStep === 1 ? (
                                <Button
                                    as="link"
                                    to="/cart"
                                    size="lg"
                                    onClick={handlePrev}
                                    variant="outline"
                                    arrow={'left'}
                                >
                                    {translate('Back To Cart')}
                                </Button>
                            ) : (
                                <Button
                                    size="lg"
                                    onClick={handlePrev}
                                    variant="outline"
                                    arrow={'left'}
                                >
                                    {translate('Previous Step')}
                                </Button>
                            )}
                            {showFullForm ? (
                                <Button
                                    variant="primary"
                                    size="lg"
                                    onClick={handleNext}
                                    isLoading={creatingMnualOrder}
                                    disabled={
                                        creatingMnualOrder ||
                                        (currentStep === steps.length &&
                                            !window.config.paymentMethods
                                                .length)
                                    }
                                >
                                    {currentStep === steps.length
                                        ? translate('Place Order')
                                        : translate('Next Step')}
                                </Button>
                            ) : (
                                <Button
                                    variant="primary"
                                    size="lg"
                                    onClick={sendOTPVerification}
                                >
                                    {translate('Verify phone')}
                                </Button>
                            )}
                        </div>
                    </div>
                </div>

                <div>
                    <div className="bg-white rounded-card overflow-hidden">
                        <h3 className="py-3 px-4 sm:px-5 md:px-8 bg-primary text-white">
                            {translate('Order Summary')}
                        </h3>

                        <div className="">
                            {Object.entries(selectedShopProducts).map(
                                ([shopName, carts]) => (
                                    <div key={shopName}>
                                        <div className="flex items-center justify-between text-sm border-b border-primary-14 h-8 sm:h-12 px-4 bg-stone-50">
                                            <h5 className="text-black/70 uppercase">
                                                {translate('Seller')}
                                            </h5>
                                            <Link
                                                to="#"
                                                className="text-primary"
                                            >
                                                {shopName}
                                            </Link>
                                        </div>

                                        {carts.map((item) => (
                                            <div
                                                className="px-2 sm:px-4 border-b border-primary-14"
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
                            <div className="border border-zinc-100 rounded-card divide-y divide-zinc-100 uppercase">
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
