<?php

namespace App\Http\Resources\Pos;

use App\Http\Resources\ProductVariationResource;
use Illuminate\Http\Resources\Json\JsonResource;

class PosCartGroupResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id"                    => (int) $this->id,
            'onHold'                => (int) $this->on_hold,
            'customerId'            => $this->customer_id ? (int) $this->customer_id : null,
            'customer'              => $this->customer,
            'shippingAddressId'     => (int) $this->shipping_address_id,
            'discount'              => (float) $this->discount,
            'shippingCharge'        => (float) $this->shipping,
            'advance'               => (float) $this->advance,
            'paymentMethod'         => $this->payment_method,
            'orderReceivingDate'    => date('Y-m-d', strtotime($this?->order_receiving_date)),
            'orderShipmentDate'     => date('Y-m-d', strtotime($this?->order_shipment_date)),
            'note'                  => $this->note,
            'createdAt'             => date('d M, Y g:i:s A', strtotime($this->created_at)),
            'posCarts'              => PosCartResource::collection($this->posCarts)
        ];
    }
}
