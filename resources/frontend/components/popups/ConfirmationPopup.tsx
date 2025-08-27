import { LiaTimesSolid } from 'react-icons/lia';
import { setAuthData } from '../../store/features/auth/authSlice';
import { updateZone } from '../../store/features/auth/authThunks';
import { setCheckoutData } from '../../store/features/checkout/checkoutSlice';
import { closePopup, usePopup } from '../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../store/store';
import { STORAGE_KEYS } from '../../types';
import { ILocallyStoredUserAddress } from '../../types/checkout';
import { translate } from '../../utils/translate';
import Button from '../buttons/Button';
import ModalWrapper from './ModalWrapper';

const ConfirmationPopup = () => {
    const dispatch = useAppDispatch();
    const { popup, popupProps } = usePopup();
    const isActive = popup === 'confirmation';

    const handleConfirm = () => {
        const newUserLocation: ILocallyStoredUserAddress = {
            country: {
                id: popupProps.address.country.id,
                name: popupProps.address.country.name,
            },
            state: {
                id: popupProps.address.state.id,
                name: popupProps.address.state.name,
            },
            city: {
                id: popupProps.address.city.id,
                name: popupProps.address.city.name,
            },
            area: {
                id: popupProps.address.area.id,
                name: popupProps.address.area.name,
                zone_id: popupProps.address.area.zone_id,
            },
        };

        localStorage.setItem(
            STORAGE_KEYS.USER_LOCATION_KEY,
            JSON.stringify(newUserLocation),
        );
        dispatch(
            setAuthData({
                userLocation: newUserLocation,
            }),
        );
        dispatch(setCheckoutData({ shippingAddress: popupProps.address }));
        dispatch(updateZone(popupProps.address.area.zone_id));
        dispatch(closePopup());
    };

    const handleCancel = () => {
        dispatch(closePopup());
    };

    return (
        <ModalWrapper isActive={isActive} className="max-w-[400px] rounded-md">
            <div className="flex justify-between px-5 py-4 border-b border-gray-200">
                <h4 className="arm-h3">{translate('Confirmation')}</h4>

                <button
                    className="text-lg"
                    onClick={() => dispatch(closePopup())}
                >
                    <LiaTimesSolid />
                </button>
            </div>

            <div className="px-5 pt-3 pb-6">
                <h3 className="text-lg font-medium mb-2">{popupProps.title}</h3>
                <p>{popupProps.message}</p>

                <div className="flex gap-3 mt-6">
                    <Button variant="danger" onClick={handleConfirm}>
                        {translate('Confirm')}
                    </Button>
                    <Button variant="light" onClick={handleCancel}>
                        {translate('Cancel')}
                    </Button>
                </div>
            </div>
        </ModalWrapper>
    );
};

export default ConfirmationPopup;
