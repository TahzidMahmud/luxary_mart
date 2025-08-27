import { useFormik } from 'formik';
import React, { useEffect, useState } from 'react';
import toast from 'react-hot-toast';
import { ActionMeta } from 'react-select';
import { number, object, string } from 'yup';
import Button from '../../../react/components/inputs/Button';
import Input from '../../../react/components/inputs/Input';
import SelectInput from '../../../react/components/inputs/SelectInput';
import Textarea from '../../../react/components/inputs/Textarea';
import { objectToFormData } from '../../../react/utils/ObjectFormData';
import { translate } from '../../../react/utils/translate';
import { IAddress, IArea, ICity, ICountry, IState } from '../../types';
import {
    addAddress,
    getAreas,
    getCities,
    getStates,
} from '../../utils/actions';

interface Props {
    customerId: number | undefined;
    onSuccess: (address: IAddress) => void;
}

interface FormValues {
    id: number | null;
    countryId: number | null;
    stateId: number | null;
    cityId: number | null;
    areaId: number | null;
    address: string;
    direction?: string;
    type: 'Home' | 'Office' | 'Other';
}

const countryId = 18; // Bangladesh
const stateId = 348; // Dhaka

const initialValues: FormValues = {
    id: null,
    countryId,
    stateId,
    cityId: null,
    areaId: null,
    address: '',
    direction: '',
    type: 'Home',
};

const validationSchema = object().shape({
    id: number().required('Id is required'),
    countryId: number().required('Country is required'),
    stateId: number().required('State is required'),
    cityId: number().required('City is required'),
    areaId: number().required('Area is required'),
    address: string().required('Address is required'),
    direction: string(),
    type: string()
        .oneOf(['Home', 'Office', 'Other'])
        .required('Type is required'),
});

const AddressForm = ({ customerId, onSuccess }: Props) => {
    const [states, setStates] = useState<IState[]>([]);
    const [cities, setCities] = useState<ICity[]>([]);
    const [areas, setAreas] = useState<IArea[]>([]);

    const formik = useFormik({
        initialValues,
        validationSchema,
        onSubmit: async (values, helpers) => {
            try {
                const data = await addAddress(objectToFormData(values));
                onSuccess(data[0]);
                helpers.resetForm();
            } catch (error) {
                toast.error(
                    error.response?.data?.message || 'Failed to add address',
                );
            }
        },
    });

    useEffect(() => {
        getStates(countryId).then(setStates);
        getCities(stateId).then(setCities);
    }, []);

    useEffect(() => {
        formik.setFieldValue('id', customerId);
    }, [customerId]);

    const handleStateChange = async (
        option: any,
        { name }: ActionMeta<any>,
    ) => {
        formik.setFieldValue(name!, option.id);
        formik.setFieldValue('cityId', null);
        formik.setFieldValue('areaId', null);

        setAreas([]);
        const cities = await getCities(option.id);
        setCities(cities);
    };

    const handleCityChange = async (option: any, { name }: ActionMeta<any>) => {
        formik.setFieldValue(name!, option.id);
        formik.setFieldValue('areaId', null);

        const areas = await getAreas(option.id);
        setAreas(areas);
    };

    const handleAreaChange = (option: any, { name }: ActionMeta<any>) => {
        formik.setFieldValue(name!, option.id);
    };

    return (
        <form onSubmit={formik.handleSubmit} className="space-y-4">
            {/* <div className="flex gap-5">
                <label className="min-w-[100px] py-2">
                    {translate('Country')}
                    <span className="text-theme-alert ml-1">*</span>
                </label>
                <SelectInput
                    {...formik.getFieldProps('countryId')}
                    name="countryId"
                    options={countries}
                    groupClassName="grow"
                    onChange={handleCountryChange}
                    getOptionValue={(option: ICountry) => option.id.toString()}
                    getOptionLabel={(option: ICountry) => option.name}
                    touched={formik.touched.countryId}
                    error={formik.errors.countryId}
                />
            </div> */}
            <div className="flex gap-5">
                <label className="min-w-[100px] py-2">
                    {translate('Division')}
                    <span className="text-theme-alert ml-1">*</span>
                </label>
                <SelectInput
                    {...formik.getFieldProps('stateId')}
                    name="stateId"
                    options={states}
                    groupClassName="grow"
                    onChange={handleStateChange}
                    getOptionValue={(option: ICountry) => option.id.toString()}
                    getOptionLabel={(option: ICountry) => option.name}
                    touched={formik.touched.stateId}
                    error={formik.errors.stateId}
                />
            </div>
            <div className="flex gap-5">
                <label className="min-w-[100px] py-2">
                    {translate('City')}
                    <span className="text-theme-alert ml-1">*</span>
                </label>
                <SelectInput
                    {...formik.getFieldProps('cityId')}
                    name="cityId"
                    options={cities}
                    groupClassName="grow"
                    onChange={handleCityChange}
                    getOptionValue={(option: ICountry) => option.id.toString()}
                    getOptionLabel={(option: ICountry) => option.name}
                    touched={formik.touched.cityId}
                    error={formik.errors.cityId}
                />
            </div>
            <div className="flex gap-5">
                <label className="min-w-[100px] py-2">
                    {translate('Area')}
                    <span className="text-theme-alert ml-1">*</span>
                </label>
                <SelectInput
                    {...formik.getFieldProps('areaId')}
                    name="areaId"
                    options={areas}
                    groupClassName="grow"
                    onChange={handleAreaChange}
                    getOptionValue={(option: ICountry) => option.id.toString()}
                    getOptionLabel={(option: ICountry) => option.name}
                    touched={formik.touched.areaId}
                    error={formik.errors.areaId}
                />
            </div>
            <div className="flex gap-5">
                <label className="min-w-[100px] py-2">
                    {translate('Address')}
                    <span className="text-theme-alert ml-1">*</span>
                </label>
                <Input
                    {...formik.getFieldProps('address')}
                    classNames={{
                        group: 'grow',
                        inputWrapper: 'max-w-none',
                    }}
                    touched={formik.touched.address}
                    error={formik.errors.address}
                />
            </div>
            <div className="flex gap-5">
                <label className="min-w-[100px] py-2">
                    {translate('Type')}
                    <span className="text-theme-alert ml-1">*</span>
                </label>
                <SelectInput
                    {...formik.getFieldProps('type')}
                    name="type"
                    options={[
                        { label: 'Home', value: 'Home' },
                        { label: 'Office', value: 'Office' },
                        { label: 'Other', value: 'Other' },
                    ]}
                    onChange={(option: any) =>
                        formik.setFieldValue('type', option.value)
                    }
                    groupClassName="grow"
                    touched={formik.touched.type}
                    error={formik.errors.type}
                />
            </div>
            <div className="flex gap-5">
                <label className="min-w-[100px] py-2">
                    {translate('Direction')}
                </label>
                <Textarea
                    {...formik.getFieldProps('direction')}
                    classNames={{
                        group: 'grow',
                        inputWrapper: 'max-w-none',
                    }}
                    touched={formik.touched.direction}
                    error={formik.errors.direction}
                />
            </div>

            <div>
                <Button as="button" type="submit" variant="secondary">
                    {translate('Add Address')}
                </Button>
            </div>
        </form>
    );
};

export default AddressForm;
