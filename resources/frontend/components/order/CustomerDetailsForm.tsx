import {
    setCheckoutData,
    useCheckoutData,
} from '../../store/features/checkout/checkoutSlice';
import { useAppDispatch } from '../../store/store';
import { translate } from '../../utils/translate';
import Input from '../inputs/Input';
import InputGroup from '../inputs/InputGroup';
import Label from '../inputs/Label';
import Textarea from '../inputs/Textarea';

const CustomerDetailsForm = () => {
    const dispatch = useAppDispatch();
    const { customerDetails } = useCheckoutData();

    const setCustomerDetails = (e: any) => {
        const { name, value } = e.target;

        dispatch(
            setCheckoutData({
                customerDetails: {
                    ...customerDetails,
                    [name]: value,
                },
            }),
        );
    };

    return (
        <div className="rounded-md border border-zinc-100">
            <h3 className="py-3.5 px-4 sm:px-5 md:px-7 bg-stone-50 uppercase">
                {translate('Customer Details')}
            </h3>

            <div className="py-4 sm:py-6 px-2 sm:px-5 md:px-9 grid sm:grid-cols-2 gap-4">
                <InputGroup>
                    <Label>{translate('name')}</Label>
                    <Input
                        name="name"
                        type="text"
                        placeholder={translate('Type Your Full Name')}
                        value={customerDetails?.name}
                        onChange={setCustomerDetails}
                    />
                </InputGroup>

                {/* <InputGroup>
                    <Label>{translate('email')}</Label>
                    <Input
                        name="email"
                        type="email"
                        placeholder={translate('Type Your Email')}
                        value={customerDetails?.email}
                        onChange={setCustomerDetails}
                    />
                </InputGroup> */}

                <InputGroup>
                    <Label>{translate('phone')}</Label>
                    <Input
                        name="phone"
                        type="text"
                        placeholder={translate('Valid Phone No.')}
                        value={customerDetails?.phone}
                        onChange={setCustomerDetails}
                    />
                </InputGroup>

                <InputGroup>
                    <Label>{translate('Alternate Phone')}</Label>
                    <Input
                        name="alternatePhone"
                        type="text"
                        placeholder={translate('Valid Phone No.')}
                        value={customerDetails?.alternatePhone}
                        onChange={setCustomerDetails}
                    />
                </InputGroup>

                <InputGroup className="sm:col-span-2">
                    <Label>{translate('note')}</Label>
                    <Textarea
                        name="note"
                        placeholder={translate('Type Your Note')}
                        rows={4}
                        value={customerDetails?.note}
                        onChange={setCustomerDetails}
                    />
                </InputGroup>
            </div>
        </div>
    );
};

export default CustomerDetailsForm;
