import { useFormik } from 'formik';
import toast from 'react-hot-toast';
import { object, string } from 'yup';

import { useSendValidationCodeMutation } from '../../../../store/features/api/userApi';
import { translate } from '../../../../utils/translate';
import Button from '../../../buttons/Button';
import Input from '../../../inputs/Input';
import InputGroup from '../../../inputs/InputGroup';
import Label from '../../../inputs/Label';
import { IAuthPayload } from '../../../../types/payload';

interface Props {
    onSubmit: (values: IAuthPayload) => void;
}

const validationSchema = object().shape({
    phone: string()
        .matches(/^\d+$/, 'Provide a valid phone number')
        .required('Phone is required'),
});

const initialValues: IAuthPayload = {
    authVia: 'phone',
    phone: '',
};

const EmailVerification = ({ onSubmit }: Props) => {
    const [sendValidationCode] = useSendValidationCodeMutation();

    const formik = useFormik({
        initialValues,
        validationSchema,
        onSubmit: async (values) => {
            try {
                const res = await sendValidationCode(values).unwrap();
                toast.success(res.message);
                onSubmit(values);
            } catch (error) {}
        },
    });

    return (
        <form onSubmit={formik.handleSubmit}>
            <h3 className="arm-h2 mb-2">
                {translate('Forgot your password?')}
            </h3>
            <p className="mb-5">
                {translate(
                    'Don’t worry. Please enter your phone to receive a verification code',
                )}
            </p>

            <InputGroup>
                <Label>{translate('phone')}</Label>
                <Input
                    type="text"
                    placeholder={translate('Type your phone')}
                    error={formik.errors.phone}
                    touched={formik.touched.phone}
                    {...formik.getFieldProps('phone')}
                />
            </InputGroup>

            <Button
                type="submit"
                variant="primary"
                className="w-full font-bold mt-5 uppercase"
                isLoading={formik.isSubmitting}
                disabled={formik.isSubmitting}
            >
                {translate('Get Verification Code')}
            </Button>
        </form>
    );
};

export default EmailVerification;
