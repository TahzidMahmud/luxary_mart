import { useEffect } from 'react';
import { useAuth } from '../../store/features/auth/authSlice';
import {
    useGetAddressesQuery,
    useLazyGetShippingChargeQuery,
} from '../../store/features/checkout/checkoutApi';
import {
    setCheckoutData,
    useCheckoutData,
} from '../../store/features/checkout/checkoutSlice';
import { togglePopup } from '../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../store/store';
import { IAddress } from '../../types/checkout';
import AddAddressCard from '../card/AddAddressCard';
import AddressCard from '../card/AddressCard';

interface Props {
    title: string;
    addressType: 'shipping' | 'billing';
}

const CheckoutAddresses = ({ title, addressType }: Props) => {
    const dispatch = useAppDispatch();
    const { carts, userLocation } = useAuth();
    const { shippingAddress, billingAddress, selectedShops, coupons } =
        useCheckoutData();

    const [getShippingCharge] = useLazyGetShippingChargeQuery();

    const { data: addressRes } = useGetAddressesQuery();

    const addresses = addressRes?.addresses;

    // get shipping charge
    useEffect(() => {
        if (!shippingAddress) return;

        const shopIds = carts
            .filter((cart) => selectedShops[cart.shop.name])
            .map((cart) => cart.shop.id)
            // remove duplicates
            .filter((value, index, self) => self.indexOf(value) === index);

        getShippingCharge(
            {
                addressId: shippingAddress.id,
                shopIds,
                coupons: coupons.map((item) => item.code),
            },
            true,
        )
            .unwrap()
            .then((shippingCharge) => {
                dispatch(setCheckoutData({ shippingCharge: shippingCharge }));
            });
    }, [shippingAddress]);

    const selectedAddress =
        addressType === 'shipping' ? shippingAddress : billingAddress;

    const setAddress = (address: IAddress) => {
        const name =
            addressType === 'shipping' ? 'shippingAddress' : 'billingAddress';

        // if address is shipping address, and zoneId of user location and shipping address is not the same
        // update user location and reload the page
        if (
            addressType === 'shipping' &&
            userLocation?.area.zone_id !== address.area.zone_id
        ) {
            dispatch(
                togglePopup({
                    popup: 'confirmation',
                    popupProps: {
                        title: 'Are you sure?',
                        message:
                            'Certain items may not be accessible if you modify the shipping address to a location different from your current one.',
                        address,
                    },
                }),
            );
        } else {
            dispatch(
                setCheckoutData({
                    [name]: address,
                }),
            );
        }
    };

    const clearAddress = () => {
        const name =
            addressType === 'shipping' ? 'shippingAddress' : 'billingAddress';

        dispatch(
            setCheckoutData({
                [name]: undefined,
            }),
        );
    };

    return (
        <div className="rounded-md border border-zinc-100 mt-8">
            <h3 className="py-3.5 px-4 sm:px-5 md:px-7 bg-stone-50 uppercase">
                {title}
            </h3>

            <div className="py-3 xl:py-6 px-3 md:px-6 xl:px-9">
                <div className="grid xs:grid-cols-2 gap-3 text-xs">
                    {addresses?.map((address) => (
                        <AddressCard
                            className={
                                selectedAddress?.id === address.id
                                    ? 'border-theme-secondary-light bg-theme-secondary-light/5'
                                    : ''
                            }
                            address={address}
                            onClick={() => setAddress(address)}
                            onDelete={() => {
                                if (selectedAddress?.id === address.id) {
                                    clearAddress();
                                }
                            }}
                            key={address.id}
                        />
                    ))}

                    <AddAddressCard />
                </div>
            </div>
        </div>
    );
};

export default CheckoutAddresses;
