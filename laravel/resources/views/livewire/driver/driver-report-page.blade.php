<div class="max-w-2xl mx-auto animate-in fade-in zoom-in-95 duration-300">
    <div class="flex items-center gap-6 mb-8">
        <a href="/" class="p-2.5 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 text-slate-600 transition-all shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </a>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Kirim Laporan Unit</h1>
    </div>

    @if(session()->has('message'))
        <div class="mb-8 p-6 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-3xl shadow-sm flex items-center gap-4 animate-in fade-in slide-in-from-top-4 duration-300">
            <div class="w-12 h-12 bg-emerald-100 rounded-2xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <div class="font-bold text-lg">{{ session('message') }}</div>
        </div>
    @endif

    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/50 p-8 sm:p-10">
        <form wire:submit="submit" class="space-y-6">
            <!-- Role Selection -->
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Kategori Pelapor</label>
                <div class="grid grid-cols-2 gap-4">
                    <button type="button" 
                        wire:click="$set('report_role', 'Driver')"
                        class="px-4 py-3 rounded-xl border-2 transition-all font-bold text-sm {{ $report_role === 'Driver' ? 'border-emerald-600 bg-emerald-50 text-emerald-700' : 'border-slate-100 bg-slate-50 text-slate-500 hover:border-slate-200' }}">
                        Driver
                    </button>
                    <button type="button" 
                        wire:click="$set('report_role', 'Asmen/Manager')"
                        class="px-4 py-3 rounded-xl border-2 transition-all font-bold text-sm {{ $report_role === 'Asmen/Manager' ? 'border-emerald-600 bg-emerald-50 text-emerald-700' : 'border-slate-100 bg-slate-50 text-slate-500 hover:border-slate-200' }}">
                        Asmen / Manager
                    </button>
                </div>
                @error('report_role') <span class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Date -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal Kejadian</label>
                    <input type="date" wire:model="tanggal" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition-all font-semibold">
                    @error('tanggal') <span class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                </div>

                <!-- Unit selection -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Unit Code</label>
                    <select wire:model="unit_code" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition-all font-semibold">
                        <option value="">Pilih Unit...</option>
                        @foreach($units as $u)
                            <option value="{{ $u->unit_code }}">{{ $u->unit_code }} - {{ $u->model }}</option>
                        @endforeach
                    </select>
                    @error('unit_code') <span class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Reporter Name -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nama Pelapor</label>
                    <input type="text" wire:model="nama_pelapor" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition-all font-semibold" placeholder="Nama Anda...">
                    @error('nama_pelapor') <span class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                </div>

                <!-- Driver Name -->
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2">Nama Driver</label>
                    <input type="text" wire:model="nama_driver" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition-all font-semibold" placeholder="Nama driver (jika ada)...">
                    @error('nama_driver') <span class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                </div>
            </div>


            <!-- Problem -->
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Keluhan / Masalah Unit</label>
                <textarea wire:model="unit_driver_problem" rows="4" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition-all font-semibold" placeholder="Jelaskan masalah yang terjadi pada unit..."></textarea>
                @error('unit_driver_problem') <span class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full py-4 bg-slate-900 text-white font-extrabold rounded-2xl shadow-xl hover:bg-emerald-600 transition-all active:scale-[0.98] text-lg uppercase tracking-wider">
                    Kirim Laporan Sekarang
                </button>
                <p class="text-center text-xs text-slate-400 mt-6 font-medium">Laporan akan langsung diteruskan ke tim mekanik & admin.</p>
            </div>
        </form>
    </div>
</div>
