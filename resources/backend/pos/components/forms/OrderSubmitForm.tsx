import dayjs from 'dayjs';
import { useFormik } from 'formik';
import React, { useEffect, useState } from 'react';
import toast from 'react-hot-toast';
import { IoHandRightOutline } from 'react-icons/io5';
import { useSearchParams } from 'react-router-dom';
import { number, object, string } from 'yup';
import { currencyFormatter } from '../../../../frontend/utils/numberFormatter';
import Button from '../../../react/components/inputs/Button';
import Input from '../../../react/components/inputs/Input';
import SelectInput from '../../../react/components/inputs/SelectInput';
import Textarea from '../../../react/components/inputs/Textarea';
import { objectToFormData } from '../../../react/utils/ObjectFormData';
import { translate } from '../../../react/utils/translate';
import OrderConfirmationModal from '../../popup/OrderConfirmationModal';
import { IAddress, ICustomer, IPosCartGroup } from '../../types';
import { submitOrder } from '../../utils/actions';

export interface IOrderSubmitData {
    tax?: number;
    posCartGroupId: number | null;
    discount: number;
    shippingCharge: number;
    paymentMethod: string;

    shippingAddressId?: number;
    customerId?: number | null;
    note?: string;

    receivedAmount: number | null;
    advancePayment: number | null;
    payingAmount: number;
    orderReceivingDate: string | Date;
    orderShipmentDate: string | Date;
}

interface Props {
    posCartGroup?: IPosCartGroup;
    shippingAddress?: IAddress | null;
    customer?: ICustomer;

    handleHoldOrder: () => void;
    handleResetOrder: () => void;
    onSuccess?: () => void;
}

const paymentMethods = [
    // { label: 'Cash', value: 'cash' },
    // { label: 'Card', value: 'card' },
    // { label: 'Card on Delivery', value: 'card_on_delivery' },
    { label: 'Cash on Delivery', value: 'cash_on_delivery' },
];

const validationSchema = object().shape({
    posCartGroupId: number().required('Cart is required'),
    receivedAmount: number(),
    advancePayment: number(),
    payingAmount: number(),
    paymentMethod: string().oneOf([
        'cash_on_delivery',
        // 'cash',
        // 'card',
        // 'card_on_delivery',
    ]),

    orderReceivingDate: string(),
    orderShipmentDate: string(),
    orderGroupId: number(),
});

const initialValues: IOrderSubmitData = {
    posCartGroupId: null,
    discount: 0,
    shippingCharge: 0,
    paymentMethod: 'cash_on_delivery',
    note: '',

    receivedAmount: 0,
    advancePayment: 0,
    payingAmount: 0,
    orderReceivingDate: dayjs().format('YYYY-MM-DD'),
    orderShipmentDate: dayjs().add(1, 'day').format('YYYY-MM-DD'),
};

