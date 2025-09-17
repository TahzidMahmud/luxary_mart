import { LiaTimesSolid } from 'react-icons/lia';
import { closePopup, usePopup } from '../../../store/features/popup/popupSlice';
import { useAppDispatch } from '../../../store/store';
import { STORAGE_KEYS } from '../../../types';
import { ILocallyStoredUserAddress } from '../../../types/checkout';
import { translate } from '../../../utils/translate';
import Button from '../../buttons/Button';
import ModalWrapper from '../ModalWrapper';
import axios from 'axios';
import { useEffect, useState } from 'react';
import toast from 'react-hot-toast';

// import { XMarkIcon, TrashIcon } from "@heroicons/react/24/outline";

export interface Order {
  id: number;
  user_id: number;
  order_group_id: number;
  order_code: number;
  shop_id: number;
  warehouse_id: number;
  amount: number;
  tax_amount: number;
  shipping_charge_amount: number;
  discount_amount: number;
  coupon_discount_amount: number;
  advance_payment: number;
  total_amount: number;
  coupon_id: number | null;
  pickup_or_delivery: "pickup" | "delivery";
  delivery_status: string; // you can narrow union later if you know all statuses
  payment_status: "paid" | "unpaid" | string;
  courier_name: string | null;
  tracking_number: string | null;
  tracking_url: string | null;
  order_receiving_date: string | null;
  order_shipment_date: string | null;
  created_by: number | null;
  created_at: string;
  updated_by: number | null;
  updated_at: string;
  deleted_by: number | null;
  deleted_at: string | null;
  pos_order: number;
  order_items: OrderItem[];
}

export interface OrderItem {
  id: number;
  order_id: number;
  product_variation_id: number;
  qty: number;
  unit_price: number;
  total_tax: number;
  total_discount: number;
  total_price: number;
  reward_points: number;
  is_refunded: number;
  created_by: number | null;
  created_at: string;
  updated_by: number | null;
  updated_at: string;
  deleted_by: number | null;
  deleted_at: string | null;
  product_variation: ProductVariation;
}

export interface ProductVariation {
  id: number;
  product_id: number;
  sku: string;
  image: string;
  code: string;
  price: number;
  discount_value: number;
  discount_type: "flat" | "percent" | string;
  created_by: number | null;
  created_at: string;
  updated_by: number | null;
  updated_at: string;
  deleted_by: number | null;
  deleted_at: string | null;
  image_url: string;
  product: Product;
}

export interface Product {
  id: number;
  name: string;
  product_translations: ProductTranslation[];
}

export interface ProductTranslation {
  id: number;
  lang_key: string;
  product_id: number;
  name: string;
  short_description: string | null;
  description: string | null;
  created_by: number | null;
  created_at: string;
  updated_by: number | null;
  updated_at: string;
  deleted_by: number | null;
  deleted_at: string | null;
}


