import { FaLocationDot } from 'react-icons/fa6';
import { IoIosArrowDown } from 'react-icons/io';
import { useAuth } from '../../../../store/features/auth/authSlice';
import { togglePopup } from '../../../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../../../store/store';
import { translate } from '../../../../utils/translate';

const LocationToggler = () => {
    const dispatch = useAppDispatch();
    const { userLocation } = useAuth();

    return (
        <button
            className="flex items-center"
            onClick={() => dispatch(togglePopup('user-location'))}
        >
            <span className="mr-2">
                <FaLocationDot />
            </span>
            {!userLocation ? (
                translate('Select Location')
            ) : (
                <>
                    {userLocation.area.name}, {userLocation.city.name}
                </>
            )}
            <span className="ml-0.5">
                <IoIosArrowDown />
            </span>
        </button>
    );
};

export default LocationToggler;
