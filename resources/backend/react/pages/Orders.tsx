import axios from 'axios';
import React, { useEffect, useRef, useState } from 'react';
import { currencyFormatter } from '../../../frontend/utils/numberFormatter';
import SelectInput from '../components/inputs/SelectInput';
import CourierInitLoader from '../components/order/delivery/CourierIntentLoader';
import SelectPartner from '../components/order/delivery/SelectPartner';
import SearchForm from '../components/order/SearchForm';
import { route } from '../utils/routeHelpers';
import { togglePopup } from '../../../frontend/store/features/popup/popupSlice';
import { useDispatch } from 'react-redux';
interface Order {
    id: number;
    code: number;
    customerName?: string;
    customerPhone?: string;
    createdDate?: string;
    orderCount?: string;
    totalAmount: number;
    deliveryStatus?: string;
    deliveryStatusToShow?: string;
    paymentStatus: string;
    address?: string;
    orderPrefix?: string;
    advance_payment:number;
}

interface Pagination {
    current_page: number;
    last_page: number;
    total: number;
}

const OrderPage: React.FC = () => {
      const dispatch = useDispatch();
    const [orders, setOrders] = useState<Order[]>([]);
    const [selectedOrderIds, setSelectedOrderIds] = useState<number[]>([]);
    const fromDateRef = useRef<HTMLInputElement>(null);
    const [filters, setFilters] = useState({
        from: '',
        to: '',
        order_status: '',
        payment_status:'',
        serach:''
    });

    const [pagination, setPagination] = useState<Pagination>({
        current_page: 1,
        last_page: 1,
        total: 0,
    });
    const handleStatusUpdate=(status:string,id:string,order_id:number)=>{
        dispatch(
            togglePopup({
                popup: 'confirmation-status-update',
                popupProps: { message:`You want to Update status to ${status}`,title:'Are Your Sure?',order:{status,id,order_id,setOrders}},
            }),
        );
    }
    const fetchOrders = async (page = 1) => {
        try {
            const response = await axios.get('/admin/api/orders', {
                params: { ...filters, page },
            });

            const data = response.data.result.data;
            const paginationData=response.data.result.meta
            setOrders(data);
            setPagination({
                current_page: paginationData.current_page,
                last_page: paginationData.last_page,
                total: paginationData.total,
            });
        } catch (error) {
            console.error('Error fetching orders:', error);
        }
    };

    useEffect(() => {
        fetchOrders();
    }, [filters]);

    const handleChange = (key: string, value: string) => {
        setFilters((prev) => ({ ...prev, [key]: value }));
    };

    const handleSearch = (e) => {
        e.preventDefault();
        const range = fromDateRef.current?.value;
        const from=range?.split('-')[0] ?? '';
        const to=range?.split('-')[1] ?? '';
        setFilters((prev) => ({ ...prev, from,to }));
        fetchOrders(1);

    };

    const handleReset = () => {
        setFilters({ from: '', to: '', order_status: '',payment_status:'',serach:'' });
        fetchOrders(1);
    };

    const handlePageChange = (page: number) => {
        if (page >= 1 && page <= pagination.last_page) {
            fetchOrders(page);
        }
    };

    const toggleSelectAll = () => {
        if (selectedOrderIds.length === orders.length) {
            setSelectedOrderIds([]);
        } else {
            setSelectedOrderIds(orders.map((o) => o.id));
        }
    };

    const toggleSelectOne = (orderId: number) => {
        setSelectedOrderIds((prev) =>
            prev.includes(orderId)
                ? prev.filter((id) => id !== orderId)
                : [...prev, orderId]
        );
    };

    const handleDownload = async () => {
        try {
            const response = await axios.get(
            `/admin/multiple-invoice-download/${selectedOrderIds.join(',')}`,
            {
                responseType: 'blob', // Important for binary data
            }
            );

            // Create a blob from the response
            const blob = new Blob([response.data], { type: 'application/zip' });

            // Create a link element
            const link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = 'invoices.zip'; // You can customize the filename

            // Append to the DOM and trigger click
            document.body.appendChild(link);
            link.click();

            // Clean up
            document.body.removeChild(link);
            window.URL.revokeObjectURL(link.href);
        } catch (error) {
            console.error('Error downloading ZIP file:', error);
        }
    };


    const statusColorMap: Record<string, string> = {
        order_placed: '!bg-green-500',
        processing: '!bg-orange-500',
        confirmed: '!bg-yellow-400',
        cancelled: '!bg-red-500',
        shipped: '!bg-orange-500',
        delivered: '!bg-blue-500',
    };

    return (
        <div className="p-4">
            <CourierInitLoader />
            <h1 className="text-xl font-bold mb-4">Orders Panel</h1>
            <div className="flex items-center justify-start space-x-5">
                {selectedOrderIds.length > 0 && (
                    <div className="mb-2 text-sm text-white">
                        Selected {selectedOrderIds.length} order(s)
                    </div>
                )}
                <div>
                    <SearchForm
                        fromDate={filters.from}
                        toDate={filters.to}
                        orderStatus={filters.order_status}
                        search={filters.serach}
                        paymentStatus={filters.payment_status}
                        onChange={handleChange}
                        onSearch={handleSearch}
                        onReset={handleReset}
                        fromRef={fromDateRef}
                    />
                </div>
                {selectedOrderIds.length>0 &&
                    <div className="flex items-center justify-start mb-3">
                        <button
                          onClick={handleDownload}
                          className="px-4 py-2 bg-yellow-300 text-gray-800 rounded hover:bg-yellow-400"
                        >Download Invoice</button>
                    </div>
                }
            </div>



            <div className="card theme-table w-full">
                <div className="overflow-x-auto">
                    <table className="divide-y product-list-table">
                        <thead>
                            <tr>
                                <th className="px-4 py-3">
                                    <input
                                        type="checkbox"
                                        checked={selectedOrderIds.length === orders.length && orders.length > 0}
                                        onChange={toggleSelectAll}
                                    />
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Shipping Address</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ITEM</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice No</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Value</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Advanced</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order Status</th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Delivery</th>
                                <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Options</th>
                            </tr>
                        </thead>
                        <tbody>
                            {orders.map((order) => (
                                <tr key={order.code}>
                                    <td className="px-4 py-4">
                                        <input
                                            type="checkbox"
                                            checked={selectedOrderIds.includes(order.id)}
                                            onChange={() => toggleSelectOne(order.id)}
                                        />
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <div className="inline-flex items-center gap-4">
                                            <div className="line-clamp-2">
                                                {order.customerName}
                                                <div className="line-clamp-2">
                                                    {order.customerPhone}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 whitespace-pre-wrap">
                                        <div className="line-clamp-2">
                                            {order.address}
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        {order.orderCount}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        {order.createdDate}
                                    </td>
                                    <td className="text-center">
                                        <a href={route('admin.orders.downloadInvoice', { id: order.id })} target="_blank">
                                            {order.orderPrefix} {order.code}
                                            <span className="text-lg text-theme-orange mx-2">
                                                <i className="fa-solid fa-file-arrow-down"></i>
                                            </span>
                                        </a>
                                    </td>

                                    <td className="px-6 py-4 whitespace-nowrap">
                                        {/* <div className="line-clamp-2">
                                            <h6 className="font-bold text-sky-500">{currencyFormatter(order.totalAmount)}</h6>
                                            {order.paymentStatus === 'paid' ? (
                                                <div>Paid Online</div>
                                            ) : (
                                                <div>Cash On Delivery</div>
                                            )}

                                        </div> */}
                                        <div className="inline-flex items-center capitalize gap-1.5">
                                            {order.paymentStatus == 'paid' ?(
                                                 <span
                                                className="text-teal-600 text-2xl">
                                                <i className="fa-solid fa-circle-check"></i>
                                            </span>
                                            ):(
                                                 <span
                                                className="text-neutral-300 dark:text-neutral-800 text-2xl">
                                                <i className="fa-solid fa-circle-check"></i>
                                            </span>
                                            )}

                                            <span>{order.paymentStatus}</span>
                                        </div>
                                    </td>
                                     <td className="px-6 py-4 whitespace-nowrap">
                                        <div className="line-clamp-2">
                                            <h6 className="font-bold text-sky-500">{currencyFormatter(order.totalAmount)}</h6>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <div className="line-clamp-2">
                                            <h6 className="font-bold text-green-500">{currencyFormatter(order.advance_payment)}</h6>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <div className="line-clamp-2">
                                            <h6 className="font-bold text-red-500">{currencyFormatter((order.totalAmount-order.advance_payment))}</h6>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4">
                                        <SelectInput
                                            name="delivery_status"
                                            placeholder=""
                                            value={order.deliveryStatus}
                                            options={[
                                                { id: 'order_placed', name: 'Order Placed' },
                                                { id: 'confirmed', name: 'Confirmed' },
                                                { id: 'processing', name: 'Processing' },
                                                { id: 'cancelled', name: 'Cancelled' },
                                                { id: 'shipped', name: 'Shipped' },
                                                { id: 'delivered', name: 'Delivered' },
                                            ]}
                                            onChange={(e)=>{
                                                handleStatusUpdate(e.name,e.id,order.id);

                                            }}
                                            getOptionLabel={(item) => item.name}
                                            getOptionValue={(item) => item.id}
                                            groupClassName="grow"
                                            statusColorClass={`${statusColorMap[order.deliveryStatus ?? 'order_placed']}`}
                                        />
                                    </td>
                                    <td className="px-6 py-4 w-16">
                                        <SelectPartner order={order} />
                                    </td>
                                    <td className="px-6 py-4 text-right text-sm font-medium">
                                        <a
                                            href={`/admin/orders/${order.code}`}
                                            className="text-secondary text-lg"
                                        >
                                            <i className="fa-solid fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            ))}
                            {orders.length === 0 && (
                                <tr>
                                    <td colSpan={10} className="text-center py-4 text-gray-500">
                                        No orders found.
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>


            {/* Pagination */}
            <div className="flex justify-between items-center mt-4">
                <p className="text-sm text-gray-700">
                    Showing page {pagination.current_page} of {pagination.last_page} ({pagination.total} orders)
                </p>
                <div className="flex space-x-2">
                    <button
                        onClick={() => handlePageChange(pagination.current_page - 1)}
                        disabled={pagination.current_page === 1}
                        className={`px-3 py-1 rounded ${
                            pagination.current_page === 1
                                ? 'bg-gray-200 text-gray-500 cursor-not-allowed'
                                : 'bg-indigo-600 text-white hover:bg-indigo-700'
                        }`}
                    >
                        Previous
                    </button>
                    <button
                        onClick={() => handlePageChange(pagination.current_page + 1)}
                        disabled={pagination.current_page === pagination.last_page}
                        className={`px-3 py-1 rounded ${
                            pagination.current_page === pagination.last_page
                                ? 'bg-gray-200 text-gray-500 cursor-not-allowed'
                                : 'bg-indigo-600 text-white hover:bg-indigo-700'
                        }`}
                    >
                        Next
                    </button>
                </div>
            </div>
        </div>
    );
};

export default OrderPage;
