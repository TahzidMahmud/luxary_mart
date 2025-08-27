import { useFormik } from 'formik';
import { useEffect } from 'react';
import toast from 'react-hot-toast';
import { object, ref, string } from 'yup';
import { useResetPasswordMutation } from '../../../../store/features/api/userApi';
import { IAuthPayload } from '../../../../types/payload';
import { translate } from '../../../../utils/translate';
import Button from '../../../buttons/Button';
import Input from '../../../inputs/Input';
import InputGroup from '../../../inputs/InputGroup';
import Label from '../../../inputs/Label';

interface Props {
    data: IAuthPayload;
    onSubmit: (values: IAuthPayload) => void;
}

const validationSchema = object().shape({
    phone: string()
        .matches(/^\d+$/, 'Provide a valid phone number')
        .required('Phone is required'),
    password: string()
        .min(6, 'Password must be at least 6 characters')
        .required('Password is required'),
    passwordConfirmation: string()
        .required('Password Confirmation is required')
        .oneOf([ref('password')], 'Passwords must match'),
});

const initialValues: IAuthPayload = {
    authVia: 'phone',
    phone: '',
    code: '',
    password: '',
    passwordConfirmation: '',
};

const ResetPassword = ({ data, onSubmit }: Props) => {
    const [resetPassword] = useResetPasswordMutation();

    const formik = useFormik({
        initialValues,
        validationSchema,
        onSubmit: async (values) => {
            try {
                const res = await resetPassword(values).unwrap();
                toast.success(res.message);
                onSubmit(values);
            } catch (error) {}
        },
    });

    useEffect(() => {
        formik.setFieldValue('phone', data.phone || '');
        formik.setFieldValue('code', data.code || '');
    }, [data]);

    return (
        <form onSubmit={formik.handleSubmit}>
            <h3 className="arm-h2 mb-2">{translate('Set New Password')}</h3>
            <p className="mb-5">
                {translate('Please enter your new password and confirm it.')}
            </p>

            <InputGroup>
                <Label>{translate('password')}</Label>
                <Input
                    type="password"
                    toggleBtn={false}
                    placeholder={translate('Type your password')}
                    error={formik.errors.password}
                    touched={formik.touched.password}
                    {...formik.getFieldProps('password')}
                />
            </InputGroup>

            <InputGroup className="mt-5">
                <Label>{translate('Confirm Password')}</Label>
                <Input
                    type="password"
                    toggleBtn={false}
                    placeholder={translate('Type password again')}
                    error={formik.errors.passwordConfirmation}
                    touched={formik.touched.passwordConfirmation}
                    {...formik.getFieldProps('passwordConfirmation')}
                />
            </InputGroup>

            <Button
                type="submit"
                variant="primary"
                className="w-full font-bold mt-5 uppercase"
                isLoading={formik.isSubmitting}
                disabled={formik.isSubmitting}
            >
                {translate('Change Password')}
            </Button>
        </form>
    );
};

export default ResetPassword;
