<?php

namespace App\Services\Delivery;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

use App\Services\Interfaces\DeliveryPartnerInterface;

class PathaoCourierService implements DeliveryPartnerInterface
{
    protected $clientId;
    protected $clientSecret;
    protected $username;
    protected $password;
    protected $baseUrl;

    public function __construct()
    {
        $this->clientId = config('services.pathao.client_id');
        $this->clientSecret = config('services.pathao.client_secret');
        $this->username = config('services.pathao.username');
        $this->password = config('services.pathao.password');
        $this->baseUrl = config('services.pathao.base_url', 'https://courier-api-sandbox.pathao.com');
    }

    protected function getToken(): string
    {
        return Cache::remember('pathao_access_token', 431000, function () {
            $response = Http::post("{$this->baseUrl}/aladdin/api/v1/issue-token", [
                'client_id'     => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type'    => 'password',
                'username'      => $this->username,
                'password'      => $this->password,
            ]);

            if ($response->failed()) {
                throw new \Exception('Failed to retrieve access token from Pathao');
            }

            $data = $response->json();
            Cache::put('pathao_refresh_token', $data['refresh_token'], 432000);
            return $data['access_token'];
        });
    }

    protected function refreshToken(): string
    {
        $refreshToken = Cache::get('pathao_refresh_token');

        if (!$refreshToken) {
            return $this->getToken();
        }

        $response = Http::post("{$this->baseUrl}/aladdin/api/v1/issue-token", [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type'    => 'refresh_token',
            'refresh_token' => $refreshToken,
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to refresh token.');
        }

        $data = $response->json();
        Cache::put('pathao_access_token', $data['access_token'], 431000);
        Cache::put('pathao_refresh_token', $data['refresh_token'], 432000);
        return $data['access_token'];
    }

    /**
     * Reusable authenticated HTTP client.
     */
    protected function withAuth(): \Illuminate\Http\Client\PendingRequest
    {
        try {
            return Http::withToken($this->getToken());
        } catch (\Throwable $e) {
            return Http::withToken($this->refreshToken());
        }
    }

    /**
     * Create a Pathao order.
     */
    public function  createOrder(object $data): array
    {
        $response = $this->withAuth()
            ->post("{$this->baseUrl}/aladdin/api/v1/orders", (array) $data);

        if ($response->status() === 401) {
            $response = $this->withAuth()
                ->post("{$this->baseUrl}/aladdin/api/v1/orders", (array) $data);
        }

        if ($response->failed()) {
            throw new \Exception('Failed to create order in Pathao: ' . $response->body());
        }

        return $response->json();

    }

    /**
     * Get list of cities.
     */
    public function getCities(): array
    {
        $response = $this->withAuth()->get("{$this->baseUrl}/aladdin/api/v1/city-list");

        if ($response->failed()) {
            throw new \Exception("Failed to fetch Pathao cities: " . $response->body());
        }

        return $response->json();
    }

    /**
     * Get zones within a given city.
     */
    public function getZones(int $cityId): array
    {
        $response = $this->withAuth()->get("{$this->baseUrl}/aladdin/api/v1/cities/{$cityId}/zone-list");

        if ($response->failed()) {
            throw new \Exception("Failed to fetch Pathao zones: " . $response->body());
        }

        return $response->json();
    }

    /**
     * Get areas within a given zone.
     */
    public function getAreas(int $zoneId): array
    {
        $response = $this->withAuth()->get("{$this->baseUrl}/aladdin/api/v1/zones/{$zoneId}/area-list");

        if ($response->failed()) {
            throw new \Exception("Failed to fetch Pathao areas: " . $response->body());
        }

        return $response->json();
    }
    public function getDeliveryStatus(string $trackingId): array
    {
        return $this->withAuth()
            ->get("{$this->baseUrl}/aladdin/api/v1/orders/{$trackingId}/info")
            ->json();
    }
}
