@extends('layouts.app')

@section('title', 'Pembayaran QRIS - Iuran Warga')

@section('content')
<style>
    .payment-container {
        max-width: 560px;
        margin: 2rem auto;
    }

    /* QRIS Container */
    .qris-container {
        text-align: center;
    }

    .qris-qr-wrapper {
        background: white;
        border: 2px solid var(--border);
        border-radius: 16px;
        padding: 1.5rem;
        display: inline-block;
        margin: 1rem auto;
        position: relative;
    }

    .qris-qr-wrapper canvas {
        display: block;
        margin: 0 auto;
    }

    .qris-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        padding: 2rem;
        color: var(--text-secondary);
    }

    .qris-loading .spinner {
        width: 40px;
        height: 40px;
        border: 3px solid var(--border);
        border-top-color: var(--primary);
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .qris-status {
        display: flex;
        align-items: center;
        gap: 8px;
        justify-content: center;
        padding: 10px 20px;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-top: 1rem;
    }

    .qris-status.waiting {
        background: var(--yellow-light);
        color: #a16207;
    }

    .qris-status.success {
        background: var(--primary-lighter);
        color: var(--primary);
    }

    .qris-timer {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-top: 0.5rem;
    }

    .qris-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: white;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        margin-bottom: 1rem;
    }

    /* Success Animation */
    .payment-success-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }

    .payment-success-overlay.active {
        display: flex;
    }

    .success-card {
        background: white;
        border-radius: 24px;
        padding: 3rem;
        text-align: center;
        animation: popIn 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        max-width: 400px;
        width: 90%;
    }

    @keyframes popIn {
        from { opacity: 0; transform: scale(0.8); }
        to { opacity: 1; transform: scale(1); }
    }

    .success-icon {
        width: 80px;
        height: 80px;
        background: var(--primary-lighter);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        color: var(--primary);
    }

    .success-checkmark {
        animation: checkmark 0.4s ease 0.2s both;
    }

    @keyframes checkmark {
        from { opacity: 0; transform: scale(0); }
        to { opacity: 1; transform: scale(1); }
    }
</style>