interface EditableItem {
  id: number;
  name: string;
  image: string;
  unitPrice: number;
  qty: number;
  tax: number;
  discount: number;
  total:number;
  removed:boolean;
}
const OrderEditPopup = () => {
    const dispatch = useAppDispatch();
    const { popup, popupProps } = usePopup();
    const isActive = popup === 'order-update';
    const {order} = popupProps;
    const [selectedOrder, setSelectedOrder]=useState<Order>();
    const [rows, setRows] = useState<EditableItem[]>([]);
    const [editableTotal, setEditableTotal]=useState(0);
    console.log('rows', rows);
    useEffect(() => {
        if (selectedOrder && selectedOrder.order_items) {
            setEditableTotal(0);
            setRows(
                selectedOrder.order_items.map((item: any) => {
                    setEditableTotal((prev:number)=>{
                        prev = prev + (item.unit_price*item.qty);
                        return prev;
                    });

                    return {
                    id: item.id,
                    name: item.product_variation.product.name,
                    image: item.product_variation.image_url,
                    unitPrice: item.unit_price,
                    qty: item.qty,
                    tax: item.total_tax,
                    discount: item.total_discount,
                    total:(item.qty * item.unit_price),
                    removed:false
                }})
            );
        }
    }, [selectedOrder]);

    useEffect(()=>{
        if(order && order.order_id){
            fetchOrderDetails();
        }
    },[order]);
    const handleCancel = () => {
        dispatch(closePopup());
    };
    const handleConfirm = async () => {
        const config = {
            method: 'post',
            url: `/admin/api/order/update`,
            headers: {
                'Authorization': `Bearer ${localStorage.getItem(STORAGE_KEYS.AUTH_KEY)}`,
                'Content-Type': 'application/json',
                'Accept-Language': localStorage.getItem('i18nextLng')!
            },
            data:{
                orderItems:rows
            }
        };
        const response = await axios(config);
        if(response.data.status == 200){
            toast.success(response.data.message);
            dispatch(closePopup());
        }else{
            toast.error('something went wrong')
        }
    };
    const fetchOrderDetails = async ()=>{
        const config = {
            method: 'get',
            url: `/admin/api/orders/${order.order_id}`,
            headers: {
                'Authorization': `Bearer ${localStorage.getItem(STORAGE_KEYS.AUTH_KEY)}`,
                'Content-Type': 'application/json',
                'Accept-Language': localStorage.getItem('i18nextLng')!
            },
        };

        const response = await axios(config);
        if(response.data.status == 200){
            setSelectedOrder(response.data.result.order);
        }
    }
    const handleRemove = (id:number)=>{
        setRows((prev)=>{
            return prev.map((item)=>{
                if(id == item.id){
                    item.removed=true;
                    setEditableTotal((prev)=>(prev-item.total));
                }
                return item;
            })
        })
    }
    const handleQtyChange= (value:number,id:number)=>{
        setEditableTotal(0);
        setRows((prev)=>{
            return prev.map((item)=>{
                if(item.id == id){
                    item.qty= value;
                    item.total = item.unitPrice * value;
                }
                setEditableTotal((prev)=>(prev + item.total));
                return item;
            })
        })
    }
    return (
        <ModalWrapper
            isActive={isActive}
            className="rounded-card"
            size={'xl'}
        >
            <div className="flex justify-between px-5 py-4 border-b border-gray-200">
                <h4 className="ek-h3">{translate('Confirmation')}</h4>

                <button
                    className="text-lg"
                    onClick={() => dispatch(closePopup())}
                >
                    <LiaTimesSolid />
                </button>
            </div>
             <div className="p-4 overflow-x-auto">
                <table className="min-w-full text-sm text-left border border-gray-700">
                  <thead className="bg-gray-800 text-gray-300">
                    <tr>
                      <th className="px-3 py-2">Product</th>
                      <th className="px-3 py-2">Unit Price</th>
                      <th className="px-3 py-2">Qty</th>
                      <th className="px-3 py-2">Sub Total</th>
                      <th className="px-3 py-2">Action</th>
                    </tr>
                  </thead>
                  <tbody>

                    {rows.map((row) => {
                        if(row.removed){
                            return;
                        }
                        return (
                            <tr
                                key={row.id}
                                className="border-t border-gray-700 hover:bg-gray-800"
                            >
                                <td className="px-3 py-2"><img src={row.image} className="h-20 w-20"></img>{row.name}</td>
                                <td className="px-3 py-2">{row.unitPrice} ৳</td>
                                <td className="px-3 py-2">
                                <input
                                    type="number"
                                    min={1}
                                    value={row.qty}
                                    onChange={(e)=>{
                                        if(Number(e.target.value) !==1){
                                            handleQtyChange(Number(e.target.value),row.id);
                                        }
                                    }}
                                    className="w-16 rounded-md bg-gray-800 border border-gray-700 px-2 py-1 text-center text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                />
                                </td>
                                <td className="px-3 py-2"> {row.total} ৳</td>
                                <td className="px-3 py-2">
                                <button
                                    onClick={() => handleRemove(row.id)}
                                    className="text-red-500 hover:text-red-400"
                                >
                                     <i className="fa-solid fa-trash"></i>
                                </button>
                                </td>
                            </tr>
                        )})
                    }
                    <tr className="bg-gray-800 font-semibold">
                      <td colSpan={5} className="px-3 py-2 text-right">
                        Total:
                      </td>
                      <td colSpan={2} className="px-3 py-2">
                       {editableTotal} ৳
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

            <div className="px-5 pt-3 pb-6">

                <div className="flex gap-3 mt-6">

                    <Button variant="secondary" size="lg" onClick={handleCancel}>
                        {translate('Cancel')}
                    </Button>
                    <Button variant="primary" size="lg" onClick={handleConfirm}>{translate('Confirm')}</Button>
                </div>
            </div>
        </ModalWrapper>
    );
};

export default OrderEditPopup;
