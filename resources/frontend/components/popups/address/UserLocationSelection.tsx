import { useFormik } from 'formik';
import { useEffect, useState } from 'react';
import toast from 'react-hot-toast';
import { LiaTimesSolid } from 'react-icons/lia';
import { object } from 'yup';
import { setAuthData, useAuth } from '../../../store/features/auth/authSlice';
import {
    useLazyGetAreasQuery,
    useLazyGetCitiesQuery,
    useLazyGetStatesQuery,
} from '../../../store/features/checkout/checkoutApi';
import { setCheckoutData } from '../../../store/features/checkout/checkoutSlice';
import { closePopup, usePopup } from '../../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../../store/store';
import { STORAGE_KEYS } from '../../../types';
import {
    IArea,
    ICity,
    ILocallyStoredUserAddress,
    IState,
} from '../../../types/checkout';
import { translate } from '../../../utils/translate';
import Button from '../../buttons/Button';
import InputGroup from '../../inputs/InputGroup';
import Label from '../../inputs/Label';
import SelectInput from '../../inputs/SelectInput';
import ModalWrapper from '../ModalWrapper';

const initialValues: Partial<ILocallyStoredUserAddress> = {
    country: {
        id: 18,
        name: 'Bangladesh',
    },
    state: undefined,
    city: undefined,
    area: undefined,
};

