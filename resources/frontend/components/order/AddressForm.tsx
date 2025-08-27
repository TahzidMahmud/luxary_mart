import { useFormik } from 'formik';
import { useEffect, useState } from 'react';
import toast from 'react-hot-toast';
import { useDispatch } from 'react-redux';
import { ActionMeta } from 'react-select';
import { object, string } from 'yup';
import {
    useAddAddressMutation,
    useLazyGetAreasQuery,
    useLazyGetCitiesQuery,
    useLazyGetStatesQuery,
    useUpdateAddressMutation,
} from '../../store/features/checkout/checkoutApi';
import { closePopup } from '../../store/features/popup/popupSlice';
import { IAddress, IArea, ICity, IState } from '../../types/checkout';
import { IAddressPayload } from '../../types/payload';
import { cn } from '../../utils/cn';
import { translate } from '../../utils/translate';
import Button from '../buttons/Button';
import Input from '../inputs/Input';
import InputGroup from '../inputs/InputGroup';
import Label from '../inputs/Label';
import SelectInput from '../inputs/SelectInput';
import Textarea from '../inputs/Textarea';

interface Props {
    address?: IAddress;
    className?: string;
    onSuccess?: (address: IAddress) => void;
}

const initialValues: IAddressPayload = {
    countryId: 18,
    stateId: undefined,
    cityId: undefined,
    areaId: undefined,
    address: '',
    direction: '',
    postalCode: '',
    type: 'home',
};

const AddressForm = ({ address, className, onSuccess }: Props) => {
    const dispatch = useDispatch();
    const [addAddress] = useAddAddressMutation();
    const [updateAddress] = useUpdateAddressMutation();

    const [getState, { isFetching: isStateLoading }] = useLazyGetStatesQuery();
    const [getCity, { isFetching: isCityLoading }] = useLazyGetCitiesQuery();
    const [getArea, { isFetching: isAreaLoading }] = useLazyGetAreasQuery();

    const [states, setStates] = useState<IState[]>([]);
    const [cities, setCities] = useState<ICity[]>([]);
    const [areas, setAreas] = useState<IArea[]>([]);

    const validationSchema = object().shape({
        countryId: string().required('Country is required'),
        stateId: string().required('State is required'),
        cityId: string().required('Select a city'),
        areaId: string().required('Select an area'),
        address: string().required('Address is required'),
        direction: string(),
        postalCode: string(),
        type: string()
            .oneOf(['home', 'office', 'other'])
            .required('Save as is required'),
    });

    const formik = useFormik({
        initialValues,
        validationSchema,
        onSubmit: async (values, formikHelpers) => {
            try {
                if (address) {
                    const updatedAddress = await updateAddress({
                        ...values,
                        id: address.id,
                    }).unwrap();

                    toast.success(translate('Address updated successfully!'));
                    onSuccess?.(updatedAddress);
                } else {
                    const newAddress = await addAddress(values).unwrap();

                    toast.success(translate('Address saved successfully!'));
                    onSuccess?.(newAddress);
                }

                formikHelpers.resetForm();
                dispatch(closePopup());
            } catch (error: any) {
                toast.error(error.data.message);
            }
        },
    });

    const setAddressToFormik = async (address: IAddress) => {
        // since we don't need to wait for one to finish to start the other
        // getting state, city, area can be done in parallel
        getState({ countryId: address.country.id })
            .unwrap()
            .then((states) => setStates(states || []));

        getCity({ stateId: address.state.id })
            .unwrap()
            .then((cities) => setCities(cities || []));

        getArea({ cityId: address.city.id })
            .unwrap()
            .then((areas) => setAreas(areas || []));

        formik.setValues({
            countryId: address.country.id,
            stateId: address.state.id,
            cityId: address.city.id,
            areaId: address.area.id,
            address: address.address,
            direction: address.direction || '',
            postalCode: address.postalCode || '',
            type: address.type,
        });
    };

    useEffect(() => {
        if (!address) return;

        setAddressToFormik(address);
    }, [address]);

    useEffect(() => {
        getState({ countryId: formik.values.countryId! }, true) // 18 is the country id for Bangladesh
            .unwrap()
            .then((states) => setStates(states));
    }, []);

    const handleStateChange = async (
        option: any,
        { name }: ActionMeta<any>,
    ) => {
        formik.setFieldValue(name!, option.id);
        formik.setFieldValue('cityId', null);
        formik.setFieldValue('areaId', null);

        setAreas([]);
        const cities = await getCity({ stateId: option.id }).unwrap();
        setCities(cities);
    };

    const handleCityChange = async (option: any, { name }: ActionMeta<any>) => {
        formik.setFieldValue(name!, option.id);
        formik.setFieldValue('areaId', null);

        const areas = await getArea({ cityId: option.id }).unwrap();
        setAreas(areas);
    };

    const handleAreaChange = (option: any, { name }: ActionMeta<any>) => {
        formik.setFieldValue(name!, option.id);
    };

    const handleInputChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        formik.setFieldValue(e.target.name, e.target.value);
    };

    const handleTextareaChange = (
        e: React.ChangeEvent<HTMLTextAreaElement>,
    ) => {
        formik.setFieldValue(e.target.name, e.target.value);
    };

    return (
        <form
            onSubmit={formik.handleSubmit}
            className={cn(
                'grid grid-cols-1 sm:grid-cols-2 gap-4 pt-6 border-t border-theme-primary-14 mt-8',
                className,
            )}
        >
            <InputGroup>
                <Label>{translate('Division')}</Label>
                <SelectInput
                    placeholder={translate('Select a division')}
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
                <Label>{translate('City')}</Label>
                <SelectInput
                    placeholder={translate('Select a city')}
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

            <InputGroup>
                <Label>{translate('Area')}</Label>
                <SelectInput
                    placeholder={translate('Select an area')}
                    name="areaId"
                    value={formik.values.areaId}
                    isLoading={isAreaLoading}
                    onChange={handleAreaChange}
                    options={areas || []}
                    getOptionLabel={(option: IArea) => option.name}
                    getOptionValue={(option: IArea) => String(option.id)}
                    error={translate(formik.errors.areaId)}
                    touched={formik.touched.areaId}
                />
            </InputGroup>

            <InputGroup>
                <Label>{translate('Post Code')}</Label>
                <Input
                    name="postalCode"
                    placeholder={translate('Type the postal code...')}
                    value={formik.values.postalCode}
                    onChange={handleInputChange}
                    error={translate(formik.errors.postalCode)}
                    touched={formik.touched.postalCode}
                />
            </InputGroup>

            <InputGroup>
                <Label>{translate('Save As')}</Label>
                <SelectInput
                    name="saveAs"
                    placeholder="Save as “home”, “office” etc..."
                    value={formik.values.type}
                    options={[
                        { label: translate('home'), value: 'home' },
                        { label: translate('office'), value: 'office' },
                        { label: translate('other'), value: 'other' },
                    ]}
                    onChange={(option) =>
                        formik.setFieldValue('type', option.value)
                    }
                    error={translate(formik.errors.type)}
                    touched={formik.touched.type}
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

            <InputGroup className="sm:col-span-2">
                <Label>{translate('Direction')}</Label>
                <Textarea
                    name="direction"
                    placeholder={translate('Give some direction...')}
                    value={formik.values.direction}
                    onChange={handleTextareaChange}
                />
            </InputGroup>

            <div>
                <Button
                    as="button"
                    type="submit"
                    variant="warning"
                    isLoading={formik.isSubmitting}
                >
                    {translate('Save Address')}
                </Button>
            </div>
        </form>
    );
};

export default AddressForm;
