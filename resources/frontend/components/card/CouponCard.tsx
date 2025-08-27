import { FaRegCopy } from 'react-icons/fa';
import { LiaAngleRightSolid } from 'react-icons/lia';
import { useDispatch } from 'react-redux';
import { togglePopup } from '../../store/features/popup/popupSlice';

import { ICouponShort } from '../../types/checkout';
import { currencyFormatter } from '../../utils/numberFormatter';
import { translate } from '../../utils/translate';
import Copy from '../Copy';
import Button from '../buttons/Button';

interface Props {
    coupon: ICouponShort;
}

const CouponCard = ({ coupon }: Props) => {
    const dispatch = useDispatch();

    return (
        <div className="relative overflow-hidden text-white bg-gradient-to-r from-rose-400 to-theme-primary rounded-md py-4 px-4 md:px-12">
            {/* half round, left */}
            <span className="absolute top-1/2 -left-4 md:-left-[calc(52px/2)] -translate-y-1/2 h-8 md:h-[52px] aspect-square rounded-full bg-white"></span>
            {/* half round, right */}
            <span className="absolute top-1/2 -right-4 md:-right-[calc(52px/2)] -translate-y-1/2 h-8 md:h-[52px] aspect-square rounded-full bg-white"></span>

            {/* middle line */}
            <span className="absolute top-1/2 -translate-y-1/2 left-0 right-0 border-t border-white border-dashed"></span>

            <div className="flex justify-between">
                <div>
                    <p className="mb-0.5 capitalize">
                        {translate('Coupon code')}
                    </p>

                    <Copy copyText={coupon.code}>
                        <Button
                            variant="no-color"
                            className="bg-orange-300 hover:bg-orange-400"
                        >
                            <span className="!text-black font-semibold uppercase">
                                {coupon.code}
                            </span>
                            <span className="text-lg">
                                <FaRegCopy />
                            </span>
                        </Button>
                    </Copy>
                </div>

                <div className="text-right">
                    <h6 className="text-xl md:text-[35px] leading-none font-semibold">
                        {coupon.discountType === 'percentage'
                            ? coupon.discount + '%'
                            : currencyFormatter(coupon.discount)}
                    </h6>
                    <p>{translate('discount')}</p>
                </div>
            </div>

            <div className="flex gap-4 mt-8">
                <div>
                    <table className="grow">
                        <tbody>
                            <tr>
                                <td className="capitalize">
                                    {translate('Valid until')}:
                                </td>
                                <td className="pl-3">{coupon.endDate}</td>
                            </tr>
                            <tr>
                                <td className="capitalize">
                                    {translate('Min Order')}:
                                </td>
                                <td className="pl-3">
                                    {currencyFormatter(coupon.minSpend)}
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <button
                        className="mt-3 inline-flex items-center gap-2"
                        onClick={() =>
                            dispatch(
                                togglePopup({
                                    popup: 'coupon-details',
                                    popupProps: { couponCode: coupon.code },
                                }),
                            )
                        }
                    >
                        <span className="text-orange-200 capitalize">
                            {translate('Coupon Details')}
                        </span>
                        <span>
                            <LiaAngleRightSolid />
                        </span>
                    </button>
                </div>

                <div className="self-end">
                    {window.config.generalSettings.appMode ===
                        'multiVendor' && (
                        <Button
                            as="link"
                            to={`/shops/${coupon.shopSlug}`}
                            variant="secondary"
                            className="capitalize"
                        >
                            {translate('Visit Shop')}
                        </Button>
                    )}
                </div>
            </div>
        </div>
    );
};

export default CouponCard;