<div class="payment-container">
    <div class="card" style="padding: 2rem;">
        {{-- Header --}}
        <div style="text-align: center; margin-bottom: 1.5rem;">
            <div class="hero-icon" style="margin-bottom: 1rem;">
                <i data-lucide="qr-code" style="width:28px;height:28px"></i>
            </div>
            <h2 style="font-size:1.3rem; font-weight:700; margin-bottom:0.25rem;">Pembayaran Iuran QRIS</h2>
            <div style="font-size: 0.6rem; color: var(--text-muted); margin-bottom: 0.5rem;">v1.0.2</div>
            <div style="font-weight:600; color:var(--text); font-size:1.1rem; margin-bottom:0.25rem;">{{ ($primaryPayment ?? $pembayaran)->warga->nama }}</div>
            <div style="display:flex; flex-wrap:wrap; justify-content:center; gap:8px;">
                @foreach($months as $m)
                    <span style="font-size:0.75rem; background:var(--bg); padding:4px 10px; border-radius:100px; border:1px solid var(--border); color:var(--text-secondary);">
                        {{ $m }}
                    </span>
                @endforeach
            </div>
        </div>

        {{-- Amount --}}
        <div style="background:var(--primary-lightest); border-radius:12px; padding:1.5rem; margin-bottom:1.5rem; text-align:center;">
            <div style="color:var(--text-secondary); font-size:0.85rem; margin-bottom:0.25rem;">Total Pembayaran ({{ count($months) }} Bulan)</div>
            <div style="font-size:2rem; font-weight:800; color:var(--primary);">Rp {{ number_format($totalAmount, 0, ',', '.') }}</div>
        </div>

        {{-- QRIS Content --}}
        <div class="qris-container">
            <div class="qris-badge">
                <i data-lucide="shield-check" style="width:14px;height:14px"></i>
                QRIS - Powered by Doitpay
            </div>

            <div id="qrisLoading" class="qris-loading">
                <div class="spinner"></div>
                <span>Generating QRIS code...</span>
            </div>

            <div id="qrisDisplay" style="display: none;">
                <div class="qris-qr-wrapper">
                    <canvas id="qrisCanvas"></canvas>
                </div>

                <div class="qris-status waiting" id="qrisStatus">
                    <div class="spinner" style="width:16px;height:16px;border-width:2px;"></div>
                    <span id="qrisStatusText">Menunggu pembayaran...</span>
                </div>

                <div class="qris-timer" id="qrisTimer">
                    QR berlaku selama <strong id="countdown">30:00</strong>
                </div>

                <button type="button" class="btn btn-primary" id="regenBtn" onclick="generateQris()"
                    style="display:none; margin-top:0.75rem; align-items:center; gap:0.5rem; padding:0.6rem 1.25rem;">
                    <i data-lucide="refresh-cw" style="width:15px;height:15px"></i>
                    Buat QR Baru
                </button>

                <p style="font-size:0.78rem; color:var(--text-muted); margin-top:1rem; max-width:320px; margin-left:auto; margin-right:auto;">
                    Buka aplikasi e-wallet atau mobile banking Anda, pilih menu <strong>Scan QR / QRIS</strong>, lalu scan kode di atas.
                </p>
            </div>

            <div id="qrisError" style="display: none;">
                <div style="padding:2rem; text-align:center;">
                    <div style="width:64px;height:64px;background:#FEF2F2;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;color:#EF4444;">
                        <i data-lucide="wifi-off" style="width:30px;height:30px"></i>
                    </div>
                    <p style="font-weight:700;color:var(--text);margin-bottom:0.5rem;font-size:1rem;">Gagal Membuat QRIS</p>
                    <div style="background:#FFF7ED;border:1px solid #FED7AA;border-radius:10px;padding:0.75rem 1rem;margin:0.75rem 0;text-align:left;">
                        <p style="font-size:0.78rem;color:#92400E;margin:0;line-height:1.5;">
                            <strong>Keterangan:</strong> <span id="qrisErrorMessage">Terjadi kesalahan.</span>
                        </p>
                    </div>
                    <p style="font-size:0.78rem;color:var(--text-muted);margin-bottom:1rem;">
                        Server payment sedang tidak dapat diakses. Silakan coba beberapa saat lagi atau hubungi admin jika masalah berlanjut.
                    </p>
                    <div style="display:flex;gap:0.75rem;justify-content:center;flex-wrap:wrap;">
                        <button type="button" class="btn btn-primary" onclick="generateQris()" id="retryBtn"
                            style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.6rem 1.25rem;">
                            <i data-lucide="refresh-cw" style="width:15px;height:15px"></i>
                            Coba Lagi
                        </button>
                        <a href="{{ route('dashboard') }}"
                            style="display:inline-flex;align-items:center;gap:0.5rem;padding:0.6rem 1.25rem;border:1px solid var(--border);border-radius:8px;color:var(--text-secondary);font-size:0.85rem;text-decoration:none;">
                            <i data-lucide="arrow-left" style="width:15px;height:15px"></i>
                            Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div style="text-align: center; margin-top: 1.5rem;">
            <a href="{{ route('dashboard') }}" style="display:inline-block; color:var(--text-secondary); text-decoration:none; font-size:0.85rem;">
                ← Kembali ke Dashboard
            </a>
        </div>
    </div>
</div>

{{-- Payment Success Overlay --}}
<div class="payment-success-overlay" id="successOverlay">
    <div class="success-card">
        <div class="success-icon">
            <i data-lucide="check" style="width:40px;height:40px" class="success-checkmark"></i>
        </div>
        <h2 style="font-size:1.5rem;font-weight:800;color:var(--text);margin-bottom:0.5rem;">Pembayaran Berhasil!</h2>
        <p style="color:var(--text-secondary);font-size:0.9rem;margin-bottom:1.5rem;">Terima kasih, iuran Anda telah tercatat.</p>
        <a href="{{ route('dashboard') }}" class="btn btn-primary" style="width:100%;justify-content:center;padding:14px;text-decoration:none;">
            Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection

