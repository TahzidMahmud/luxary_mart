<?php

namespace App\Http\Controllers\Backend\Api\DeliveryPartner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Delivery\DeliveryPartnerFactory;
use App\Data\Orders\PathaoOrderData;
use App\Data\Orders\SteadfastOrderData;
use App\Data\Orders\RedxOrderData;
use App\Models\OrderGroup;
use App\Services\Delivery\PathaoCourierService;
use App\Services\Delivery\RedxService;

class CourierOrderController extends Controller
{
    public function store(Request $request)
    {
        $partner = strtolower($request->partner); // 'pathao', 'steadfast', 'redx'

        if (!in_array($partner, ['pathao', 'steadfast', 'redx'])) {
            return response()->json([
                'success' => false,
                'message' => $partner . ' is not supported'
            ]);
        }

        try {
            $manager = DeliveryPartnerFactory::make($partner);
            $order_group = OrderGroup::where('code', $request->code)->first();

            if (is_null($order_group)) {
                return response()->json([
                    'success' => false,
                    'status' => 404,
                    'message' => translate('Order not found with this code'),
                ], 200);
            }

            $dto = $this->resolveDto($partner, $request, $order_group);
            $response = $manager->createOrder($dto); // Cast DTO to array
            // ✅ Extract tracking number from response
            $trackingNumber = extractTrackingNumber($partner, $response);
            $deliveryStatus = extractDeliveryStatus($partner, $response);

            // ✅ Save tracking data to order_group
            $order_group->update([
                'courier_partner'  => $partner,
                'tracking_number'  => $trackingNumber,
                'delivery_status'  => $deliveryStatus,
                'courier_response' => $response,
            ]);
            return response()->json([
                'success' => true,
                'status' => 'success',
                'partner' => $partner,
                'data' => $response,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'partner' => $partner,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    protected function resolveDto(string $partner, Request $request, OrderGroup $order_group): object
    {
        $transaction = $order_group->transaction;
        return match ($partner) {
            'pathao' => new PathaoOrderData(
                store_id: (int) env('PATHAO_STORE_ID'),
                merchant_order_id: $order_group->code,
                recipient_name: $order_group->name,
                recipient_phone: $order_group->phone,
                recipient_address: $order_group->shipping_address,
                recipient_city: (int) $request->input('recipient_city'),
                recipient_zone: (int) $request->input('recipient_zone'),
                recipient_area: (int) $request->input('recipient_area'),
                delivery_type: (int) ($request->input('delivery_type') ?? 48),
                item_type: (int) ($request->input('item_type') ?? 2),
                item_quantity: (int) $order_group->getTotalQuantity(),
                item_weight: (float) $request->input('item_weight'),
                item_description: $request->input('item_description') ?? '',
                amount_to_collect: $transaction->status === 'paid' ? 0 : $transaction->total_amount,
                special_instruction: $request->input('instruction') ?? ''
            ),

            'steadfast' => new SteadfastOrderData(
                invoice: $order_group->code,
                recipient_name: $order_group->name,
                recipient_phone: $order_group->phone,
                recipient_email: $order_group->email,
                recipient_address: $order_group->shipping_address,
                cod_amount: $transaction->status === 'paid' ? 0 : $transaction->total_amount,
                delivery_type: (int) ($request->input('delivery_type') ?? 1),
                total_lot: (int) $order_group->getTotalQuantity(),
                note: $request->input('instruction') ?? ''
            ),

            'redx' => new RedxOrderData(
                customer_name: $order_group->name,
                customer_phone: $order_group->phone,
                customer_address: $order_group->shipping_address,
                cash_collection_amount: $transaction->status === 'paid' ? 0 : $transaction->total_amount,
                merchant_invoice_id: (string) $order_group->code,
                parcel_weight: (float) $request->input('weight'),
                instruction: $request->input('instruction') ?? '',
                delivery_area_id: $request->input('delivery_area_id'),
                delivery_area: $request->input('delivery_area'),
                value: (string) $transaction->total_amount,
                type: $request->input('type') ?? 'delivery'
            ),

            default => throw new \InvalidArgumentException("Unsupported partner: {$partner}"),
        };
    }

    public function getCities()
    {
        try {
            $pathaoService = new PathaoCourierService();
            $cities = $pathaoService->getCities();

            return response()->json(['status' => 'success', 'data' => $cities]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function getZones(int $cityId)
    {
        try {
            $pathaoService = new PathaoCourierService();
            $zones = $pathaoService->getZones($cityId);

            return response()->json(['status' => 'success', 'data' => $zones]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function getAreas(int $zoneId)
    {
        try {
            $pathaoService = new PathaoCourierService();
            $areas = $pathaoService->getAreas($zoneId);

            return response()->json(['status' => 'success', 'data' => $areas]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 400);
        }
    }

    public function getRedxAreas(RedxService $redxService)
    {
        try {
            $areas = $redxService->getAreas();

            return response()->json(['status' => 'success', 'data' => $areas]);
        } catch (\Throwable $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function getDeliveryStatus(Request $request)
    {
        $partner = strtolower($request->partner);
        $trackingId = $request->tracking_id;

        if (!in_array($partner, ['pathao', 'steadfast', 'redx'])) {
            return response()->json([
                'success' => false,
                'message' => "{$partner} is not supported"
            ]);
        }

        if (!$trackingId) {
            return response()->json([
                'success' => false,
                'message' => 'Tracking ID is required'
            ], 422);
        }

        try {
            $manager = DeliveryPartnerFactory::make($partner);
            $response = $manager->getDeliveryStatus($trackingId);

            return response()->json([
                'success' => true,
                'partner' => $partner,
                'data' => $response,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'partner' => $partner,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}