const UserLocationSelection = () => {
    const dispatch = useAppDispatch();
    const { popup, popupProps } = usePopup();
    const { userLocation } = useAuth();
    const isActive = popup === 'user-location';

    const [getState, { isFetching: isStateLoading }] = useLazyGetStatesQuery();
    const [getCity, { isFetching: isCityLoading }] = useLazyGetCitiesQuery();
    const [getArea, { isFetching: isAreaLoading }] = useLazyGetAreasQuery();

    const [states, setStates] = useState<IState[]>([]);
    const [cities, setCities] = useState<ICity[]>([]);
    const [areas, setAreas] = useState<IArea[]>([]);

    const validationSchema = object().shape({
        country: object().required(translate('Country is required')),
        state: object().required(translate('State is required')),
        city: object().required(translate('Select a city')),
        area: object().required(translate('Select an area')),
    });

    const formik = useFormik({
        initialValues,
        validationSchema,
        onSubmit: async (values) => {
            const newUserLocation: ILocallyStoredUserAddress = {
                country: {
                    id: values.country!.id,
                    name: values.country!.name,
                },
                state: {
                    id: values.state!.id,
                    name: values.state!.name,
                },
                city: {
                    id: values.city!.id,
                    name: values.city!.name,
                },
                area: {
                    id: values.area!.id,
                    name: values.area!.name,
                    zone_id: values.area!.zone_id,
                },
            };

            toast.success(translate('Location updated successfully'));
            localStorage.setItem(
                STORAGE_KEYS.USER_LOCATION_KEY,
                JSON.stringify(newUserLocation),
            );
            dispatch(
                setAuthData({
                    userLocation: newUserLocation,
                }),
            );

            // clear checkout data when user location is changed
            // so that shipping charge are recalculated
            dispatch(
                setCheckoutData({
                    shippingAddress: undefined,
                }),
            );

            dispatch(closePopup());

            if (popupProps.redirectOnSuccess) {
                window.location.href = popupProps.redirectOnSuccess;
            } else {
                window.location.reload();
            }
        },
    });

    useEffect(() => {
        getState({ countryId: 18 })
            .unwrap()
            .then((states) => setStates(states || []));

        if (!userLocation) return;
        setLocationToFormik(userLocation);
    }, []);

    const setLocationToFormik = async (location: ILocallyStoredUserAddress) => {
        // since we don't need to wait for one to finish to start the other
        // getting state, city, area can be done in parallel
        getState({ countryId: location.country.id })
            .unwrap()
            .then((states) => setStates(states || []));

        getCity({ stateId: location.state.id })
            .unwrap()
            .then((cities) => setCities(cities || []));

        getArea({ cityId: location.city.id })
            .unwrap()
            .then((areas) => setAreas(areas || []));

        formik.setValues(location);
    };

    // const handleCountryChange = async (option: ICountry) => {
    //     formik.setFieldValue('country', option);
    //     formik.setFieldValue('state', null);
    //     formik.setFieldValue('city', null);
    //     formik.setFieldValue('area', null);

    //     setCities([]);
    //     setAreas([]);
    //     const states = await getState({ countryId: option.id }, true).unwrap();
    //     setStates(states);
    // };

    const handleStateChange = async (option: IState) => {
        formik.setFieldValue('state', option);
        formik.setFieldValue('city', null);
        formik.setFieldValue('area', null);

        setAreas([]);
        const cities = await getCity({ stateId: option.id }).unwrap();
        setCities(cities);
    };

    const handleCityChange = async (option: ICity) => {
        formik.setFieldValue('city', option);
        formik.setFieldValue('area', null);

        const areas = await getArea({ cityId: option.id }).unwrap();
        setAreas(areas);
    };

    const handleAreaChange = (option: IArea) => {
        formik.setFieldValue('area', option);
    };

    return (
        <ModalWrapper size="sm" isActive={isActive} className="rounded-md">
            <div className="relative bg-theme-secondary-light text-white p-4 sm:p-8">
                <div className="max-w-[340px] mx-auto flex items-center gap-4">
                    <div>
                        <img src="/images/location.png" alt="location" />
                    </div>
                    <div>
                        <h4 className="text-base sm:text-2xl font-semibold mb-1">
                            {translate('Select Location')}
                        </h4>
                        <p>
                            {translate(
                                'You will be shown shops or products available in your location',
                            )}
                        </p>
                    </div>
                </div>

                <button
                    className="absolute right-5 top-5 text-xl z-[1]"
                    onClick={() => dispatch(closePopup())}
                >
                    <LiaTimesSolid />
                </button>
            </div>

            <div className="mx-auto px-[15px] max-w-[370px] pt-7 pb-[57px]">
                {userLocation ? (
                    <div className="bg-theme-secondary-light/10 py-3 px-7 rounded-md mb-5">
                        <h5 className="text-xs mb-1">
                            {translate('Selected Location')}
                        </h5>
                        <p className="text-neutral-500">
                            {userLocation?.area.name}, {userLocation?.city.name}
                            , {userLocation?.state.name},{' '}
                            {userLocation?.country.name}
                        </p>
                    </div>
                ) : null}

                <form onSubmit={formik.handleSubmit} className={'space-y-3'}>
                    <InputGroup>
                        <Label>{translate('Division')}</Label>
                        <SelectInput
                            placeholder={translate('Select a division')}
                            name="state"
                            value={String(formik.values.state?.id)}
                            isLoading={isStateLoading}
                            onChange={handleStateChange}
                            options={states || []}
                            getOptionLabel={(option: IState) => option.name}
                            getOptionValue={(option: IState) =>
                                String(option.id)
                            }
                            error={formik.errors.state}
                            touched={formik.touched.state}
                        />
                    </InputGroup>

                    <InputGroup>
                        <Label>{translate('city')}</Label>
                        <SelectInput
                            placeholder={translate('Select a city')}
                            name="city"
                            value={formik.values.city?.id}
                            isLoading={isCityLoading}
                            onChange={handleCityChange}
                            options={cities || []}
                            getOptionLabel={(option: ICity) => option.name}
                            getOptionValue={(option: ICity) =>
                                String(option.id)
                            }
                            error={formik.errors.city}
                            touched={formik.touched.city}
                        />
                    </InputGroup>

                    <InputGroup>
                        <Label>{translate('area')}</Label>
                        <SelectInput
                            placeholder={translate('Select an area')}
                            name="area"
                            value={formik.values.area?.id}
                            isLoading={isAreaLoading}
                            onChange={handleAreaChange}
                            options={areas || []}
                            getOptionLabel={(option: IArea) => option.name}
                            getOptionValue={(option: IArea) =>
                                String(option.id)
                            }
                            error={formik.errors.area}
                            touched={formik.touched.area}
                        />
                    </InputGroup>

                    <div className="mt-6">
                        <Button
                            type="submit"
                            className="w-full font-bold"
                            disabled={false}
                        >
                            {translate('Set Location')}
                        </Button>
                    </div>
                </form>
            </div>
        </ModalWrapper>
    );
};

export default UserLocationSelection;