@section('scripts')
{{-- QR Code library (QRious - reliable CDN) --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>

<script>
    // Support both single and multi-payment modes
    @if(isset($primaryPayment))
        const PEMBAYARAN_ID = null;
        const PAYMENT_IDS   = [{{ $allPayments->pluck('id')->join(',') }}];
    @else
        const PEMBAYARAN_ID = {{ $pembayaran->id }};
        const PAYMENT_IDS   = [];
    @endif
    const CSRF_TOKEN = '{{ csrf_token() }}';
    let pollingInterval   = null;
    let countdownInterval = null;
    // Primary ID to poll status
    const PRIMARY_ID = PAYMENT_IDS.length > 0 ? PAYMENT_IDS[0] : PEMBAYARAN_ID;

    // ===== QRIS Generation =====
    async function generateQris() {
        const loading = document.getElementById('qrisLoading');
        const display = document.getElementById('qrisDisplay');
        const error   = document.getElementById('qrisError');

        loading.style.display = 'flex';
        display.style.display = 'none';
        error.style.display   = 'none';

        // Abort fetch if server takes too long (2 retries × 45s timeout + overhead)
        const controller = new AbortController();
        const timeoutId  = setTimeout(() => controller.abort(), 100000);

        try {
            const response = await fetch('{{ route("doitpay.generate") }}', {
                method: 'POST',
                signal: controller.signal,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                },
                body: PAYMENT_IDS.length > 0
                    ? JSON.stringify({ payment_ids: PAYMENT_IDS })
                    : JSON.stringify({ pembayaran_id: PEMBAYARAN_ID })
            });

            clearTimeout(timeoutId);
            const data = await response.json();

            if (data.success && data.qr_content) {
                loading.style.display = 'none';
                display.style.display = 'block';

                // Render QR code using QRious
                const canvas = document.getElementById('qrisCanvas');
                new QRious({
                    element: canvas,
                    value: data.qr_content,
                    size: 260,
                    level: 'M',
                    foreground: '#1a1a2e',
                    background: '#ffffff'
                });

                // Hide regenerate button (fresh QR active)
                document.getElementById('regenBtn').style.display = 'none';

                // Start polling for payment status
                startPolling();

                // Start countdown from the QR's real remaining lifetime
                startCountdown(Math.max(0, Math.floor(data.expires_in ?? 30 * 60)));

                lucide.createIcons();
            } else {
                loading.style.display = 'none';
                error.style.display   = 'block';
                document.getElementById('qrisErrorMessage').textContent = data.message || 'Unknown error (HTTP ' + response.status + ')';
                lucide.createIcons();
            }
        } catch (err) {
            clearTimeout(timeoutId);
            loading.style.display = 'none';
            error.style.display   = 'block';
            const msg = err.name === 'AbortError'
                ? 'Request timeout — server terlalu lama merespons. Coba lagi.'
                : 'Koneksi gagal: ' + err.message;
            document.getElementById('qrisErrorMessage').textContent = msg;
            lucide.createIcons();
            console.error('QRIS Error:', err);
        }
    }

    // ===== Payment Status Polling =====
    function startPolling() {
        if (pollingInterval) clearInterval(pollingInterval);

        pollingInterval = setInterval(async () => {
            try {
                const response = await fetch('{{ route("doitpay.checkStatus") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ pembayaran_id: PRIMARY_ID })
                });

                const data = await response.json();

                if (data.status === 'lunas') {
                    clearInterval(pollingInterval);
                    clearInterval(countdownInterval);
                    showPaymentSuccess();
                }
            } catch (err) {
                console.error('Polling error:', err);
            }
        }, 5000); // Poll every 5 seconds
    }

    // ===== Countdown Timer =====
    function startCountdown(totalSeconds) {
        if (countdownInterval) clearInterval(countdownInterval);

        let remaining = totalSeconds;
        const el = document.getElementById('countdown');

        countdownInterval = setInterval(() => {
            remaining--;
            if (remaining <= 0) {
                clearInterval(countdownInterval);
                clearInterval(pollingInterval);
                el.textContent = 'Expired';
                document.getElementById('qrisStatusText').textContent = 'QR kadaluarsa — buat QR baru untuk bayar';
                document.getElementById('regenBtn').style.display = 'inline-flex';
                lucide.createIcons();
                return;
            }
            const min = Math.floor(remaining / 60).toString().padStart(2, '0');
            const sec = (remaining % 60).toString().padStart(2, '0');
            el.textContent = `${min}:${sec}`;
        }, 1000);
    }

    // ===== Show Payment Success =====
    function showPaymentSuccess() {
        const statusEl = document.getElementById('qrisStatus');
        statusEl.className = 'qris-status success';
        statusEl.innerHTML = '<span>✓ Pembayaran Berhasil!</span>';

        // Show overlay after a short delay
        setTimeout(() => {
            document.getElementById('successOverlay').classList.add('active');
            lucide.createIcons();
        }, 500);
    }

    // ===== Auto-generate QRIS on page load =====
    document.addEventListener('DOMContentLoaded', function() {
        generateQris();
    });
</script>
@endsection