const OrderSubmitForm = ({
    posCartGroup,
    customer,
    shippingAddress,

    handleHoldOrder,
    handleResetOrder,
    onSuccess,
}: Props) => {
    const [searchParams] = useSearchParams();
    const [isModalOpen, setIsModalOpen] = useState(false);

    const formik = useFormik({
        initialValues,
        validationSchema,
        onSubmit: async () => {
            if (!customer) {
                toast.error('Please select customer');
                return;
            }
            try {
                if (!posCartGroup?.posCarts.length) {
                    toast.error('Cart is empty');
                    return;
                }

                setIsModalOpen(true);
            } catch (error: any) {
                toast.error(
                    error.response.data.message || 'Failed to submit order',
                );
            }
        },
    });

    useEffect(() => {
        if (!posCartGroup) return;

        const tax =
            posCartGroup?.posCarts.reduce(
                (acc, posCart) => acc + posCart.variation?.tax * posCart.qty,
                0,
            ) || 0;

        formik.setValues({
            ...formik.values,
            tax,
            posCartGroupId: (posCartGroup.id || posCartGroup.posCartGroupId)!,
            shippingCharge: posCartGroup.shippingCharge || 0,
            discount: posCartGroup.discount || 0,
            advancePayment: posCartGroup.advance || 0,
            customerId: posCartGroup.customerId,
            note: posCartGroup.note,
            orderReceivingDate: posCartGroup.orderReceivingDate || new Date(),
            orderShipmentDate: posCartGroup.orderShipmentDate || new Date(),
            paymentMethod:
                posCartGroup.paymentMethod || initialValues.paymentMethod,
        });
    }, [posCartGroup]);

    useEffect(() => {
        if (!posCartGroup) return;

        const { discount, shippingCharge } = formik.values;

        const payingAmount =
            posCartGroup.posCarts.reduce(
                (total, cart) =>
                    total +
                    cart.qty * cart.variation?.discountedBasePriceWithTax,
                0,
            ) -
            Number(discount) +
            Number(shippingCharge);

        formik.setFieldValue('payingAmount', payingAmount);
    }, [posCartGroup, formik.values.discount, formik.values.shippingCharge]);

    useEffect(() => {
        formik.setFieldValue('customerId', customer?.id);
        formik.setFieldValue('shippingAddressId', shippingAddress?.id);
    }, [customer, shippingAddress]);

    const handleSubmitOrder = async () => {
        try {
            const res = await submitOrder(
                objectToFormData({
                    ...formik.values,
                    orderGroupId:
                        Number(searchParams.get('orderGroupId')) || null,
                }),
            );
            toast.success('Order submitted successfully');

            window.open(`/admin/invoice-download/${res.orderId}`, '_blank');

            setIsModalOpen(false);
            formik.resetForm();
            onSuccess?.();
        } catch (error: any) {
            toast.error(error.message);
        }
    };

    return (
        <>
            <form onSubmit={formik.handleSubmit}>
                <div className="card__content">
                    <div className="grid grid-cols-3 gap-3.5">
                        <div className="">
                            <label className="mb-2">
                                {translate('Discount')}
                            </label>
                            <Input
                                {...formik.getFieldProps('discount')}
                                placeholder={currencyFormatter(0)}
                            />
                        </div>
                        <div className="">
                            <label className="mb-2">
                                {translate('Shipping')}
                            </label>
                            <Input
                                placeholder={currencyFormatter(0)}
                                {...formik.getFieldProps('shippingCharge')}
                            />
                        </div>
                        <div className="">
                            <label className="mb-2">
                                {translate('Advance')}
                            </label>
                            <Input
                                {...formik.getFieldProps('advancePayment')}
                                type="number"
                                classNames={{
                                    group: 'grow',
                                    inputWrapper: 'max-w-none',
                                }}
                                touched={formik.touched.advancePayment}
                                error={formik.errors.advancePayment}
                                placeholder={currencyFormatter(0)}
                            />
                        </div>
                    </div>

                    <div className="text-center py-5 text-theme-primary bg-theme-secondary-light/40 dark:text-foreground rounded-md text-xl font-bold mt-5">
                        TOTAL PAYABLE:{' '}
                        {currencyFormatter(formik.values.payingAmount)}
                    </div>

                    <div className="space-y-3 mt-12">
                        <div className="flex gap-5">
                            <label className="min-w-[150px] py-2">
                                {translate('Payment Method')}
                                <span className="text-theme-alert ml-1">*</span>
                            </label>
                            <SelectInput
                                value={formik.values.paymentMethod}
                                options={paymentMethods}
                                onChange={(option) =>
                                    formik.setFieldValue(
                                        'paymentMethod',
                                        option.value,
                                    )
                                }
                                groupClassName="grow"
                            />
                        </div>

                        <div className="!mt-8">
                            <p>Order Receiving & Shipment Info</p>
                            <p className="text-rose-500 mt-2">
                                {translate(
                                    "If You don't Pick receiving & shipment date it will be automatically set as today and next day",
                                )}
                            </p>
                        </div>

                        <div className="flex gap-5">
                            <label className="min-w-[150px] py-2">
                                {translate('Receiving Date')}
                            </label>
                            <Input
                                type="date"
                                classNames={{
                                    group: 'grow',
                                    inputWrapper: 'max-w-none',
                                }}
                                {...formik.getFieldProps('orderReceivingDate')}
                            />
                        </div>

                        <div className="flex gap-5">
                            <label className="min-w-[150px] py-2">
                                {translate('Shipment Date')}
                            </label>
                            <Input
                                type="date"
                                classNames={{
                                    group: 'grow',
                                    inputWrapper: 'max-w-none',
                                }}
                                {...formik.getFieldProps('orderShipmentDate')}
                            />
                        </div>

                        <div className="flex gap-5">
                            <label className="min-w-[150px] py-2">
                                {translate('Note')}
                            </label>
                            <Textarea
                                {...formik.getFieldProps('note')}
                                classNames={{
                                    group: 'grow',
                                    inputWrapper: 'max-w-none',
                                }}
                                rows={4}
                                placeholder={translate('Note')}
                            />
                        </div>
                    </div>
                </div>

                <div className="card__footer grid grid-cols-3 gap-3.5 border-t border-border pt-8">
                    <Button
                        variant="warning"
                        as="button"
                        type="button"
                        disabled={!posCartGroup}
                        onClick={handleHoldOrder}
                    >
                        <span className="text-2xl">
                            <IoHandRightOutline />
                        </span>
                        {translate('Hold Order')}
                    </Button>
                    <Button
                        variant="danger"
                        as="button"
                        type="button"
                        onClick={handleResetOrder}
                    >
                        {translate('Rest All')}
                    </Button>
                    <Button variant="primary" as="button" type="submit">
                        {translate('Pay Now')}
                    </Button>
                </div>
            </form>

            <OrderConfirmationModal
                isModalOpen={isModalOpen}
                data={formik.values}
                posCartGroup={posCartGroup}
                customer={customer}
                onConfirm={handleSubmitOrder}
                shippingAddress={shippingAddress}
                onCancel={() => setIsModalOpen(false)}
            />
        </>
    );
};

export default OrderSubmitForm;
