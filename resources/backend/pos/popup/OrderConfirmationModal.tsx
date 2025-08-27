import dayjs from 'dayjs';
import React from 'react';
import { LiaTimesSolid } from 'react-icons/lia';
import { currencyFormatter } from '../../../frontend/utils/numberFormatter';
import Button from '../../react/components/inputs/Button';
import { translate } from '../../react/utils/translate';
import { IOrderSubmitData } from '../components/forms/OrderSubmitForm';
import { IAddress, ICustomer, IPosCartGroup } from '../types';
import ModalWrapper from './ModalWrapper';

interface Props {
    isModalOpen: boolean;
    data: IOrderSubmitData;
    posCartGroup?: IPosCartGroup;
    customer?: ICustomer;
    shippingAddress?: IAddress | null;

    onConfirm: () => void;
    onCancel?: () => void;
}

const OrderConfirmationModal = ({
    isModalOpen,
    data,
    posCartGroup,
    customer,
    shippingAddress,
    onConfirm,
    onCancel,
}: Props) => {
    const handleClose = () => onCancel?.();

    return (
        <>
            <ModalWrapper
                isActive={isModalOpen}
                size="lg"
                onClose={handleClose}
            >
                <div>
                    <div className="theme-modal__header">
                        <h5>{translate('Order Confirmation')}</h5>

                        <button
                            className="text-xl text-white sm:text-theme-secondary-light"
                            type="button"
                            onClick={handleClose}
                        >
                            <LiaTimesSolid />
                        </button>
                    </div>

                    <div className="theme-modal__body space-y-3">
                        <div className="pt-4 space-y-8 bg-white">
                            <div className="">
                                <table className="w-full mb-5">
                                    <tbody>
                                        <tr>
                                            <td className="w-1/2">
                                                <img
                                                    src="https://luxuryonlinemart.com/public/uploads/all/pP7JMy3pAwdNWgR9hegbgfdLJXa2p7ywh0JZSzpi.png"
                                                    height="70"
                                                    className="inline-block"
                                                />
                                            </td>
                                            <td className="w-1/2"></td>
                                        </tr>
                                    </tbody>
                                </table>

                                <table>
                                    <tbody className="text-sm">
                                        <tr>
                                            <td className="pr-10 space-y-2">
                                                <p className="inline-block px-2 border border-green-600 text-green-600 mb-3">
                                                    Billing From
                                                </p>

                                                <p className="text-base font-bold">
                                                    Luxury Online Mart
                                                </p>

                                                <p>
                                                    House 29/B, Road 1/D,
                                                    Nikunja-2, Khilkhet, Dhaka
                                                </p>

                                                <div className="space-x-2">
                                                    <span className="font-bold">
                                                        Mobile:
                                                    </span>
                                                    <span className="text-sm">
                                                        01306915635
                                                    </span>
                                                </div>

                                                <div className="space-x-2">
                                                    <span className="font-bold">
                                                        Email:
                                                    </span>
                                                    <span className="text-sm">
                                                        luxuryonlinemart@gmail.com
                                                    </span>
                                                </div>

                                                <div className="space-x-2">
                                                    <span className="font-bold">
                                                        Website
                                                    </span>
                                                    <span className="text-sm">
                                                        https://www.luxuryonlinemart.com/
                                                    </span>
                                                </div>
                                            </td>
                                            <td className="space-y-2">
                                                <div className="text-left text-sm">
                                                    <span>Billing Date:</span>
                                                    <span>
                                                        {dayjs().format(
                                                            'DD MMM, YYYY',
                                                        )}
                                                    </span>
                                                </div>

                                                <div className="inline-block px-2 border border-green-600 text-green-600 mb-3">
                                                    Ship To
                                                </div>

                                                <div className="space-x-2">
                                                    <span className="font-bold">
                                                        Name:
                                                    </span>
                                                    <span className="text-sm">
                                                        {customer?.name}
                                                    </span>
                                                </div>

                                                {/* <div className="space-x-2">
                                                    <span className="font-bold">
                                                        Mobile:
                                                    </span>
                                                    <span className="text-sm">
                                                        
                                                    </span>
                                                </div> */}

                                                <div className="space-x-2">
                                                    <span className="font-bold">
                                                        Address:
                                                    </span>
                                                    <span className="text-sm">
                                                        {
                                                            shippingAddress?.address
                                                        }
                                                        ,{' '}
                                                        {
                                                            shippingAddress
                                                                ?.area?.name
                                                        }
                                                        ,{' '}
                                                        {
                                                            shippingAddress
                                                                ?.city.name
                                                        }
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div className="">
                                <hr className="mb-3" />
                                <table className="w-full">
                                    <thead>
                                        <tr>
                                            <th className="w-3/5"></th>
                                            <th className="w-2/5"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                Delivery Type: Cash On Delivery
                                            </td>
                                            <td>
                                                <p>
                                                    Payment Status:
                                                    {data.advancePayment ===
                                                    data.payingAmount
                                                        ? 'Paid'
                                                        : 'Due'}
                                                </p>
                                                <p className="mt-2">
                                                    Payment Method: Cash On
                                                    Delivery
                                                </p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div className="text-sm">
                                <table className="border-t-2 w-full">
                                    <thead>
                                        <tr>
                                            <th className="text-left py-2 text-sm w-[30px]">
                                                SL.
                                            </th>
                                            <th className="text-left py-2 text-sm w-[80px]">
                                                Image
                                            </th>
                                            <th className="text-left py-2 text-sm">
                                                Product
                                            </th>
                                            <th className="text-left py-2 text-sm w-[100px]">
                                                Quantity
                                            </th>
                                            <th className="text-left py-2 text-sm w-[100px]">
                                                Rate
                                            </th>
                                            <th className="text-left py-2 text-sm w-[100px]">
                                                Amount
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody className="border-t [&_td]:py-2">
                                        {posCartGroup?.posCarts.map(
                                            (item, idx) => (
                                                <tr key={item.id}>
                                                    <td>{idx + 1}</td>
                                                    <td>
                                                        <img
                                                            height="50"
                                                            src={
                                                                item.product?.thumbnailImg
                                                            }
                                                        />
                                                    </td>
                                                    <td className="text-sm">
                                                        <p>
                                                            {item.product?.name}
                                                        </p>
                                                        <p>
                                                            Variation -{' '}
                                                            {
                                                                item.variation?.name
                                                            }
                                                        </p>
                                                    </td>

                                                    <td className="text-sm">
                                                        {item.qty}
                                                    </td>

                                                    <td className="text-sm">
                                                        {currencyFormatter(
                                                            item.variation?.discountedBasePrice,
                                                        )}
                                                    </td>
                                                    <td className="text-sm">
                                                        {currencyFormatter(
                                                            item.qty *
                                                                item.variation?.discountedBasePrice,
                                                        )}
                                                    </td>
                                                </tr>
                                            ),
                                        )}
                                    </tbody>
                                </table>
                            </div>

                            <div className="p-16 pb-4">
                                <table className="w-full">
                                    <thead>
                                        <tr>
                                            <th className="w-3/5"></th>
                                            <th className="w-2/5"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td></td>
                                            <td className="flex justify-end">
                                                <table className="py-3">
                                                    <tbody>
                                                        <tr className="py-2">
                                                            <th className="text-left font-bold py-2 text-sm">
                                                                Total Discount:
                                                            </th>
                                                            <td className="py-2 text-right font-medium text-sm pl-20">
                                                                <span className="py-2">
                                                                    {currencyFormatter(
                                                                        data.discount,
                                                                    )}
                                                                </span>
                                                            </td>
                                                        </tr>

                                                        <tr className="py-2">
                                                            <th className="text-left font-bold py-2 text-sm">
                                                                Tax:
                                                            </th>
                                                            <td className="py-2 text-right font-medium text-sm pl-20">
                                                                <span className="py-2">
                                                                    {currencyFormatter(
                                                                        data.tax,
                                                                    )}
                                                                </span>
                                                            </td>
                                                        </tr>

                                                        <tr className="py-2">
                                                            <th className="text-left font-bold py-2 text-sm">
                                                                Shipping Cost:
                                                            </th>
                                                            <td className="py-2 text-right font-medium text-sm pl-20">
                                                                <span className="py-2">
                                                                    {currencyFormatter(
                                                                        data.shippingCharge,
                                                                    )}
                                                                </span>
                                                            </td>
                                                        </tr>

                                                        <tr className="py-2">
                                                            <th className="text-left font-extrabold py-2 text-sm">
                                                                <span className="py-2">
                                                                    Grand Total:
                                                                </span>
                                                            </th>
                                                            <td className="py-2 text-right font-medium text-sm pl-20">
                                                                <span className="py-2">
                                                                    {currencyFormatter(
                                                                        data.payingAmount,
                                                                    )}
                                                                </span>
                                                            </td>
                                                        </tr>

                                                        <tr className="py-2">
                                                            <th className="text-left font-bold py-2 text-sm">
                                                                Advance:
                                                            </th>
                                                            <td className="py-2 text-right font-medium text-sm pl-20">
                                                                <span className="py-2">
                                                                    {currencyFormatter(
                                                                        data.advancePayment,
                                                                    )}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        <tr className="py-2">
                                                            <th className="text-left font-bold py-2 text-sm">
                                                                Due:
                                                            </th>
                                                            <td className="py-2 text-right font-medium text-sm pl-20">
                                                                {currencyFormatter(
                                                                    data.payingAmount -
                                                                        (data.advancePayment ||
                                                                            0),
                                                                )}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div className="theme-modal__footer pt-5">
                        <Button
                            as="button"
                            type="button"
                            variant="light"
                            onClick={handleClose}
                            className="mr-3"
                        >
                            {translate('Cancel')}
                        </Button>
                        <Button
                            variant="primary"
                            as="button"
                            onClick={onConfirm}
                        >
                            {translate('Confirm Order')}
                        </Button>
                    </div>
                </div>
            </ModalWrapper>
        </>
    );
};

export default OrderConfirmationModal;
