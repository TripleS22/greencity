<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Services\DoitpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DoitpayController extends Controller
{
    protected DoitpayService $doitpay;

    /** QRIS validity window — must match what Doitpay is told in generateQris() */
    private const QR_VALIDITY_MINUTES = 30;

    public function __construct(DoitpayService $doitpay)
    {
        $this->doitpay = $doitpay;
    }

    /**
     * Generate QRIS code for a pembayaran record
     * Called via AJAX from the bayar page
     */
    public function generateQris(Request $request)
    {
        try {
            // Support both single (pembayaran_id) and multi (payment_ids[]) modes
            if ($request->filled('payment_ids') && is_array($request->payment_ids)) {
                // ── Multi-payment mode ──────────────────────────────────────
                $request->validate([
                    'payment_ids'   => 'required|array|min:1',
                    'payment_ids.*' => 'exists:pembayaran,id',
                ]);

                /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Pembayaran[] $payments */
                $payments = Pembayaran::whereIn('id', $request->payment_ids)
                    ->where('status', '!=', 'lunas')
                    ->get();

                if ($payments->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Semua pembayaran sudah lunas.',
                    ], 400);
                }

                $totalAmount = $payments->sum('jumlah');

                // ── CACHE CHECK: If primary payment already has valid QR, return it ──
                $primary = $payments->first();
                $isValidCache = false;

                // Stale cache (past QR validity) must be regenerated, not re-served
                $cacheStillValid = $primary->qr_expired_at && $primary->qr_expired_at->isFuture();

                if ($cacheStillValid && $primary->qr_content && $primary->doitpay_reference_no && $primary->order_id) {
                    // Check if all requested payments share the same order_id
                    $isSameGroup = true;
                    foreach ($payments as $p) {
                        if ($p->order_id !== $primary->order_id) {
                            $isSameGroup = false;
                            break;
                        }
                    }

                    // Check if there are no other payments with this order_id
                    if ($isSameGroup) {
                        $countInDb = Pembayaran::where('order_id', $primary->order_id)->count();
                        if ($countInDb === $payments->count()) {
                            $isValidCache = true;
                        }
                    }
                }

                if ($isValidCache) {
                    // All payments share the exact same order_id and no extras — return cached QR
                    Log::info('Doitpay: Returning cached QRIS (multi)', [
                        'payment_ids' => $request->payment_ids,
                        'order_id'    => $primary->order_id,
                    ]);
                    return response()->json([
                        'success'    => true,
                        'qr_content' => $primary->qr_content,
                        'reference'  => $primary->doitpay_reference_no,
                        'merchant'   => null,
                        'amount'     => $totalAmount,
                        'expires_in' => now()->diffInSeconds($primary->qr_expired_at, false),
                    ]);
                }


                // ── Generate fresh QRIS ────────────────────────────────────
                $freshOrderId = 'QRIS-' . strtoupper(\Illuminate\Support\Str::random(8)) . '-' . time();

                // Assign same order_id to all, clear stale QR state
                foreach ($payments as $p) {
                    $p->update([
                        'order_id'             => $freshOrderId,
                        'qr_content'           => null,
                        'doitpay_reference_no' => null,
                    ]);
                }

                $partnerRefNo = $freshOrderId;

                Log::info('Doitpay: Generating QRIS (multi-payment)', [
                    'payment_ids' => $request->payment_ids,
                    'order_id'    => $partnerRefNo,
                    'amount'      => $totalAmount,
                ]);

                $result = $this->doitpay->generateQris($partnerRefNo, (float) $totalAmount, self::QR_VALIDITY_MINUTES);

                if ($result['success']) {
                    $data = $result['data'];

                    // Save QR to all linked payments
                    foreach ($payments as $p) {
                        $p->update([
                            'qr_content'           => $data['qrContent'] ?? null,
                            'doitpay_reference_no' => $data['referenceNo'] ?? null,
                            'qr_expired_at'        => now()->addMinutes(self::QR_VALIDITY_MINUTES),
                        ]);
                    }

                    return response()->json([
                        'success'    => true,
                        'qr_content' => $data['qrContent'] ?? null,
                        'reference'  => $data['referenceNo'] ?? null,
                        'merchant'   => $data['merchantName'] ?? null,
                        'amount'     => $totalAmount,
                        'expires_in' => self::QR_VALIDITY_MINUTES * 60,
                    ]);
                }

                // ── Handle Conflict error (QR already exists at Doitpay) ──
                $errData     = $result['data'] ?? [];
                $respCode    = $errData['responseCode'] ?? '';
                $errorMsg    = $result['error'] ?? 'Gagal generate QRIS';

                if (str_starts_with($respCode, '409')) {
                    // QR already exists with this reference — try with a completely different ID
                    $retryOrderId = 'QRIS-' . strtoupper(\Illuminate\Support\Str::random(10)) . '-' . (time() + 1);
                    /** @var \App\Models\Pembayaran $p */
                    foreach ($payments as $p) {
                        $p->update(['order_id' => $retryOrderId]);
                    }
                    $retryResult = $this->doitpay->generateQris($retryOrderId, (float) $totalAmount, self::QR_VALIDITY_MINUTES);
                    if ($retryResult['success']) {
                        $data = $retryResult['data'];
                        /** @var \App\Models\Pembayaran $p */
                        foreach ($payments as $p) {
                            $p->update([
                                'qr_content'           => $data['qrContent'] ?? null,
                                'doitpay_reference_no' => $data['referenceNo'] ?? null,
                                'qr_expired_at'        => now()->addMinutes(self::QR_VALIDITY_MINUTES),
                            ]);
                        }
                        return response()->json([
                            'success'    => true,
                            'qr_content' => $data['qrContent'] ?? null,
                            'reference'  => $data['referenceNo'] ?? null,
                            'merchant'   => $data['merchantName'] ?? null,
                            'amount'     => $totalAmount,
                            'expires_in' => self::QR_VALIDITY_MINUTES * 60,
                        ]);
                    }
                    $errorMsg = $retryResult['error'] ?? 'Gagal generate QRIS (conflict)';
                }

                Log::error('Doitpay: QRIS generate failed (multi)', [
                    'payment_ids' => $request->payment_ids,
                    'order_id'    => $partnerRefNo,
                    'resp_code'   => $respCode,
                    'error'       => $errorMsg,
                ]);

                // Humanize error for known Doitpay codes
                if ($respCode === '4044701' || str_contains($errorMsg, 'Invalid Request')) {
                    $errorMsg = 'Server payment (Doitpay) menolak permintaan. Kemungkinan layanan sedang gangguan. Kode: ' . $respCode;
                }

                return response()->json([
                    'success' => false,
                    'message' => $errorMsg,
                ], 422);


            } else {
                // ── Single-payment mode (existing logic) ───────────────────
                $request->validate([
                    'pembayaran_id' => 'required|exists:pembayaran,id',
                ]);

                $pembayaran = Pembayaran::findOrFail($request->pembayaran_id);

                if ($pembayaran->isLunas()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Pembayaran sudah lunas.',
                    ], 400);
                }

                // If QR content already exists in DB, verify it belongs ONLY to this payment
                $cacheStillValid = $pembayaran->qr_expired_at && $pembayaran->qr_expired_at->isFuture();
                $isValidCache = false;
                if ($cacheStillValid && $pembayaran->qr_content && $pembayaran->doitpay_reference_no && $pembayaran->order_id) {
                    $countInDb = Pembayaran::where('order_id', $pembayaran->order_id)->count();
                    if ($countInDb === 1) {
                        $isValidCache = true;
                    }
                }

                if ($isValidCache) {
                    return response()->json([
                        'success'    => true,
                        'qr_content' => $pembayaran->qr_content,
                        'reference'  => $pembayaran->doitpay_reference_no,
                        'merchant'   => null,
                        'amount'     => $pembayaran->jumlah,
                        'expires_in' => now()->diffInSeconds($pembayaran->qr_expired_at, false),
                    ]);
                }

                $freshOrderId = 'QRIS-' . strtoupper(\Illuminate\Support\Str::random(8)) . '-' . time();
                $pembayaran->update([
                    'order_id'             => $freshOrderId,
                    'qr_content'           => null,
                    'doitpay_reference_no' => null,
                ]);

                $totalAmount  = $pembayaran->jumlah;
                $partnerRefNo = $freshOrderId;

                Log::info('Doitpay: Generating QRIS', [
                    'pembayaran_id' => $pembayaran->id,
                    'order_id'      => $partnerRefNo,
                    'amount'        => $totalAmount,
                ]);

                $result = $this->doitpay->generateQris($partnerRefNo, (float) $totalAmount, self::QR_VALIDITY_MINUTES);

                if ($result['success']) {
                    $data = $result['data'];
                    $pembayaran->update([
                        'qr_content'           => $data['qrContent'] ?? null,
                        'doitpay_reference_no' => $data['referenceNo'] ?? null,
                        'qr_expired_at'        => now()->addMinutes(self::QR_VALIDITY_MINUTES),
                    ]);

                    return response()->json([
                        'success'    => true,
                        'qr_content' => $data['qrContent'] ?? null,
                        'reference'  => $data['referenceNo'] ?? null,
                        'merchant'   => $data['merchantName'] ?? null,
                        'amount'     => $totalAmount,
                        'expires_in' => self::QR_VALIDITY_MINUTES * 60,
                    ]);
                }

                // ── Handle Conflict error (single) ──
                $errData  = $result['data'] ?? [];
                $respCode = $errData['responseCode'] ?? '';
                $errorMsg = $result['error'] ?? 'Gagal generate QRIS';

                if (str_starts_with($respCode, '409')) {
                    $retryOrderId = 'QRIS-' . strtoupper(\Illuminate\Support\Str::random(10)) . '-' . (time() + 1);
                    $pembayaran->update(['order_id' => $retryOrderId]);
                    $retryResult = $this->doitpay->generateQris($retryOrderId, (float) $totalAmount, self::QR_VALIDITY_MINUTES);
                    if ($retryResult['success']) {
                        $data = $retryResult['data'];
                        $pembayaran->update([
                            'qr_content'           => $data['qrContent'] ?? null,
                            'doitpay_reference_no' => $data['referenceNo'] ?? null,
                            'qr_expired_at'        => now()->addMinutes(self::QR_VALIDITY_MINUTES),
                        ]);
                        return response()->json([
                            'success'    => true,
                            'qr_content' => $data['qrContent'] ?? null,
                            'reference'  => $data['referenceNo'] ?? null,
                            'merchant'   => $data['merchantName'] ?? null,
                            'amount'     => $totalAmount,
                            'expires_in' => self::QR_VALIDITY_MINUTES * 60,
                        ]);
                    }
                    $errorMsg = $retryResult['error'] ?? 'Gagal generate QRIS (conflict)';
                }

                Log::error('Doitpay: QRIS generate failed', [
                    'pembayaran_id' => $pembayaran->id,
                    'order_id'      => $partnerRefNo,
                    'resp_code'     => $respCode,
                    'error'         => $errorMsg,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $errorMsg,
                ], 422);
            }

        } catch (\Exception $e) {
            Log::error('Doitpay: generateQris exception', [
                'error' => $e->getMessage(),
                'file'  => $e->getFile(),
                'line'  => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Check payment status via query API
     * Called via AJAX polling from bayar page
     */
    public function checkStatus(Request $request)
    {
        $request->validate([
            'pembayaran_id' => 'required|exists:pembayaran,id',
        ]);

        $pembayaran = Pembayaran::findOrFail($request->pembayaran_id);

        if ($pembayaran->isLunas()) {
            return response()->json([
                'success' => true,
                'status'  => 'lunas',
                'message' => 'Pembayaran sudah berhasil.',
            ]);
        }

        if (!$pembayaran->doitpay_reference_no) {
            return response()->json([
                'success' => false,
                'status'  => 'no_reference',
                'message' => 'Belum ada referensi pembayaran.',
            ]);
        }

        $result = $this->doitpay->queryPayment(
            $pembayaran->order_id,
            $pembayaran->doitpay_reference_no
        );

        if ($result['success'] && isset($result['data']['latestTransactionStatus'])) {
            $txStatus = $result['data']['latestTransactionStatus'];

            // Status 00 = Success, 01 = Initiated, 02 = Paying, 03 = Pending, 04 = Refund
            if ($txStatus === '00') {
                // Mark ALL payments that share the same order_id as lunas
                // (supports multi-payment QRIS where multiple records share 1 order_id)
                $updatePayload = [
                    'status'         => 'lunas',
                    'metode_bayar'   => 'qris_doitpay',
                    'tanggal_bayar'  => now(),
                    'transaction_id' => $result['data']['originalReferenceNo'] ?? $pembayaran->doitpay_reference_no,
                ];

                Pembayaran::where('order_id', $pembayaran->order_id)
                    ->where('status', '!=', 'lunas')
                    ->update($updatePayload);

                return response()->json([
                    'success' => true,
                    'status'  => 'lunas',
                    'message' => 'Pembayaran berhasil!',
                ]);
            }

            return response()->json([
                'success' => true,
                'status'  => 'pending',
                'message' => $result['data']['transactionStatusDesc'] ?? 'Menunggu pembayaran...',
            ]);
        }

        return response()->json([
            'success' => true,
            'status'  => 'pending',
            'message' => 'Menunggu pembayaran...',
        ]);
    }

    /**
     * Doitpay Payment Notification Callback
     * This endpoint receives payment notifications from Doitpay server
     * URL: POST /doitpay/callback
     */
    public function callback(Request $request)
    {
        $rawBody = $request->getContent();
        $data = json_decode($rawBody, true);

        Log::info('Doitpay Callback received', [
            'headers' => $request->headers->all(),
            'body'    => $data,
        ]);

        // Verify signature from headers
        $timestamp = $request->header('X-TIMESTAMP', '');
        $signature = $request->header('X-SIGNATURE', '');

        // Log the callback for debugging
        Log::info('Doitpay Callback data', [
            'partnerReferenceNo' => $data['partnerReferenceNo'] ?? 'N/A',
            'amount'             => $data['amount'] ?? 'N/A',
            'responseCode'       => $data['responseCode'] ?? 'N/A',
        ]);

        // Extract payment info from callback
        $partnerRefNo = $data['partnerReferenceNo'] ?? null;
        $originalRefNo = $data['originalReferenceNo'] ?? null;
        $responseCode = $data['responseCode'] ?? null;
        $transactionStatus = $data['latestTransactionStatus'] ?? null;

        if (!$partnerRefNo) {
            Log::warning('Doitpay Callback: Missing partnerReferenceNo');
            return response()->json([
                'responseCode'    => '4005200',
                'responseMessage' => 'Missing partnerReferenceNo',
            ], 400);
        }

        // Find pembayaran records by order_id (which is the partnerReferenceNo)
        $payments = Pembayaran::where('order_id', $partnerRefNo)->get();

        if ($payments->isEmpty()) {
            Log::warning('Doitpay Callback: Payment not found', ['partnerRefNo' => $partnerRefNo]);
            return response()->json([
                'responseCode'    => '4045201',
                'responseMessage' => 'Transaction Not Found',
            ], 404);
        }

        // Process based on transaction status
        // Status 00 = Success
        if ($transactionStatus === '00' || $responseCode === '2005200') {
            foreach ($payments as $payment) {
                if ($payment->status !== 'lunas') {
                    $payment->update([
                        'status'              => 'lunas',
                        'metode_bayar'        => 'qris_doitpay',
                        'tanggal_bayar'       => now(),
                        'transaction_id'      => $originalRefNo ?? $payment->doitpay_reference_no,
                        'doitpay_reference_no' => $originalRefNo ?? $payment->doitpay_reference_no,
                    ]);
                }
            }

            Log::info('Doitpay Callback: Payment marked as lunas', [
                'order_id' => $partnerRefNo,
                'count'    => $payments->count(),
            ]);

            return response()->json([
                'responseCode'    => '2005200',
                'responseMessage' => 'Success',
            ]);
        }

        // Other statuses - log but don't change payment status
        Log::info('Doitpay Callback: Non-success status', [
            'order_id'          => $partnerRefNo,
            'transactionStatus' => $transactionStatus,
            'responseCode'      => $responseCode,
        ]);

        return response()->json([
            'responseCode'    => '2005200',
            'responseMessage' => 'Received',
        ]);
    }
}
