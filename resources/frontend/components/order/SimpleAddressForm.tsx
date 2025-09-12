import { ICartProduct } from '../../types/product';
import { useFormik } from 'formik';
import { useEffect, useState } from 'react';
import toast from 'react-hot-toast';
import { useDispatch } from 'react-redux';
import { ActionMeta } from 'react-select';
import { object, string } from 'yup';
import { useAuth } from '../../store/features/auth/authSlice';
import {
    useLazyGetCitiesQuery,
    useLazyGetShippingChargeQuery,
    useLazyGetStatesQuery,
} from '../../store/features/checkout/checkoutApi';
import {
    setCheckoutData,
    useCheckoutData,
} from '../../store/features/checkout/checkoutSlice';
import { closePopup } from '../../store/features/popup/popupSlice';
import {
    IAddress,
    ICity,
    ICountry,
    IShippingCharge,
    IState,
} from '../../types/checkout';
import { AddressPayload } from '../../types/payload';
import { cn } from '../../utils/cn';
import { translate } from '../../utils/translate';
import InputGroup from '../inputs/InputGroup';
import Label from '../inputs/Label';
import SelectInput from '../inputs/SelectInput';
import Textarea from '../inputs/Textarea';
interface Props {
    // address?: IAddress;
    className?: string;
    // onSuccess?: (address: IAddress) => void;
}

