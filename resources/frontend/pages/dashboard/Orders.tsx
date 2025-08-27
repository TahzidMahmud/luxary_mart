import { FaCheckCircle, FaShoppingCart } from 'react-icons/fa';
import { useSearchParams } from 'react-router-dom';
import Button from '../../components/buttons/Button';
import Pagination from '../../components/pagination/Pagination';
import { DashboardOrderSkeleton } from '../../components/skeletons/from-svg';
import SectionTitle from '../../components/titles/SectionTitle';
import { useGetOrdersQuery } from '../../store/features/api/orderApi';
import { cn } from '../../utils/cn';
import { currencyFormatter } from '../../utils/numberFormatter';
import { translate } from '../../utils/translate';

const orderStatuses = [
    {
        label: 'All Orders',
        value: '',
        colorClass: '',
    },
    {
        label: 'Order Placed',
        value: 'order_placed',
        colorClass: 'bg-theme-secondary-light',
    },
    {
        label: 'Confirmed',
        value: 'confirmed',
        colorClass: 'bg-sky-600',
    },
    {
        label: 'Processing',
        value: 'processing',
        colorClass: 'bg-theme-orange',
    },
    {
        label: 'Shipped',
        value: 'shipped',
        colorClass: 'bg-yellow-600',
    },
    {
        label: 'Delivered',
        value: 'delivered',
        colorClass: 'bg-theme-green',
    },
    {
        label: 'Cancelled',
        value: 'cancelled',
        colorClass: 'bg-theme-alert',
    },
];

const Orders = () => {
    const [searchParams, setSearchParams] = useSearchParams();

    const orderStatus = searchParams.get('order-status') || '';
    const page = searchParams.get('page') || 1;
    const limit = searchParams.get('limit') || 10;

    const { data: ordersRes, isFetching } = useGetOrdersQuery({
        page,
        limit,
        deliveryStatus: orderStatus,
    });

    const orders = ordersRes?.result.data || [];
    const pagination = ordersRes?.result.meta;

    const getOrderStatusBtnClasses = (isActive: boolean) => {
        return cn(
            'bg-zinc-100 text-theme-secondary-light h-9 hover:text-white',
            {
                'bg-theme-primary text-white': isActive,
            },
        );
    };

    const handleOrderStatusChange = (status: string) => {
        setSearchParams({ 'order-status': status });
    };

    return (
        <div
            className="bg-white border border-zinc-100 rounded-md"
            id="my-orders"
        >
            <div className="px-8 pt-10 pb-4">
                <SectionTitle
                    icon={<FaShoppingCart />}
                    title={`My Orders (${pagination?.total || 0})`}
                    className="mb-7"
                />

                <div className="flex overflow-x-auto whitespace-nowrap gap-1.5 pb-3 scrollbar-sm">
                    {orderStatuses.map((status) => (
                        <Button
                            className={getOrderStatusBtnClasses(
                                orderStatus === status.value,
                            )}
                            onClick={() =>
                                handleOrderStatusChange(status.value)
                            }
                            key={status.value}
                        >
                            {translate(status.label)}
                        </Button>
                    ))}
                </div>
            </div>

            <div className="overflow-x-scroll">
                {isFetching ? (
                    <DashboardOrderSkeleton />
                ) : (
                    <table className="order-table min-w-[550px]">
                        <thead className="uppercase">
                            <tr>
                                <th>{translate('Order No')}</th>
                                <th>{translate('Date')}</th>
                                <th>{translate('Price')}</th>
                                <th>{translate('Payment')}</th>
                                <th>{translate('Order Status')}</th>
                                <th className="w-[140px]">
                                    {translate('Details')}
                                </th>
                            </tr>
                        </thead>

                        <tbody className="">
                            {!orders.length ? (
                                <tr>
                                    <td colSpan={6}>
                                        <h3 className="text-center text-gray-500">
                                            {translate('No orders found')}
                                        </h3>
                                    </td>
                                </tr>
                            ) : (
                                orders.map((order) => (
                                    <tr key={order.id}>
                                        <td className="">{order.codeToShow}</td>
                                        <td>
                                            <p className="text-zinc-600">
                                                {order.createdDate}
                                            </p>
                                            <p className="text-muted font-public-sans">
                                                {order.createdTime}
                                            </p>
                                        </td>
                                        <td className="text-[15px] text-theme-secondary-light font-bold">
                                            {currencyFormatter(
                                                order.totalAmount,
                                            )}
                                        </td>
                                        <td className="capitalize">
                                            <div className="flex items-center gap-2">
                                                {order.paymentStatus ===
                                                'paid' ? (
                                                    <span className="text-theme-green text-xl">
                                                        <FaCheckCircle />
                                                    </span>
                                                ) : null}
                                                {order.paymentStatus}
                                            </div>
                                        </td>
                                        <td>
                                            <div className="inline-flex items-center gap-1">
                                                <span
                                                    className={cn(
                                                        'inline-block w-3 h-3 rounded-full bg-theme-green',
                                                        orderStatuses.find(
                                                            (status) =>
                                                                status.value ===
                                                                order.deliveryStatus,
                                                        )!.colorClass,
                                                    )}
                                                ></span>
                                                {order.deliveryStatusToShow}
                                            </div>
                                        </td>
                                        <td>
                                            <Button
                                                as="link"
                                                to={`/orders/${order.code}`}
                                                variant="link"
                                                className="w-full text-theme-secondary-light border border-zinc-100 px-5"
                                            >
                                                {translate('Details')}
                                            </Button>
                                        </td>
                                    </tr>
                                ))
                            )}

                            {(pagination?.last_page || 1) > 1 && (
                                <tr>
                                    <td colSpan={6}>
                                        <Pagination
                                            pagination={pagination}
                                            scrollTo="#my-orders"
                                            className="border-t-0 mt-0 py-0"
                                        />
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                )}
            </div>
        </div>
    );
};

export default Orders;
