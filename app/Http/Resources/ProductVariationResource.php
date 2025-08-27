<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariationResource extends JsonResource
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
            'id'                            => $this->id,
            'productId'                     => (int) $this->product_id,
            'name'                          => generateVariationName($this->code),
            'code'                          => $this->code,
            'sku'                           => $this->sku,
            'stocks'                        => ProductVariationStockResource::collection($this->productVariationStocks()->whereIn('warehouse_id', session('WarehouseIds'))->get()),
            'image'                         => uploadedAsset($this->image),
            'basePrice'                     => (float) variationPrice($this->product, $this, false),
            'discountedBasePrice'           => (float) variationDiscountedPrice($this->product, $this, false),
            'basePriceWithTax'              => (float) variationPrice($this->product, $this),
            'discountedBasePriceWithTax'    => (float) variationDiscountedPrice($this->product, $this),
            'tax'                           => (float) variationDiscountedPrice($this->product, $this) - (float) variationDiscountedPrice($this->product, $this, false),
            'dealEnds'                      => $this->__getDealEnds($this)
        ];
    }

    # deal ends
    private function __getDealEnds($productVariation)
    {
        $endsAt             = null;
        $campaignProduct    = $productVariation->campaignProducts()->whereHas('campaign', function ($q) {
            $q->where('start_date', '<=', strtotime(date('d-m-Y H:i:s')))->where('end_date', '>=', strtotime(date('d-m-Y H:i:s')));
        })->first();

        if ($campaignProduct) {
            $campaign = $campaignProduct->campaign;
            $endsAt   = $campaign->end_date;
        }

        return $endsAt;
    }
}
