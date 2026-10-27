<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Tiket - IT Helpdesk</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @if(isset($ticket) && $ticket && in_array($ticket->status, ['open', 'progress']))
    {{-- Auto-refresh setiap 30 detik untuk update status antrian --}}
    <meta http-equiv="refresh" content="30">
    @endif
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #2e6429 0%, #3a7d34 50%, #245520 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.25);
            width: 100%;
            max-width: 600px;
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #3a7d34, #245520);
            color: #fff;
            padding: 32px 40px;
            text-align: center;
        }
        .header i { font-size: 40px; margin-bottom: 12px; opacity: 0.9; }
        .header h1 { font-size: 22px; font-weight: 700; margin-bottom: 6px; }
        .header p { font-size: 13px; opacity: 0.8; }
        .body { padding: 32px 40px; }
        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block; font-size: 13px; font-weight: 600;
            color: #333; margin-bottom: 6px;
        }
        .form-control {
            width: 100%; padding: 11px 14px;
            border: 1.5px solid #ddd; border-radius: 8px;
            font-size: 15px; font-family: inherit; font-weight: 600;
            letter-spacing: 1px; text-align: center;
            transition: border-color .2s, box-shadow .2s;
            background: #fafafa;
        }
        .form-control:focus {
            outline: none; border-color: #3a7d34;
            box-shadow: 0 0 0 3px rgba(58,125,52,.12);
            background: #fff;
        }
        .btn-submit {
            width: 100%; padding: 13px;
            background: linear-gradient(135deg, #3a7d34, #245520);
            color: #fff; border: none; border-radius: 8px;
            font-size: 15px; font-weight: 600; cursor: pointer;
            font-family: inherit; transition: all .2s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            margin-bottom: 20px;
        }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(58,125,52,.4); }

        /* Alert styles */
        .alert-success {
            background: #d4edda; color: #155724;
            border: 1px solid #c3e6cb; border-radius: 10px;
            padding: 16px 18px; margin-bottom: 20px;
            display: flex; align-items: flex-start; gap: 12px;
            font-size: 13px; animation: slideIn .4s ease;
        }
        .alert-success i { font-size: 20px; margin-top: 1px; flex-shrink: 0; }
        .alert-error {
            background: #f8d7da; color: #721c24;
            border: 1px solid #f5c6cb; border-radius: 10px;
            padding: 14px 18px; margin-bottom: 20px; font-size: 13px;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Ticket detail card */
        .ticket-card {
            border: 1.5px solid #e8f5e9;
            border-radius: 12px;
            overflow: hidden;
            margin-top: 4px;
            animation: slideIn .4s ease;
        }
        .ticket-card-header {
            background: linear-gradient(135deg, #f1f8f1, #e8f5e9);
            padding: 14px 20px;
            display: flex; align-items: center; justify-content: space-between;
            border-bottom: 1px solid #dcedc8;
        }
        .ticket-code-display {
            font-size: 16px; font-weight: 700; color: #245520;
            letter-spacing: 1.5px; font-family: monospace;
        }
        .ticket-card-body { padding: 18px 20px; }
        .detail-row {
            display: flex; margin-bottom: 13px;
            font-size: 13.5px; align-items: flex-start;
        }
        .detail-row:last-child { margin-bottom: 0; }
        .detail-row .label {
            width: 130px; font-weight: 600;
            color: #666; flex-shrink: 0;
            padding-top: 2px;
        }
        .detail-row .value { flex: 1; color: #222; }

        /* Badge status */
        .badge {
            padding: 4px 10px; border-radius: 20px;
            font-size: 12px; font-weight: 700;
            color: white; display: inline-flex;
            align-items: center; gap: 5px;
        }
        .badge-open    { background: linear-gradient(135deg, #f39c12, #e67e22); }
        .badge-progress{ background: linear-gradient(135deg, #3498db, #2980b9); }
        .badge-closed  { background: linear-gradient(135deg, #2ecc71, #27ae60); }

        /* Queue position box */
        .queue-box {
            background: linear-gradient(135deg, #fff8e1, #fff3cd);
            border: 1.5px solid #ffd54f;
            border-radius: 10px;
            padding: 16px 20px;
            margin-bottom: 16px;
            display: flex; align-items: center; gap: 16px;
        }
        .queue-number {
            font-size: 42px; font-weight: 800; color: #f57c00;
            line-height: 1;
        }
        .queue-info { flex: 1; }
        .queue-info .queue-title {
            font-size: 13px; font-weight: 700; color: #555;
            text-transform: uppercase; letter-spacing: .5px;
            margin-bottom: 3px;
        }
        .queue-info .queue-desc { font-size: 12px; color: #888; }

        /* Progress timeline */
        .timeline {
            display: flex; gap: 0;
            margin: 20px 0 10px;
        }
        .timeline-step {
            flex: 1; text-align: center; position: relative;
        }
        .timeline-step::before {
            content: '';
            position: absolute; top: 18px; left: 50%; right: -50%;
            height: 3px; background: #eee; z-index: 0;
        }
        .timeline-step:last-child::before { display: none; }
        .timeline-step.active::before,
        .timeline-step.done::before { background: #3a7d34; }
        .step-circle {
            width: 38px; height: 38px; border-radius: 50%;
            background: #eee; color: #aaa;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 6px; font-size: 14px;
            position: relative; z-index: 1;
            transition: all .3s;
        }
        .timeline-step.done   .step-circle { background: #3a7d34; color: white; }
        .timeline-step.active .step-circle { background: #3498db; color: white; box-shadow: 0 0 0 4px rgba(52,152,219,.2); }
        .step-label { font-size: 10.5px; color: #aaa; font-weight: 600; }
        .timeline-step.done   .step-label { color: #3a7d34; }
        .timeline-step.active .step-label { color: #3498db; }

        /* Resolution box */
        .resolution-box {
            background: #f0fdf4; border: 1px solid #bbf7d0;
            border-radius: 10px; padding: 16px 18px;
            margin-top: 16px;
        }
        .resolution-box .res-title {
            font-size: 12px; font-weight: 700; color: #166534;
            text-transform: uppercase; letter-spacing: .5px; margin-bottom: 6px;
        }
        .resolution-notes {
            background: white; border: 1px solid #dcfce7;
            border-radius: 8px; padding: 10px 14px;
            font-size: 13px; color: #333; margin-top: 8px;
        }

        /* Refresh info */
        .refresh-info {
            text-align: center; font-size: 11px; color: #bbb;
            margin-top: 14px; display: flex; align-items: center;
            justify-content: center; gap: 5px;
        }
        .refresh-info i { font-size: 12px; }
        #countdown { font-weight: 600; color: #888; }

        /* Copy code button */
        .copy-btn {
            background: none; border: 1px solid #3a7d34;
            color: #3a7d34; padding: 4px 10px; border-radius: 6px;
            font-size: 11px; cursor: pointer; font-family: inherit;
            font-weight: 600; display: flex; align-items: center;
            gap: 4px; transition: all .2s;
        }
        .copy-btn:hover { background: #3a7d34; color: white; }

        .new-ticket-link {
            display: block; text-align: center; margin-top: 16px;
            font-size: 13px; color: #888;
        }
        .new-ticket-link a {
            color: #3a7d34; font-weight: 600; text-decoration: none;
        }
        .new-ticket-link a:hover { text-decoration: underline; }

        .footer-note {
            text-align: center; font-size: 12px; color: #999;
            padding: 16px 40px 24px;
            border-top: 1px solid #f0f0f0;
        }
        @media(max-width: 500px) {
            .header { padding: 24px 20px; }
            .body { padding: 24px 20px; }
            .detail-row { flex-direction: column; }
            .detail-row .label { width: 100%; margin-bottom: 2px; }
            .queue-number { font-size: 32px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <i class="fas fa-ticket-alt"></i>
            <h1>Status Tiket Helpdesk</h1>
            <p>Pantau perkembangan laporan IT kamu secara real-time.</p>
        </div>
        <div class="body">

            {{-- Notifikasi tiket baru dibuat --}}
            @if($justCreated ?? false)
            <div class="alert-success">
                <i class="fas fa-check-circle"></i>
                <div>
                    <strong>Tiket berhasil dikirim!</strong><br>
                    Laporan kamu sudah masuk. Kamu bisa bookmark halaman ini untuk memantau status kapan saja.
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="alert-error">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
            @endif

            {{-- Form cari tiket --}}
            <form action="{{ route('public.ticket.track.process') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label><i class="fas fa-search" style="color:#3a7d34"></i> Kode Tiket</label>
                    <input type="text" name="ticket_code" id="ticketCodeInput" class="form-control"
                        placeholder="TCK-XXXXXX"
                        value="{{ isset($ticket) && $ticket ? $ticket->ticket_code : request('ticket_code') }}" required>
                </div>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-search"></i> Cek Status
                </button>
            </form>

            {{-- Detail tiket --}}
            @if(isset($ticket) && $ticket)
                {{-- Timeline Progress --}}
                <div class="timeline">
                    <div class="timeline-step {{ in_array($ticket->status, ['open','progress','closed']) ? 'done' : '' }}">
                        <div class="step-circle"><i class="fas fa-paper-plane"></i></div>
                        <div class="step-label">Dikirim</div>
                    </div>
                    <div class="timeline-step {{ in_array($ticket->status, ['progress','closed']) ? ($ticket->status === 'progress' ? 'active' : 'done') : '' }}">
                        <div class="step-circle"><i class="fas fa-tools"></i></div>
                        <div class="step-label">Diproses</div>
                    </div>
                    <div class="timeline-step {{ $ticket->status === 'closed' ? 'done' : '' }}">
                        <div class="step-circle"><i class="fas fa-check"></i></div>
                        <div class="step-label">Selesai</div>
                    </div>
                </div>

                {{-- Posisi antrian (hanya untuk tiket open) --}}
                @if($ticket->status === 'open' && isset($queuePosition))
                <div class="queue-box">
                    <div class="queue-number">{{ $queuePosition }}</div>
                    <div class="queue-info">
                        <div class="queue-title">Posisi Antrian Kamu</div>
                        <div class="queue-desc">
                            @if($queuePosition == 1)
                                Tiket kamu adalah yang pertama dalam antrian! 🎉
                            @else
                                Ada {{ $queuePosition - 1 }} tiket lain yang masuk lebih dulu.
                            @endif
                        </div>
                    </div>
                    <i class="fas fa-list-ol" style="font-size:28px; color:#f57c00; opacity:.5;"></i>
                </div>
                @endif

                <div class="ticket-card">
                    <div class="ticket-card-header">
                        <span class="ticket-code-display">{{ $ticket->ticket_code }}</span>
                        <div style="display:flex; align-items:center; gap:8px;">
                            @if($ticket->status == 'open')
                                <span class="badge badge-open"><i class="fas fa-clock"></i> Menunggu</span>
                            @elseif($ticket->status == 'progress')
                                <span class="badge badge-progress"><i class="fas fa-spinner fa-spin"></i> Diproses</span>
                            @else
                                <span class="badge badge-closed"><i class="fas fa-check-circle"></i> Selesai</span>
                            @endif
                            <button class="copy-btn" onclick="copyCode('{{ $ticket->ticket_code }}')" title="Salin kode tiket">
                                <i class="fas fa-copy"></i> Salin
                            </button>
                        </div>
                    </div>
                    <div class="ticket-card-body">
                        <div class="detail-row">
                            <div class="label">Judul</div>
                            <div class="value"><strong>{{ $ticket->title }}</strong></div>
                        </div>
                        <div class="detail-row">
                            <div class="label">Kategori</div>
                            <div class="value">
                                <span style="background:#e8f5e9; color:#2e7d32; padding:2px 8px; border-radius:4px; font-size:12px; font-weight:600; text-transform:capitalize;">
                                    {{ $ticket->category }}
                                </span>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="label">Lokasi</div>
                            <div class="value">{{ $ticket->location ? $ticket->location->name : '-' }}</div>
                        </div>
                        <div class="detail-row">
                            <div class="label">Tanggal Lapor</div>
                            <div class="value">{{ \Carbon\Carbon::parse($ticket->request_date)->translatedFormat('d F Y') }}</div>
                        </div>
                        @if($ticket->description)
                        <div class="detail-row">
                            <div class="label">Deskripsi</div>
                            <div class="value" style="color:#555;">{{ $ticket->description }}</div>
                        </div>
                        @endif

                        {{-- Info selesai --}}
                        @if($ticket->status === 'closed')
                        <div class="resolution-box">
                            <div class="res-title"><i class="fas fa-check-circle"></i> Tiket Telah Diselesaikan</div>
                            <div style="font-size:12px; color:#555;">
                                Diselesaikan pada:
                                <strong>{{ $ticket->resolved_at ? \Carbon\Carbon::parse($ticket->resolved_at)->translatedFormat('d F Y, H:i') : '-' }}</strong>
                            </div>
                            @if($ticket->resolution_notes)
                                <div class="resolution-notes">
                                    {!! nl2br(e($ticket->resolution_notes)) !!}
                                </div>
                            @endif
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Countdown refresh (hanya saat masih open/progress) --}}
                @if(in_array($ticket->status, ['open', 'progress']))
                <div class="refresh-info">
                    <i class="fas fa-sync-alt fa-spin"></i>
                    Halaman ini akan otomatis diperbarui dalam <span id="countdown">30</span> detik
                </div>
                @endif

                <div class="new-ticket-link">
                    <a href="{{ route('public.ticket') }}"><i class="fas fa-plus"></i> Buat tiket baru</a>
                    &nbsp;·&nbsp;
                    <a href="{{ route('public.ticket.track') }}"><i class="fas fa-search"></i> Cari tiket lain</a>
                </div>

            @elseif(!request('ticket_code'))
                <p style="text-align:center; color:#aaa; font-size:13px; margin-top:8px;">
                    <i class="fas fa-info-circle"></i>
                    Masukkan kode tiket (format: <strong>TCK-XXXXXX</strong>) untuk melihat statusnya.
                </p>
                <div class="new-ticket-link" style="margin-top:12px;">
                    Belum punya tiket? <a href="{{ route('public.ticket') }}">Buat laporan baru</a>
                </div>
            @endif

        </div>
        <div class="footer-note">
            <i class="fas fa-shield-alt"></i>
            Tim IT Helpdesk siap membantu kamu.
        </div>
    </div>

    <script>
        // Countdown timer untuk auto-refresh
        (function() {
            const el = document.getElementById('countdown');
            if (!el) return;
            let secs = 30;
            setInterval(function() {
                secs--;
                if (secs <= 0) secs = 30;
                el.textContent = secs;
            }, 1000);
        })();

        // Salin kode tiket
        function copyCode(code) {
            navigator.clipboard.writeText(code).then(function() {
                const btn = document.querySelector('.copy-btn');
                const orig = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check"></i> Disalin!';
                btn.style.background = '#3a7d34';
                btn.style.color = 'white';
                setTimeout(function() {
                    btn.innerHTML = orig;
                    btn.style.background = '';
                    btn.style.color = '';
                }, 2000);
            });
        }
    </script>
</body>
</html>
