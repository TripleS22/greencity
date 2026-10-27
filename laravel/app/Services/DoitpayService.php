<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DoitpayService
{
    protected string $clientId;
    protected string $secretKey;
    protected string $merchantRef;   // Merchant Reference (for X-PARTNER-ID header)
    protected string $merchantPan;   // Merchant PAN / MPAN (for merchantId in body)
    protected string $subMerchantId;
    protected string $nmid;
    protected string $channelId;
    protected string $baseUrl;
    protected string $privateKeyPath;

    public function __construct()
    {
        $this->clientId       = config('doitpay.client_id');
        $this->secretKey      = config('doitpay.secret_key');
        $this->merchantRef    = config('doitpay.merchant_id');      // Merchant Reference
        $this->merchantPan    = config('doitpay.merchant_pan');     // MPAN for body merchantId
        $this->subMerchantId  = config('doitpay.sub_merchant_id');
        $this->nmid           = config('doitpay.nmid');
        $this->channelId      = config('doitpay.channel_id');
        $this->baseUrl        = config('doitpay.base_url');

        // Use env path if set, otherwise default to storage/doitpay_private_key.pem
        $configPath = config('doitpay.private_key_path');
        $this->privateKeyPath = (!empty($configPath) && file_exists($configPath))
            ? $configPath
            : storage_path('doitpay_private_key.pem');
    }

    /**
     * Get current timestamp in ISO 8601 format with +07:00 timezone
     */
    protected function getTimestamp(int $offsetSeconds = 0): string
    {
        $dt = now()->setTimezone('+07:00');
        if ($offsetSeconds) {
            $dt->addSeconds($offsetSeconds);
        }
        return $dt->format('Y-m-d\TH:i:sP');
    }

    /**
     * Load the RSA private key from file
     */
    protected function getPrivateKey()
    {
        $keyContent = file_get_contents($this->privateKeyPath);
        $privateKey = openssl_pkey_get_private($keyContent);

        if ($privateKey === false) {
            throw new \RuntimeException('Failed to load RSA private key: ' . openssl_error_string());
        }

        return $privateKey;
    }

    /**
     * Create asymmetric signature (RSA SHA-256) for access token request
     */
    protected function createAsymmetricSignature(string $stringToSign): string
    {
        $privateKey = $this->getPrivateKey();
        
        $signature = '';
        $success = openssl_sign($stringToSign, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        
        if (!$success) {
            throw new \RuntimeException('Failed to create asymmetric signature: ' . openssl_error_string());
        }

        return base64_encode($signature);
    }

    /**
     * Create symmetric signature (HMAC SHA-512) for API requests
     */
    protected function createSymmetricSignature(
        string $httpMethod,
        string $endpoint,
        string $accessToken,
        string $requestBody,
        string $timestamp
    ): string {
        // Minify the JSON body
        if (!empty($requestBody)) {
            $decoded = json_decode($requestBody, true);
            $minified = json_encode($decoded, JSON_UNESCAPED_SLASHES);
            $sha256Hash = strtolower(hash('sha256', $minified));
        } else {
            $sha256Hash = strtolower(hash('sha256', ''));
        }

        $stringToSign = "{$httpMethod}:{$endpoint}:{$accessToken}:{$sha256Hash}:{$timestamp}";
        $hmacHash = hash_hmac('sha512', $stringToSign, $this->clientId, true);

        return base64_encode($hmacHash);
    }

    /**
     * Get access token from Doitpay API using B2B auth
     */
    public function getAccessToken(): ?string
    {
        $url = "{$this->baseUrl}/auth/v1.0/access-token/b2b";
        $timestamp = $this->getTimestamp();
        $stringToSign = "{$this->clientId}|{$timestamp}";

        try {
            $signature = $this->createAsymmetricSignature($stringToSign);
        } catch (\Exception $e) {
            Log::error('Doitpay: Failed to create asymmetric signature', ['error' => $e->getMessage()]);
            return null;
        }

        $headers = [
            'X-TIMESTAMP'  => $timestamp,
            'X-CLIENT-KEY' => $this->clientId,
            'X-SIGNATURE'  => $signature,
            'Content-Type' => 'application/json',
        ];

        try {
            $response = Http::withHeaders($headers)

                ->timeout(20)->post($url, ['grantType' => 'client_credentials']);

            $data = $response->json();

            if (isset($data['accessToken'])) {
                Log::info('Doitpay: Access token obtained successfully');
                return $data['accessToken'];
            }

            Log::error('Doitpay: Failed to get access token', ['response' => $data]);
            return null;

        } catch (\Exception $e) {
            Log::error('Doitpay: Access token request failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Generate QRIS QR Code via Doitpay SNAP API
     *
     * @param string $partnerReferenceNo  Unique reference number for this transaction
     * @param float  $amount              Amount in IDR
     * @param int    $validityMinutes     QR validity period in minutes
     * @return array{success: bool, data: array|null, error: string|null}
     */
    public function generateQris(string $partnerReferenceNo, float $amount, int $validityMinutes = 30): array
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            return [
                'success' => false,
                'data'    => null,
                'error'   => 'Gagal mendapatkan access token dari Doitpay',
            ];
        }

        $endpoint = '/qris/v1.0/qr/qr-mpm-generate';
        $url = "{$this->baseUrl}{$endpoint}";
        $timestamp = $this->getTimestamp();
        $validityPeriod = $this->getTimestamp($validityMinutes * 60);

        $payload = [
            'partnerReferenceNo' => $partnerReferenceNo,
            'amount' => [
                'value'    => number_format($amount, 2, '.', ''),
                'currency' => 'IDR',
            ],
            'merchantId'     => $this->merchantPan,     // MPAN (Merchant PAN), NOT Merchant Reference
            'submerchantId'  => $this->subMerchantId,
            'storeId'        => $this->nmid,             // NMID
            'validityPeriod' => $validityPeriod,
            'additionalInfo' => [
                'deviceId' => '12345679237',
                'channel'  => 'mobilephone',
            ],
        ];

        $requestBody = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $externalId = Str::limit($partnerReferenceNo, 36, '');

        try {
            $signature = $this->createSymmetricSignature('POST', $endpoint, $accessToken, $requestBody, $timestamp);
        } catch (\Exception $e) {
            Log::error('Doitpay: Failed to create symmetric signature', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'data'    => null,
                'error'   => 'Gagal membuat signature: ' . $e->getMessage(),
            ];
        }

        $headers = [
            'Authorization' => "Bearer {$accessToken}",
            'X-TIMESTAMP'   => $timestamp,
            'X-SIGNATURE'   => $signature,
            'X-PARTNER-ID'  => $this->merchantRef,
            'X-EXTERNAL-ID' => $externalId,
            'CHANNEL-ID'    => $this->channelId,
            'Content-Type'  => 'application/json',
        ];

        $maxAttempts = 2;
        $lastError   = null;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                Log::info("Doitpay: QRIS generate attempt {$attempt}/{$maxAttempts}", [
                    'order_id'    => $partnerReferenceNo,
                    'amount'      => $amount,
                    'url'         => $url,
                    'merchantId'  => $this->merchantPan,
                    'subMerchantId' => $this->subMerchantId,
                    'storeId_nmid'  => $this->nmid,
                    'channelId'   => $this->channelId,
                    'payload'     => $payload,
                    'headers'     => array_merge($headers, ['Authorization' => 'Bearer ***REDACTED***']),
                ]);

                $response = Http::withHeaders($headers)
                    ->timeout(45)
                    ->withBody($requestBody, 'application/json')
                    ->post($url);

                $data = $response->json();

                Log::info('Doitpay: QRIS Generate response', [
                    'attempt'       => $attempt,
                    'http_status'   => $response->status(),
                    'code'          => $data['responseCode'] ?? 'N/A',
                    'message'       => $data['responseMessage'] ?? 'N/A',
                    'full_response' => $data,
                ]);

                if (isset($data['responseCode']) && $data['responseCode'] === '2004700') {
                    return [
                        'success' => true,
                        'data'    => $data,
                        'error'   => null,
                    ];
                }

                // Non-timeout error — don't retry
                return [
                    'success' => false,
                    'data'    => $data,
                    'error'   => $data['responseMessage'] ?? 'Gagal generate QRIS',
                ];

            } catch (\Illuminate\Http\Client\ConnectionException $e) {
                // Timeout or connection error — retry
                $lastError = $e->getMessage();
                Log::warning("Doitpay: QRIS timeout attempt {$attempt}/{$maxAttempts}", [
                    'order_id' => $partnerReferenceNo,
                    'error'    => $lastError,
                ]);
                if ($attempt < $maxAttempts) {
                    sleep(2); // brief pause before retry
                }
            } catch (\Exception $e) {
                Log::error('Doitpay: QRIS generate request failed', ['error' => $e->getMessage()]);
                return [
                    'success' => false,
                    'data'    => null,
                    'error'   => 'Request gagal: ' . $e->getMessage(),
                ];
            }
        }

        // All attempts exhausted (timeout)
        return [
            'success' => false,
            'data'    => null,
            'error'   => 'Server Doitpay tidak merespons setelah ' . $maxAttempts . ' percobaan. Silakan coba lagi nanti.',
        ];
    }

    /**
     * Query payment status from Doitpay
     *
     * @param string $originalPartnerReferenceNo
     * @param string $originalReferenceNo
     * @return array{success: bool, data: array|null, error: string|null}
     */
    public function queryPayment(string $originalPartnerReferenceNo, string $originalReferenceNo): array
    {
        $accessToken = $this->getAccessToken();

        if (!$accessToken) {
            return [
                'success' => false,
                'data'    => null,
                'error'   => 'Gagal mendapatkan access token dari Doitpay',
            ];
        }

        $endpoint = '/qris/v1.0/qr/qr-mpm-query';
        $url = "{$this->baseUrl}{$endpoint}";
        $timestamp = $this->getTimestamp();

        $payload = [
            'originalPartnerReferenceNo' => $originalPartnerReferenceNo,
            'originalReferenceNo'        => $originalReferenceNo,
            'merchantId'                 => $this->merchantPan,     // MPAN, not Merchant Reference
            'serviceCode'                => '51',
        ];

        $requestBody = json_encode($payload, JSON_UNESCAPED_SLASHES);

        try {
            $signature = $this->createSymmetricSignature('POST', $endpoint, $accessToken, $requestBody, $timestamp);
        } catch (\Exception $e) {
            return [
                'success' => false,
                'data'    => null,
                'error'   => 'Gagal membuat signature',
            ];
        }

        $headers = [
            'Authorization' => "Bearer {$accessToken}",
            'X-TIMESTAMP'   => $timestamp,
            'X-SIGNATURE'   => $signature,
            'X-PARTNER-ID'  => $this->merchantRef,
            'X-EXTERNAL-ID' => Str::uuid()->toString(),
            'CHANNEL-ID'    => $this->channelId,
            'Content-Type'  => 'application/json',
        ];

        try {
            $response = Http::withHeaders($headers)
                ->timeout(20)
                ->withBody($requestBody, 'application/json')
                ->post($url);

            $data = $response->json();

            Log::info('Doitpay: Query Payment response', [
                'status' => $data['responseCode'] ?? 'N/A',
                'txStatus' => $data['latestTransactionStatus'] ?? 'N/A',
            ]);

            return [
                'success' => ($data['responseCode'] ?? '') === '2005100',
                'data'    => $data,
                'error'   => null,
            ];

        } catch (\Exception $e) {
            Log::error('Doitpay: Query payment failed', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'data'    => null,
                'error'   => 'Request gagal: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verify the symmetric signature from Doitpay callback notification
     * 
     * @param string $httpMethod      HTTP method (POST)
     * @param string $endpoint        Callback endpoint path
     * @param string $requestBody     Raw request body
     * @param string $timestamp       X-TIMESTAMP header
     * @param string $signature       X-SIGNATURE header
     * @return bool
     */
    public function verifyCallbackSignature(
        string $httpMethod,
        string $endpoint,
        string $requestBody,
        string $timestamp,
        string $signature
    ): bool {
        // For callback, Doitpay uses client_id as HMAC key (same as transaction signing)
        if (!empty($requestBody)) {
            $decoded = json_decode($requestBody, true);
            $minified = json_encode($decoded, JSON_UNESCAPED_SLASHES);
            $sha256Hash = strtolower(hash('sha256', $minified));
        } else {
            $sha256Hash = strtolower(hash('sha256', ''));
        }

        // The accessToken in callback context â€” check X-SIGNATURE directly
        // Doitpay callback uses a different signing format sometimes
        // We'll verify by regenerating the expected signature
        $stringToSign = "{$httpMethod}:{$endpoint}::{$sha256Hash}:{$timestamp}";
        $expectedHmac = hash_hmac('sha512', $stringToSign, $this->clientId, true);
        $expectedSignature = base64_encode($expectedHmac);

        return hash_equals($expectedSignature, $signature);
    }
}
