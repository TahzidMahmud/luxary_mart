import { useFormik } from 'formik';
import { useEffect } from 'react';
import { LiaTimesSolid } from 'react-icons/lia';
import { object, string } from 'yup';
import { verifyUser } from '../../../../store/features/auth/authThunks';
import {
    closePopup,
    usePopup,
} from '../../../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../../../store/store';
import { IAuthPayload } from '../../../../types/payload';
import { translate } from '../../../../utils/translate';
import Button from '../../../buttons/Button';
import InputGroup from '../../../inputs/InputGroup';
import Label from '../../../inputs/Label';
import OTPInput from '../../../inputs/OTPInput';
import ModalWrapper from '../../ModalWrapper';

const initialValues: IAuthPayload = {
    authVia: 'phone',
    phone: '',
    code: '',
};

const validationSchema = object().shape({
    phone: string()
        .matches(/^\d+$/, 'Provide a valid phone number')
        .required('Phone is required'),
    code: string().length(6).required('Code is required'),
});

const UserVerificationPopup = () => {
    const dispatch = useAppDispatch();
    const { popup, popupProps } = usePopup();

    const isActive = popup === 'user-verification';

    const formik = useFormik({
        initialValues,
        validationSchema,
        onSubmit: async (values) => {
            try {
                await dispatch(verifyUser(values)).unwrap();
                dispatch(closePopup());
            } catch (error) {}
        },
    });

    useEffect(() => {
        formik.setFieldValue('phone', popupProps.phone);
    }, [popupProps]);

    useEffect(() => {
        if (isActive) {
            formik.setFieldValue('code', '');
        }
    }, [isActive]);

    return (
        <ModalWrapper isActive={isActive}>
            <button
                className="absolute right-5 top-5 text-xl text-white sm:text-theme-secondary-light z-[1]"
                onClick={() => dispatch(closePopup())}
            >
                <LiaTimesSolid />
            </button>

            <div className="theme-modal__body min-h-[595px] flex max-sm:flex-col bg-white p-0">
                <div className="bg-theme-secondary-light text-white flex flex-col justify-center">
                    <div className="flex gap-8 sm:flex-col max-sm:items-center sm:w-[242px] py-4 px-4 sm:px-9">
                        <div className="grow">
                            <h3 className="font-semibold text-base sm:text-2xl">
                                {translate('Verify Your Phone')}
                            </h3>
                            <p className="mt-3">
                                {translate(
                                    'Verify your phone number to recover your password.',
                                )}
                            </p>
                        </div>
                        <img
                            src="/images/auth-2.png"
                            alt=""
                            className="max-xs:max-w-[50px] max-sm:max-w-[100px] max-sm:mr-12"
                        />
                    </div>
                </div>
                <form
                    className="flex-grow flex flex-col justify-center px-4 py-4 sm:p-10"
                    onSubmit={formik.handleSubmit}
                >
                    <h3 className="arm-h2 mb-2">
                        {translate('Verify Your Phone')}
                    </h3>
                    <p className="mb-5">
                        {translate(
                            'Please enter the code sent to your phone to verify',
                        )}
                    </p>

                    <InputGroup>
                        <Label htmlFor="code">{translate('code')}</Label>
                        <OTPInput
                            value={formik.values.code || ''}
                            onChange={(value) =>
                                formik.setFieldValue('code', value)
                            }
                        />
                    </InputGroup>

                    <Button
                        type="submit"
                        variant="primary"
                        className="w-full font-bold mt-5 uppercase"
                        isLoading={formik.isSubmitting}
                        disabled={formik.isSubmitting}
                    >
                        {translate('Verify')}
                    </Button>
                </form>
            </div>
        </ModalWrapper>
    );
};

export default UserVerificationPopup;
