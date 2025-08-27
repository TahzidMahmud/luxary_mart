<?php
namespace App\Services\Delivery;

use App\Services\Interfaces\DeliveryPartnerInterface;
use Illuminate\Support\Facades\Http;

class SteadfastService implements DeliveryPartnerInterface
{
    protected $baseUrl;
    protected $apiKey;
    protected $secretKey;

    public function __construct()
    {
        $this->baseUrl = config('services.steadfast.base_url');
        $this->apiKey = config('services.steadfast.api_key');
        $this->secretKey = config('services.steadfast.secret_key');
    }

    protected function headers()
    {
        return [
            'Api-Key' => $this->apiKey,
            'Secret-Key' => $this->secretKey,
            'Content-Type' => 'application/json',
        ];
    }

    public function  createOrder(object $data): array
    {
        return Http::withHeaders($this->headers())
                   ->post("{$this->baseUrl}/create_order",(array) $data)
                   ->json();
    }

    public function bulkCreateOrders(array $orders)
    {
        return Http::withHeaders($this->headers())
                   ->post("{$this->baseUrl}/bulk_order_create", ['orders' => $orders])
                   ->json();
    }

    public function getDeliveryStatus(string $trackingId)
    {
        return Http::withHeaders($this->headers())
                   ->get("{$this->baseUrl}/status_by_trackingcode/{$trackingId}")
                   ->json();
    }

    public function getBalance()
    {
        return Http::withHeaders($this->headers())
                   ->get("{$this->baseUrl}/get_balance")
                   ->json();
    }
}
