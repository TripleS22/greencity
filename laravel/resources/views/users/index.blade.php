@extends('layouts.app')

@section('title', 'Manajemen Admin — Iuran Warga')

@section('styles')
<style>
    .user-avatar {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 700;
        font-size: 0.95rem;
        flex-shrink: 0;
    }

    .user-info { line-height: 1.3; }
    .user-name { font-weight: 600; font-size: 0.92rem; color: var(--text); }
    .user-email { font-size: 0.8rem; color: var(--text-secondary); }

    .self-badge {
        font-size: 0.72rem;
        font-weight: 600;
        background: var(--primary-lighter);
        color: var(--primary);
        padding: 2px 9px;
        border-radius: 20px;
        margin-left: 6px;
        vertical-align: middle;
    }

    .btn-danger {
        background: var(--red-light);
        color: var(--red);
        border: 1px solid rgba(239,68,68,0.2);
    }
    .btn-danger:hover {
        background: var(--red);
        color: #fff;
    }

    .password-wrapper { position: relative; }
    .password-wrapper .toggle-pw {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        color: var(--text-secondary);
        padding: 2px;
    }
    .password-wrapper .toggle-pw:hover { color: var(--primary); }
    .password-wrapper input { padding-right: 40px; }

    @media (max-width: 768px) {
        .users-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>
@endsection

@section('content')

{{-- Page Header --}}
<div class="hero" style="padding: 2rem 1rem 1rem;">
    <div class="hero-icon">
        <i data-lucide="shield-check" style="width:28px;height:28px"></i>
    </div>
    <h1 style="font-size:1.8rem">Manajemen Admin</h1>
    <p>Kelola akun admin yang bisa mengakses sistem iuran warga</p>
</div>

<div style="display:grid; grid-template-columns: 1fr 380px; gap:1.5rem; align-items:start;" class="users-grid">

    {{-- Daftar Admin --}}
    <div class="card">
        <div class="card-header">
            <div>
                <div class="card-title">Daftar Admin</div>
                <div style="font-size:0.82rem;color:var(--text-secondary);margin-top:2px;">
                    {{ $users->count() }} akun terdaftar
                </div>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Admin</th>
                        <th>Dibuat</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="user-avatar">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="user-info">
                                    <div class="user-name">
                                        {{ $user->name }}
                                        @if($user->id === auth()->id())
                                            <span class="self-badge">Anda</span>
                                        @endif
                                    </div>
                                    <div class="user-email">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td style="font-size:0.85rem;color:var(--text-secondary);">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td class="text-right">
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy', $user) }}"
                                  onsubmit="return confirm('Hapus admin {{ $user->name }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i data-lucide="trash-2" style="width:14px;height:14px"></i>
                                    Hapus
                                </button>
                            </form>
                            @else
                            <span style="font-size:0.8rem;color:var(--text-muted);">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center" style="padding:2rem;color:var(--text-muted);">
                            Belum ada admin terdaftar.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Form Tambah Admin --}}
    <div class="card" style="position:sticky;top:80px;">
        <div class="card-header" style="margin-bottom:1rem;">
            <div class="card-title" style="display:flex;align-items:center;gap:8px;">
                <i data-lucide="user-plus" style="width:18px;height:18px;color:var(--primary)"></i>
                Tambah Admin Baru
            </div>
        </div>

        @if($errors->any())
        <div class="alert alert-error">
            <i data-lucide="alert-circle" style="width:16px;height:16px;flex-shrink:0"></i>
            {{ $errors->first() }}
        </div>
        @endif

        <form method="POST" action="{{ route('users.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-input" value="{{ old('name') }}"
                       placeholder="Contoh: Budi Santoso" required>
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input" value="{{ old('email') }}"
                       placeholder="admin@example.com" required>
            </div>

            <div class="form-group">
                <label class="form-label">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="pwd" name="password" class="form-input"
                           placeholder="Minimal 6 karakter" required>
                    <button type="button" class="toggle-pw" onclick="togglePwd('pwd','eye1')">
                        <i data-lucide="eye" id="eye1" style="width:15px;height:15px"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Konfirmasi Password</label>
                <div class="password-wrapper">
                    <input type="password" id="pwd2" name="password_confirmation" class="form-input"
                           placeholder="Ulangi password" required>
                    <button type="button" class="toggle-pw" onclick="togglePwd('pwd2','eye2')">
                        <i data-lucide="eye" id="eye2" style="width:15px;height:15px"></i>
                    </button>
                </div>
            </div>

            <div style="background:var(--primary-lightest);border:1px solid rgba(34,197,94,0.2);border-radius:10px;padding:10px 12px;margin-bottom:1.25rem;font-size:0.82rem;color:var(--primary);display:flex;gap:8px;align-items:flex-start;">
                <i data-lucide="info" style="width:14px;height:14px;flex-shrink:0;margin-top:1px"></i>
                <span>Akun baru otomatis mendapat role <strong>Admin</strong> dan bisa mengakses semua fitur pengelolaan.</span>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;">
                <i data-lucide="user-plus" style="width:16px;height:16px"></i>
                Tambah Admin
            </button>
        </form>
    </div>

</div>

@endsection

@section('scripts')
<script>
function togglePwd(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon  = document.getElementById(iconId);
    if (input.type === 'password') {
        input.type = 'text';
        icon.setAttribute('data-lucide', 'eye-off');
    } else {
        input.type = 'password';
        icon.setAttribute('data-lucide', 'eye');
    }
    lucide.createIcons();
}
</script>
@endsection
