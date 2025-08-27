<?php

namespace App\Http\Resources\Campaign;

use Illuminate\Http\Resources\Json\JsonResource;

class CampaignProductResource extends JsonResource
{
    public function toArray($request)
    {
        $data = [
            'id'                        => (int) $this->id,
            'name'                      => $this->collectTranslation('name'),
            'thumbnailImage'            => uploadedAsset($this->thumbnail_image),
            'basePrice'                 => (float) productBasePrice($this, false),
            'hasVariation'              => (int) $this->has_variation,
            'variations'                => CampaignProductVariationResource::collection($this->variations()->withoutTrashed()->get())
        ];
        return $data;
    }
}
