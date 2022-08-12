<?php

namespace App\SDK;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use JsonException;

class CloudKassirSDK
{
    private array $headers;
    private const BASE_URL = 'https://api.cloudpayments.ru';

    public function __construct(
        string $cloudKassirApiId,
        string $cloudKassirApiSecret
    ) {
        $this->headers = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode($cloudKassirApiId . ":" . $cloudKassirApiSecret),
        ];
    }

    /**
     * @throws JsonException
     */
    private function sendRequest(string $uri, array $data = []): array
    {
        $request = new Request(
            'POST',
            self::BASE_URL . $uri,
            $this->headers,
            json_encode($data, JSON_THROW_ON_ERROR)
        );

        $client = new Client();
        $response = $client->send($request);

        return json_decode(
            $response->getBody()->getContents(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
    }

    /**
     * @throws JsonException
     */
    public function test(): array
    {
        return $this->sendRequest('/test');
    }

    /**
     * @throws JsonException
     */
    public function generateReceipt(array $data, ?string $requestId = null): array
    {
        if ($requestId) {
            $this->headers['X-Request-ID'] = $requestId;
        }

        return $this->sendRequest('/kkt/receipt', $data);
    }

    /**
     * @throws JsonException
     */
    public function getReceipt(string $receiptId): array
    {
        $data = ['Id' => $receiptId];

        return $this->sendRequest('/kkt/receipt/get', $data);
    }

    /**
     * @throws JsonException
     */
    public function getReceiptStatus(string $receiptId): array
    {
        $data = ['Id' => $receiptId];

        return $this->sendRequest('/kkt/receipt/status/get', $data);
    }
}
