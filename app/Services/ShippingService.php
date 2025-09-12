<?php

namespace App\Services;

use App\Models\City;
use App\Models\Coupon;
use App\Models\Shop;
use App\Models\ZoneShippingCharge;

class ShippingService
{
    /**
     * Calculate total shipping charge for cart items grouped by shop and shipping class.
     *
     * @param int $cityId
     * @param array $cartItems
     * @param array $couponCodes
     * @return float
     */
    public static function calculate(int $cityId, array $cartItems, array $couponCodes = []): float
    {
        $zoneId = City::findOrFail($cityId)->zone_id;
        $coupons = collect($couponCodes)->isNotEmpty()
            ? Coupon::whereIn('code', $couponCodes)->get()
            : collect();

        $totalShippingCharge = 0;

        foreach ($cartItems as $cart) {
            $shopId = $cart['shop_id'];
            $shippingClassId = $cart['shipping_class_id'] ?? null;

            // Skip if free shipping coupon is applied for the shop
            $isFreeShipping = $coupons->first(fn($c) => $c->shop_id == $shopId && $c->is_free_shipping);
            if ($isFreeShipping) continue;

            // Zone-specific shipping charge
            $zoneCharge = ZoneShippingCharge::where([
                'shop_id' => $shopId,
                'zone_id' => $zoneId,
                'shipping_class_id' => $shippingClassId,
            ])->value('charge');

            if (!is_null($zoneCharge)) {
                $totalShippingCharge += $zoneCharge;
            } else {
                // Fallback to default charge from shop
                $defaultCharge = Shop::where('id', $shopId)->value('default_shipping_charge');
                $totalShippingCharge += $defaultCharge ?? 0;
            }
        }

        return $totalShippingCharge;
    }
}
