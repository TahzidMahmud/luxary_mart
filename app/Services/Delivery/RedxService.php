<?php

namespace App\Services\Delivery;

use App\Services\Interfaces\DeliveryPartnerInterface;
use Illuminate\Support\Facades\Http;

class RedxService implements DeliveryPartnerInterface
{
    protected string $baseUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.redx.base_url'); // Example: https://openapi.redx.com.bd/v1.0.0-beta
        $this->apiKey = config('services.redx.api_key');
    }

    protected function headers(): array
    {
        return [
            'API-ACCESS-TOKEN' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Create a single RedX order
     */
    public function createOrder(object $data): array
    {
        return Http::withHeaders($this->headers())
            ->post("{$this->baseUrl}/parcel", (array) $data)
            ->json();
    }

    /**
     * Create multiple RedX orders
     */
    public function bulkCreateOrders(array $orders): array
    {
        return Http::withHeaders($this->headers())
            ->post("{$this->baseUrl}/bulk-parcel", ['data' => $orders])
            ->json();
    }

    /**
     * Track delivery status by RedX tracking ID
     */
    public function getDeliveryStatus(string $trackingId): array
    {
        return Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/parcel/track/{$trackingId}")
            ->json();
    }

    /**
     * Get current merchant balance from RedX
     */
    public function getBalance(): array
    {
        return Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/merchant/balance")
            ->json();
    }

    /**
     * Get list of RedX delivery areas
     */
    public function getAreas(): array
    {
        return Http::withHeaders($this->headers())
            ->get("{$this->baseUrl}/areas")
            ->json();
    }
}