const SimpleAddressForm = ({ className }: Props) => {
    const dispatch = useDispatch();
    const { shippingAddress, billingAddress, selectedShops, coupons } =
        useCheckoutData();
    const initialValues: AddressPayload = {
        countryId: shippingAddress ? shippingAddress.countryId : undefined,
        stateId: shippingAddress ? shippingAddress.stateId : undefined,
        cityId: shippingAddress ? shippingAddress.cityId : undefined,
        areaId: undefined,
        address: shippingAddress ? shippingAddress.address : '',
        direction: '',
        postalCode: '',
        type: 'home',
    };
    const [getState, { isFetching: isStateLoading }] = useLazyGetStatesQuery();
    const [getCity, { isFetching: isCityLoading }] = useLazyGetCitiesQuery();
    const [getShippingCharge, { isFetching: isShippingChargeLoading }] =
        useLazyGetShippingChargeQuery();

    const [states, setStates] = useState<IState[]>([]);
    const [cities, setCities] = useState<ICity[]>([]);
    const [shippingCharge, setShippingCharge] = useState<IShippingCharge[]>([]);
    const [selectedState, setSelectedState] = useState<IState | null>(null);
    const [selectedCity, setSelectedCity] = useState<ICity | null>(null);
    const [address, setAddress] = useState<string | null>(null);
    const countries = window.config.countries;
    const { carts, user, guestUserId } = useAuth();
    const selectedShopProducts: Record<string, ICartProduct[]> = {};
    Object.keys(selectedShops).forEach((shopName) => {
        if (!selectedShops[shopName]) return;

        const products = carts.filter((cart) => cart.shop.name === shopName);
        selectedShopProducts[shopName] = products;
    });

    const selectedShopIds = Object.values(selectedShopProducts)
        .map((products) => products[0]?.shop?.id)
        .filter((id, i, arr) => id && arr.indexOf(id) === i);

    const selectedCartIds = Object.values(selectedShopProducts)
        .map((products) => products.map((product) => product.id))
        .flat();

    const validationSchema = object().shape({
        countryId: string().required('Country is required'),
        stateId: string().required('State is required'),
        cityId: string().required('Select a city'),
    });
    const [addressData, setAddressData] = useState(() => ({
        id: 0,
        countryId: 18,
        country: {
            id: 1,
            code: 'BD',
            name: 'Bangladesh',
            is_active: 1,
        },
    }));
    const setAddressToFormik = async () => {
        formik.setValues({
            countryId: shippingAddress ? shippingAddress.countryId : 18,
            stateId: shippingAddress ? shippingAddress.stateId : undefined,
            cityId: shippingAddress ? shippingAddress.cityId : undefined,
            areaId: undefined,
            address: shippingAddress ? shippingAddress.address : '',
            direction: '',
            postalCode: '',
            type: 'home',
        });
    };

    useEffect(() => {
        setAddressToFormik();
    }, []);
    useEffect(() => {
        if (selectedState) {
            setAddressData((prev) => ({
                ...prev,
                stateId: selectedState.id,
                state: selectedState,
            }));
        }
        if (selectedCity) {
            setAddressData((prev) => ({
                ...prev,
                cityId: selectedCity.id,
                city: selectedCity,
            }));
        }
        if (address) {
            setAddressData((prev) => ({
                ...prev,
                address: address,
            }));
        }
        if (selectedCity && selectedState && address && address != '') {
            dispatch(
                setCheckoutData({
                    shippingAddress: addressData as IAddress,
                    billingAddress: addressData as IAddress,
                }),
            );
        }
    }, [selectedState, selectedCity, address]);

    const formik = useFormik({
        initialValues,
        validationSchema,
        onSubmit: async (values, formikHelpers) => {
            try {
                formikHelpers.resetForm();
                dispatch(closePopup());
            } catch (error: any) {
                toast.error(error.data.message);
            }
        },
    });

    useEffect(() => {
        handleCountryChange();
    }, []);
    useEffect(() => {
        if (!formik.values.cityId) {
            dispatch(setCheckoutData({ shippingCharge: 0 }));
        }
    }, []);

    const handleCountryChange = async () => {
        formik.setFieldValue('countryId', 18);
        formik.setFieldValue('stateId', null);
        formik.setFieldValue('cityId', null);
        setCities([]);
        const states = await getState({ countryId: 18 }, true).unwrap();
        setStates(states);
        setAddressToFormik();
    };

    const handleStateChange = async (
        option: any,
        { name }: ActionMeta<any>,
    ) => {
        formik.setFieldValue(name!, option.id);
        formik.setFieldValue('cityId', null);
        const cities = await getCity({ stateId: option.id }).unwrap();
        states.map((item: IState) => {
            if (item.id == option.id) {
                setSelectedState(item);
            }
        });
        setCities(cities);
    };

    const handleCityChange = async (option: any, { name }: ActionMeta<any>) => {
        formik.setFieldValue(name!, option.id);

        const selectedCity = cities.find(
            (item: ICity) => item.id === option.id,
        );
        if (selectedCity) {
            setSelectedCity(selectedCity);
        }
        // Call the API only if data exists
        // if (option.id) {
        //     try {
        //         const shippingChargeData = await getShippingCharge({
        //             cityId: option.id,
        //             userId: user?.id ?? 0,
        //             guestUserId: guestUserId ?? 0,
        //             shopIds: selectedShopIds,
        //             cartIds: selectedCartIds,
        //             coupons: coupons.map((c) => c.code),
        //         }).unwrap();

        //         dispatch(
        //             setCheckoutData({ shippingCharge: shippingChargeData }),
        //         );
        //     } catch (error) {
        //         console.error('Shipping charge error:', error);
        //         toast.error('Failed to calculate shipping charge');
        //     }
        // }
    };

    const handleTextareaChange = (
        e: React.ChangeEvent<HTMLTextAreaElement>,
    ) => {
        formik.setFieldValue(e.target.name, e.target.value);
        setAddress(e.target.value);
    };

    return (
        <form
            onSubmit={formik.handleSubmit}
            className={cn(
                'grid grid-cols-1 sm:grid-cols-2 gap-4 pb-4 ',
                className,
            )}
        >
            <InputGroup className="hidden">
                <Label>{translate('country')}</Label>
                <SelectInput
                    placeholder={translate('Select a country')}
                    name="countryId"
                    value={18}
                    onChange={handleCountryChange}
                    options={countries || []}
                    getOptionLabel={(option: ICountry) => option.name}
                    getOptionValue={(option: ICountry) => String(option.id)}
                    error={translate(formik.errors.countryId)}
                    touched={formik.touched.countryId}
                />
            </InputGroup>

            <InputGroup>
                <Label>{translate('District')}</Label>
                <SelectInput
                    placeholder={translate('Select a District')}
                    name="stateId"
                    value={String(formik.values.stateId)}
                    isLoading={isStateLoading}
                    onChange={handleStateChange}
                    options={states || []}
                    getOptionLabel={(option: IState) => option.name}
                    getOptionValue={(option: IState) => String(option.id)}
                    error={translate(formik.errors.stateId)}
                    touched={formik.touched.stateId}
                />
            </InputGroup>

            <InputGroup>
                <Label>{translate('City/Town')}</Label>
                <SelectInput
                    placeholder={translate('Select a city/town')}
                    name="cityId"
                    value={formik.values.cityId}
                    isLoading={isCityLoading}
                    onChange={handleCityChange}
                    options={cities || []}
                    getOptionLabel={(option: ICity) => option.name}
                    getOptionValue={(option: ICity) => String(option.id)}
                    error={translate(formik.errors.cityId)}
                    touched={formik.touched.cityId}
                />
            </InputGroup>
            <InputGroup className="sm:col-span-2">
                <Label>{translate('address')}</Label>
                <Textarea
                    name="address"
                    placeholder={translate('Give full address...')}
                    value={formik.values.address}
                    onChange={handleTextareaChange}
                    error={translate(formik.errors.address)}
                    touched={formik.touched.address}
                />
            </InputGroup>
        </form>
    );
};

export default SimpleAddressForm;
