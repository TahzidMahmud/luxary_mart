<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Route;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = [
            'id'                    => $this->id,
            'orderGroupId'          => $this->order_group_id,
            'code'                  => $this->order_code,
            'codeToShow'            => getSetting('orderCodePrefix') . $this->order_code,
            'createdDate'           => date('d M, Y', strtotime($this->created_at)),
            'createdTime'           => date('h:i A', strtotime($this->created_at)),
            'updatedDate'           => date('d M, Y', strtotime($this->updated_at)),
            'updatedTime'           => date('h:i A', strtotime($this->updated_at)),
            'subtotalAmount'        => $this->amount,
            'taxAmount'             => $this->tax_amount,
            'shippingChargeAmount'  => $this->shipping_charge_amount,
            'discountAmount'        => $this->discount_amount,
            'couponDiscountAmount'  => $this->coupon_discount_amount,
            'totalAmount'           => $this->total_amount,
            'deliveryStatus'        => $this->delivery_status,
            'deliveryStatusToShow'  => ucfirst(str_replace('_', ' ', $this->delivery_status)),
            'paymentStatus'         => $this->payment_status,
            'advance_payment'=>$this->advance_payment,

        ];

        $additionalData = [];
        if (Route::currentRouteName() == 'orders.show' || Route::currentRouteName() == 'orders.success') {
            $orderGroup = $this->orderGroup;

            $additionalData = [
                'paymentMethod'     => ucfirst(str_replace('_', ' ', $orderGroup->transaction->payment_method)),
                'deliveryAddress'   => [
                    'type'      => $orderGroup->shipping_address_type,
                    'address'   => $orderGroup->shipping_address,
                    'direction' => $orderGroup->direction,
                    'phone'    => $orderGroup->phone,
                    'alternativePhone'    => $orderGroup->alternative_phone,
                ],
                'items'             => OrderItemResource::collection($this->orderItems),
                'shop'              => new ShopResource($this->shop),
                'orderTimeline'     => OrderUpdateResource::collection($this->orderUpdates),
            ];
        }
        if(Route::currentRouteName() == 'admin.allorders'){
            $orderGroup = $this->orderGroup;

            $additionalData['courier_partner']=$orderGroup->courier_partner ?? '';
            $additionalData['courier_devlivery_status']=$orderGroup->delivery_status ?? '';
            $additionalData['tracking_number']=$orderGroup->tracking_number ?? '';
            $additionalData['customerName']= $this->user?->name;
            $additionalData['customerPhone']= $this->user?->phone;
            $additionalData['address']= $orderGroup->shipping_address ?? '';
            $additionalData['orderCount']= $this->orderItems()->count();
            $additionalData['orderPrefix'] = getSetting('orderCodePrefix');
        }

        if (Route::currentRouteName() == routePrefix() . '.dashboard.recentOrders') {
            $additionalData['customerName'] = $this->user->name;
        }

        $data = array_merge($data, $additionalData);

        return $data;
    }
}
