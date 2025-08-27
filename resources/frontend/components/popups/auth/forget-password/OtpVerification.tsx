import { useFormik } from 'formik';
import { useEffect } from 'react';
import toast from 'react-hot-toast';
import { object, string } from 'yup';
import { useVerifyCodeMutation } from '../../../../store/features/api/userApi';
import { IAuthPayload } from '../../../../types/payload';
import { translate } from '../../../../utils/translate';
import Button from '../../../buttons/Button';
import InputGroup from '../../../inputs/InputGroup';
import Label from '../../../inputs/Label';
import OTPInput from '../../../inputs/OTPInput';

interface Props {
    data: IAuthPayload;
    onSubmit: (values: IAuthPayload) => void;
}

const validationSchema = object().shape({
    phone: string()
        .matches(/^\d+$/, 'Provide a valid phone number')
        .required('Phone is required'),
    code: string().length(6).required('Code is required'),
});

const initialValues: IAuthPayload = {
    authVia: 'phone',
    phone: '',
    code: '',
};

const OtpVerification = ({ data, onSubmit }: Props) => {
    const [verifyCode] = useVerifyCodeMutation();

    const formik = useFormik({
        initialValues,
        validationSchema,
        onSubmit: async (values) => {
            try {
                const res = await verifyCode(values).unwrap();
                toast.success(res.message);
                onSubmit(values);
            } catch (error) {}
        },
    });

    useEffect(() => {
        formik.setValues({
            authVia: 'phone',
            phone: data.phone,
            code: '',
        });
    }, [data]);

    useEffect(() => {
        formik.setValues({
            authVia: 'phone',
            phone: data.phone,
            code: '',
        });
    }, []);

    return (
        <form onSubmit={formik.handleSubmit}>
            <h3 className="arm-h2 mb-2">{translate('Verify Your Phone')}</h3>
            <p className="mb-5">
                {translate(
                    'Please enter the code sent to your phone to verify',
                )}
            </p>

            <InputGroup>
                <Label htmlFor="code">{translate('code')}</Label>
                <OTPInput
                    value={formik.values.code || ''}
                    onChange={(value) => formik.setFieldValue('code', value)}
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
    );
};

export default OtpVerification;
