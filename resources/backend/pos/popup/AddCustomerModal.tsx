import { useFormik } from 'formik';
import React, { useEffect, useState } from 'react';
import toast from 'react-hot-toast';
import { LiaTimesSolid } from 'react-icons/lia';
import { ActionMeta } from 'react-select';
import { object, string } from 'yup';
import Button from '../../react/components/inputs/Button';
import Input from '../../react/components/inputs/Input';
import SelectInput from '../../react/components/inputs/SelectInput';
import { objectToFormData } from '../../react/utils/ObjectFormData';
import { translate } from '../../react/utils/translate';
import { IArea, ICity, ICountry, ICustomer, IState } from '../types';
import { addCustomer, getAreas, getCities, getStates } from '../utils/actions';
import ModalWrapper from './ModalWrapper';

interface Props {
    onSuccess: (customer: ICustomer) => void;
}

interface FormValues {
    name: string;
    phone: string;
    password?: string;
    countryId: number | null;
    stateId: number | null;
    cityId: number | null;
    areaId: number | null;
    postalCode: string;
    address: string;
}

const initialValues: FormValues = {
    name: '',
    phone: '',
    password: '123456',
    countryId: 18,
    stateId: null,
    cityId: null,
    areaId: null,
    postalCode: '',
    address: '',
};

const validationSchema = object().shape({
    name: string().required('Name is required'),
    phone: string(),
    password: string(),
    postalCode: string(),
    address: string(),
});

const AddCustomerModal = ({ onSuccess }: Props) => {
    const [isActive, setIsActive] = useState(false);

    const [states, setStates] = useState<IState[]>([]);
    const [cities, setCities] = useState<ICity[]>([]);
    const [areas, setAreas] = useState<IArea[]>([]);

    const handleClose = () => {
        setIsActive(false);
    };

    const formik = useFormik({
        initialValues,
        validationSchema,
        onSubmit: async (values, helpers) => {
            try {
                const data = await addCustomer(objectToFormData(values));
                setIsActive(false);
                onSuccess(data.newCustomer);
                helpers.resetForm();
            } catch (error) {
                toast.error(
                    error.response?.data?.message || 'Failed to add customer',
                );
            }
        },
    });

    useEffect(() => {
        (async () => {
            const states = await getStates(formik.values.countryId!);
            setStates(states);
        })();
    }, [formik.values.countryId]);

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
        <>
            <Button
                variant="primary"
                className="h-[37px] whitespace-nowrap"
                onClick={() => setIsActive(true)}
            >
                {translate('Add Customer')}
            </Button>

            <ModalWrapper isActive={isActive} onClose={handleClose}>
                <form onSubmit={formik.handleSubmit}>
                    <div className="theme-modal__header">
                        <h5>{translate('Add Customer')}</h5>

                        <button
                            className="text-xl text-white sm:text-theme-secondary-light"
                            type="button"
                            onClick={handleClose}
                        >
                            <LiaTimesSolid />
                        </button>
                    </div>

                    <div className="theme-modal__body space-y-3">
                        <div className="flex gap-5">
                            <label className="min-w-[100px] py-2">
                                {translate('Name')}
                                <span className="text-theme-alert ml-1">*</span>
                            </label>
                            <Input
                                {...formik.getFieldProps('name')}
                                classNames={{
                                    group: 'grow',
                                    inputWrapper: 'max-w-none',
                                }}
                                touched={formik.touched.name}
                                error={formik.errors.name}
                            />
                        </div>
                        <div className="flex gap-5">
                            <label className="min-w-[100px] py-2">
                                {translate('Phone')}
                            </label>
                            <Input
                                {...formik.getFieldProps('phone')}
                                classNames={{
                                    group: 'grow',
                                    inputWrapper: 'max-w-none',
                                }}
                                touched={formik.touched.phone}
                                error={formik.errors.phone}
                            />
                        </div>
                        <div className="flex gap-5">
                            <label className="min-w-[100px] py-2">
                                {translate('Division')}
                            </label>
                            <SelectInput
                                {...formik.getFieldProps('stateId')}
                                name="stateId"
                                options={states}
                                groupClassName="grow"
                                onChange={handleStateChange}
                                getOptionValue={(option: ICountry) =>
                                    option.id.toString()
                                }
                                getOptionLabel={(option: ICountry) =>
                                    option.name
                                }
                                touched={formik.touched.stateId}
                                error={formik.errors.stateId}
                            />
                        </div>
                        <div className="flex gap-5">
                            <label className="min-w-[100px] py-2">
                                {translate('City')}
                            </label>
                            <SelectInput
                                {...formik.getFieldProps('cityId')}
                                name="cityId"
                                options={cities}
                                groupClassName="grow"
                                onChange={handleCityChange}
                                getOptionValue={(option: ICountry) =>
                                    option.id.toString()
                                }
                                getOptionLabel={(option: ICountry) =>
                                    option.name
                                }
                                touched={formik.touched.cityId}
                                error={formik.errors.cityId}
                            />
                        </div>
                        <div className="flex gap-5">
                            <label className="min-w-[100px] py-2">
                                {translate('Area')}
                            </label>
                            <SelectInput
                                {...formik.getFieldProps('areaId')}
                                name="areaId"
                                options={areas}
                                groupClassName="grow"
                                onChange={handleAreaChange}
                                getOptionValue={(option: ICountry) =>
                                    option.id.toString()
                                }
                                getOptionLabel={(option: ICountry) =>
                                    option.name
                                }
                                touched={formik.touched.areaId}
                                error={formik.errors.areaId}
                            />
                        </div>
                        <div className="flex gap-5">
                            <label className="min-w-[100px] py-2">
                                {translate('Postal Code')}
                            </label>
                            <Input
                                {...formik.getFieldProps('postalCode')}
                                classNames={{
                                    group: 'grow',
                                    inputWrapper: 'max-w-none',
                                }}
                                touched={formik.touched.postalCode}
                                error={formik.errors.postalCode}
                            />
                        </div>
                        <div className="flex gap-5">
                            <label className="min-w-[100px] py-2">
                                {translate('Address')}
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
                    </div>

                    <div className="theme-modal__footer">
                        <Button
                            as="button"
                            type="button"
                            variant="light"
                            onClick={handleClose}
                            className="mr-3"
                        >
                            {translate('Cancel')}
                        </Button>
                        <Button variant="primary" as="button" type="submit">
                            {translate('Submit')}
                        </Button>
                    </div>
                </form>
            </ModalWrapper>
        </>
    );
};

export default AddCustomerModal;
