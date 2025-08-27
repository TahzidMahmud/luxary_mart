import { useFormik } from 'formik';
import { LiaTimesSolid } from 'react-icons/lia';
import { Link } from 'react-router-dom';
import { boolean, object, string } from 'yup';
import { signupUser } from '../../../store/features/auth/authThunks';
import {
    closePopup,
    togglePopup,
    usePopup,
} from '../../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../../store/store';
import { IAuthPayload } from '../../../types/payload';
import { translate } from '../../../utils/translate';
import Button from '../../buttons/Button';
import SocialLogin from '../../buttons/SocialLogin';
import Checkbox from '../../inputs/Checkbox';
import Input from '../../inputs/Input';
import InputGroup from '../../inputs/InputGroup';
import Label from '../../inputs/Label';
import ModalWrapper from '../ModalWrapper';

const signupSchema = object().shape({
    name: string().required('Name is required'),

    phone: string()
        .matches(/^\d+$/, 'Provide a valid phone number')
        .required('Phone is required'),
    password: string()
        .min(6, 'Password must be at least 6 characters')
        .required('Password is required'),
    agree: boolean().oneOf(
        [true],
        'You must agree to the terms and conditions',
    ),
});

const initialValues: IAuthPayload = {
    name: '',
    authVia: 'phone',
    phone: '',
    password: '',
    agree: false,
};

const Signup = () => {
    const { popup } = usePopup();
    const dispatch = useAppDispatch();
    const isActive = popup === 'signup';

    const formik = useFormik({
        initialValues,
        validateOnChange: false,
        validationSchema: signupSchema,
        onSubmit: async (values) => {
            try {
                const res = await dispatch(signupUser(values)).unwrap();

                if (res.authStatus === 'verify') {
                    dispatch(
                        togglePopup({
                            popup: 'user-verification',
                            popupProps: {
                                phone: values.phone,
                            },
                        }),
                    );
                } else {
                    dispatch(closePopup());
                }
            } catch (err: any) {}
        },
    });

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
                                {translate('Register Your Account')}
                            </h3>
                            <p className="mt-3">
                                {translate(
                                    'And you will be able to track your orders and get special promotions',
                                )}
                            </p>
                        </div>
                        <img
                            src="/images/auth.png"
                            alt=""
                            className="max-xs:max-w-[50px] max-sm:max-w-[100px] max-sm:mr-12"
                        />
                    </div>
                </div>
                <div className="flex-grow flex flex-col justify-center px-4 py-4 sm:p-10">
                    <form
                        className="space-y-2.5"
                        onSubmit={formik.handleSubmit}
                    >
                        <InputGroup>
                            <Label htmlFor="name">{translate('name')}</Label>
                            <Input
                                type="text"
                                id="name"
                                placeholder={translate('Type Your Full Name')}
                                error={formik.errors.name}
                                touched={formik.touched.name}
                                {...formik.getFieldProps('name')}
                            />
                        </InputGroup>

                        <InputGroup>
                            <Label htmlFor="phone">{translate('phone')}</Label>
                            <Input
                                id="phone"
                                placeholder={translate(
                                    'Provide a valid phone number',
                                )}
                                error={formik.errors.phone}
                                touched={formik.touched.phone}
                                {...formik.getFieldProps('phone')}
                                type="text"
                            />
                        </InputGroup>

                        <InputGroup>
                            <Label htmlFor="password">
                                {translate('password')}
                            </Label>
                            <Input
                                type="password"
                                id="password"
                                placeholder={translate('Type Your Password')}
                                error={formik.errors.password}
                                touched={formik.touched.password}
                                {...formik.getFieldProps('password')}
                            />
                        </InputGroup>

                        <Checkbox
                            id="agree"
                            className="!mt-4"
                            label={
                                <span className="text-xs">
                                    {translate('By registering i agree to the')}{' '}
                                    <Link to="#">
                                        {translate('Terms and Conditions')}
                                    </Link>
                                </span>
                            }
                            error={formik.errors.agree}
                            touched={formik.touched.agree}
                            {...formik.getFieldProps('agree')}
                        />

                        <div className="">
                            <Button
                                type="submit"
                                className="w-full font-bold"
                                isLoading={formik.isSubmitting}
                            >
                                {translate('Register')}
                            </Button>
                        </div>
                    </form>

                    <div className="mt-6">
                        <div className="relative flex items-center justify-center mb-3">
                            <div className="px-3 bg-white relative z-[1]">
                                {translate('Register with social')}
                            </div>
                            <span className="absolute border-b border-theme-primary-14 w-full left-0 top-1/2 -translate-y-1/2"></span>
                        </div>

                        <SocialLogin />

                        <div className="text-center text-xs text-zinc-500 mt-5">
                            {translate('Already have an account?')}
                            <button
                                onClick={() => dispatch(togglePopup('signin'))}
                                className="ml-1 text-theme-secondary-light"
                            >
                                {translate('Sign In')}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </ModalWrapper>
    );
};

export default Signup;
