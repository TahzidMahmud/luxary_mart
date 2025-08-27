import { useEffect } from 'react';
import toast from 'react-hot-toast';
import { BsCheck } from 'react-icons/bs';
import { FaRegCopy } from 'react-icons/fa';
import { LiaTimesSolid } from 'react-icons/lia';
import { useLazyGetCouponDetailsQuery } from '../../../store/features/api/shopApi';
import { closePopup, usePopup } from '../../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../../store/store';
import { currencyFormatter } from '../../../utils/numberFormatter';
import { translate } from '../../../utils/translate';
import Copy from '../../Copy';
import Button from '../../buttons/Button';
import ModalWrapper from '../ModalWrapper';

const CouponDetails = () => {
    const dispatch = useAppDispatch();
    const { popup, popupProps } = usePopup();
    const isActive = popup === 'coupon-details';
    const [getCouponDetails, { data: coupon, isLoading }] =
        useLazyGetCouponDetailsQuery();

    useEffect(() => {
        if (isActive) {
            getCouponDetails(popupProps?.couponCode, true)
                .unwrap()
                .then((coupon) => {
                    // if coupon not found
                    // close the popup and show toast
                    if (!coupon) {
                        dispatch(closePopup());
                        toast.error(translate('Coupon not found'));
                    }
                });
        }
    }, [isActive]);

    return (
        <ModalWrapper isActive={isActive}>
            <button
                className="absolute right-5 top-5 text-xl text-white z-[1]"
                onClick={() => dispatch(closePopup())}
            >
                <LiaTimesSolid />
            </button>
            <div className="theme-modal__body min-h-[595px] w-full bg-white p-0">
                <div className="flex max-sm:flex-col items-center justify-between py-5 sm:py-8 md:py-12 gap-4 px-4 sm:px-9 md:px-[52px] bg-gradient-to-r from-theme-primary to-theme-secondary-light text-white">
                    <div className="leading-none">
                        {isLoading ? (
                            <span className="skeleton bg-indigo-500 h-10"></span>
                        ) : (
                            <span className="text-[40px] font-semibold">
                                {coupon?.discountType === 'percentage'
                                    ? coupon?.discount + '%'
                                    : currencyFormatter(coupon?.discount)}
                            </span>
                        )}
                        <br />
                        <span className="text-base font-normal">
                            {coupon?.discountType === 'percentage'
                                ? 'Percentage Discount'
                                : 'Flat Discount'}
                        </span>
                    </div>

                    {!isLoading && (
                        <table>
                            <tbody>
                                <tr>
                                    <td>{translate('Valid until')}:</td>
                                    <td className="pl-3">{coupon?.endDate}</td>
                                </tr>
                                <tr>
                                    <td>{translate('Min Order')}:</td>
                                    <td className="pl-3">
                                        {currencyFormatter(coupon?.minSpend)}
                                    </td>
                                </tr>
                                <tr>
                                    <td>{translate('Max Discount')}:</td>
                                    <td className="pl-3">
                                        {currencyFormatter(
                                            coupon?.maxDiscountAmount,
                                        )}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    )}

                    <div>
                        <p className="mb-0.5">{translate('Coupon code')}</p>
                        <Copy copyText={coupon?.code!}>
                            <Button
                                variant="no-color"
                                className="h-[37px] min-w-[90px] bg-orange-300 hover:bg-orange-400"
                            >
                                <span className="!text-black font-semibold uppercase">
                                    {coupon?.code}
                                </span>
                                <span className="text-lg">
                                    <FaRegCopy />
                                </span>
                            </Button>
                        </Copy>
                    </div>
                </div>

                <div className="py-5 sm:pt-8 sm:pb-7 px-4 sm:px-9 md:px-[52px]">
                    <h4 className="arm-h2">
                        {translate('Terms and Conditions')}
                    </h4>

                    <ul className="mt-4 text-sm text-[#3D3D3D] list-check space-y-2">
                        {isLoading ? (
                            <>
                                <li className="skeleton bg-gray-300 w-1/2"></li>
                                <li className="skeleton bg-gray-300 w-1/4"></li>
                                <li className="skeleton bg-gray-300 w-1/3"></li>
                                <li className="skeleton bg-gray-300 w-3/4"></li>
                            </>
                        ) : (
                            coupon?.conditions.map((condition) => (
                                <li key={condition.id}>
                                    <span className="pt-1 text-lg text-theme-secondary-light">
                                        <BsCheck />
                                    </span>
                                    {condition.text}
                                </li>
                            ))
                        )}
                    </ul>

                    <h4 className="arm-h2 mt-7">
                        {translate('Eligible Products')}
                    </h4>
                    <div className="px-8 py-5 mt-5 rounded-md bg-stone-50 border border-zinc-100">
                        <h5 className="font-public-sans text-sm mb-1">
                            {translate('Categories')}
                        </h5>
                        <p className="text-neutral-400">
                            {isLoading ? (
                                <div className="flex flex-wrap gap-x-2 gap-y-1">
                                    <span className="skeleton w-1/6"></span>
                                    <span className="skeleton w-2/6"></span>
                                    <span className="skeleton w-1/6"></span>
                                    <span className="skeleton w-6/6"></span>
                                    <span className="skeleton w-2/6"></span>
                                    <span className="skeleton w-1/6"></span>
                                </div>
                            ) : (
                                coupon?.categories
                                    .map((category) => category.name)
                                    .join(' | ')
                            )}
                        </p>

                        <h5 className="font-public-sans text-sm mb-1 mt-4">
                            {translate('Products')}
                        </h5>
                        <p className="text-neutral-400">
                            {isLoading ? (
                                <div className="flex flex-wrap gap-x-2 gap-y-1">
                                    <span className="skeleton w-1/6"></span>
                                    <span className="skeleton w-1/6"></span>
                                    <span className="skeleton w-6/6"></span>
                                    <span className="skeleton w-1/6"></span>
                                    <span className="skeleton w-2/6"></span>
                                    <span className="skeleton w-2/6"></span>
                                </div>
                            ) : (
                                coupon?.products
                                    .map((product) => product.name)
                                    .join(' | ')
                            )}
                        </p>
                    </div>

                    <div className="flex justify-end mt-6">
                        <Button
                            as="link"
                            to={`/products?couponCode=${coupon?.code}`}
                            variant="secondary"
                        >
                            {translate('View all products')}
                        </Button>
                    </div>
                </div>
            </div>
        </ModalWrapper>
    );
};

export default CouponDetails;
