<?php

namespace App\Services;

use App\Models\Zone;
use App\Models\ZoneShippingCharge;

class DeliveryChargeService
{
    # get data
    public static function index($request)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        $searchKey = null;
        $limit     = $request->limit ?? perPage();
        $zones     = Zone::latest();

        if ($request->search != null) {
            $zones     = $zones->where('name', 'like', '%' . $request->search . '%');
            $searchKey  = $request->search;
        }
        $zones = $zones->paginate($limit);

        $data = [
            'status'    => 200,
            'message'   => '',
            'result'    => [
                'zones'         => $zones,
                'searchKey'     => $searchKey,
            ],
        ];

        return $data;
    }

    # add new data
    public static function store($request)
    {
        $data = [
            'status'        => 200,
            'message'       => '',
            'result'        => [],
        ];

        try {

            foreach ($request->zone_ids as $key => $zoneId) {
                $zoneShippingCharge = ZoneShippingCharge::where('shop_id', shopId())->where('zone_id', $zoneId)->first();
                if ($zoneShippingCharge) {
                    $zoneShippingCharge->update(['charge' => $request->delivery_charges[$key]]);
                } else {
                    $zoneShippingCharge = new ZoneShippingCharge;
                    $zoneShippingCharge->shop_id = shopId();
                    $zoneShippingCharge->zone_id = $zoneId;
                    $zoneShippingCharge->charge = $request->delivery_charges[$key];
                    $zoneShippingCharge->save();
                }
            }

            $data = [
                'status'    => 200,
                'message'   => translate('Shipping charges have been updated successfully'),
                'result'    => [],
            ];
            return $data;
        } catch (\Throwable $th) {
            $data = [
                'status'    => 403,
                'message'   => translate('Something went wrong'),
                'result'    => [],
            ];
            return $data;
        }
    }
}
