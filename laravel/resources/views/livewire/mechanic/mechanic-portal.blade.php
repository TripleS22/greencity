<div class="space-y-8">
    @if(session()->has('message'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl shadow-sm flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300">
            <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ session('message') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl shadow-sm flex items-center gap-3 animate-in fade-in slide-in-from-top-4 duration-300 mb-4">
            <svg class="w-5 h-5 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    @if($view === 'list')
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Portal Mekanik</h1>
                <p class="text-slate-500 mt-1">Kelola pengerjaan unit secara real-time.</p>
            </div>
            <button 
                wire:click="setView('create-analysis')"
                class="inline-flex items-center justify-center px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl transition-all duration-200 shadow-lg shadow-emerald-100 active:scale-95 gap-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Analisis Baru
            </button>
        </div>

        <!-- Search -->
        <div class="relative group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-slate-400 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input 
                type="text" 
                wire:model.live="search" 
                placeholder="Cari Unit atau Masalah..."
                class="block w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 transition-all outline-none"
            >
        </div>

        <!-- Tabs -->
        <div class="flex p-1 bg-slate-100 rounded-2xl w-full sm:w-fit">
            <button 
                wire:click="$set('tab', 'active')"
                class="flex items-center gap-2 flex-1 sm:flex-none px-6 py-2.5 rounded-xl text-sm font-bold transition-all {{ $tab === 'active' ? 'bg-white text-emerald-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}"
            >
                Aktif
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'active' ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-200 text-slate-600' }}">
                    {{ $activeCount }}
                </span>
            </button>
            <button 
                wire:click="$set('tab', 'archived')"
                class="flex items-center gap-2 flex-1 sm:flex-none px-6 py-2.5 rounded-xl text-sm font-bold transition-all {{ $tab === 'archived' ? 'bg-white text-emerald-600 shadow-sm' : 'text-slate-500 hover:text-slate-700' }}"
            >
                Arsip
                <span class="px-2 py-0.5 rounded-full text-[10px] {{ $tab === 'archived' ? 'bg-emerald-100 text-emerald-600' : 'bg-slate-200 text-slate-600' }}">
                    {{ $archivedCount }}
                </span>
            </button>
        </div>

        <!-- Active Tasks Summary (New) -->
        @php
            $activeReports = $analyses->filter(fn($item) => $item->mechanicReport && !$item->mechanicReport->time_finish);
        @endphp

        @if($tab === 'active' && $activeReports->count() > 0)
            <div class="bg-emerald-900 rounded-[2rem] p-6 text-white shadow-xl shadow-emerald-200 mb-8 overflow-hidden relative">
                <div class="relative z-10">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="w-2 h-2 bg-emerald-400 rounded-full animate-ping"></span>
                        <h2 class="text-sm font-bold uppercase tracking-wider text-emerald-200">Unit Sedang Dikerjakan ({{ $activeReports->count() }})</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($activeReports as $active)
                            <div 
                                wire:click="setView('create-report', {{ $active->id }})"
                                class="bg-emerald-800/50 border border-emerald-700/50 p-4 rounded-2xl hover:bg-emerald-700/50 transition-all cursor-pointer group"
                            >
                                <div class="flex justify-between items-start mb-2">
                                    <span class="font-black text-lg">{{ $active->unit_code }}</span>
                                    @if($active->mechanicReport->is_paused)
                                        <span class="text-[10px] bg-amber-500 text-white px-2 py-0.5 rounded-full font-bold">⏸ PAUSED</span>
                                    @else
                                        <span class="text-[10px] bg-emerald-500 text-white px-2 py-0.5 rounded-full font-bold">ACTIVE</span>
                                    @endif
                                </div>
                                <div class="text-xs text-emerald-300 line-clamp-1 mb-3">{{ $active->unit_driver_problem }}</div>
                                <div class="flex items-center justify-between text-[10px] font-bold text-emerald-200 uppercase">
                                    <span>Mulai: {{ \Carbon\Carbon::parse($active->mechanicReport->time_start)->format('H:i') }}</span>
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        {{ \Carbon\Carbon::parse($active->mechanicReport->time_start)->diff(now())->format('%h Jam %i Menit') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <!-- Decorative element -->
                <div class="absolute -right-10 -bottom-10 opacity-10">
                    <svg class="w-40 h-40" fill="currentColor" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
        @endif

        <!-- List -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($analyses as $item)
                <div class="bg-white rounded-3xl border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-slate-200/50 transition-all duration-300 overflow-hidden group">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-full border border-emerald-100 uppercase tracking-wider">
                                {{ $item->unit_code }}
                            </span>
                            <span class="text-slate-400 text-sm font-medium">
                                {{ \Carbon\Carbon::parse($item->tanggal)->format('d M Y') }}
                            </span>
                        </div>
                        
                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-emerald-600 transition-colors line-clamp-1">
                            {{ $item->unit_driver_problem }}
                        </h3>
                        <p class="text-slate-500 text-sm mt-1 mb-4 flex items-center gap-1.5 font-medium">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Driver: {{ $item->nama_driver ?: '-' }}
                        </p>

                        @if($item->hasil_analisis)
                        <div class="mb-4 p-3 bg-slate-50 rounded-xl border border-slate-100 italic text-xs text-slate-600">
                            <strong>Analisis:</strong> {{ Str::limit($item->hasil_analisis, 80) }}
                        </div>
                        @endif

                        <div class="pt-4 border-t border-slate-50 flex items-center justify-between gap-3">
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-slate-100 rounded-full flex items-center justify-center text-[10px] font-bold text-slate-600 border border-slate-200">
                                        {{ strtoupper(substr($item->nama_mechanic_satu, 0, 1)) }}
                                    </div>
                                    <span class="text-xs font-semibold text-slate-600 truncate max-w-[100px]">
                                        {{ $item->nama_mechanic_satu }}
                                    </span>
                                </div>
                                @if($item->nama_mechanic_dua && $item->nama_mechanic_dua !== '-')
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-slate-100 rounded-full flex items-center justify-center text-[10px] font-bold text-slate-400 border border-slate-200">
                                        {{ strtoupper(substr($item->nama_mechanic_dua, 0, 1)) }}
                                    </div>
                                    <span class="text-xs font-medium text-slate-400 truncate max-w-[100px]">
                                        {{ $item->nama_mechanic_dua }}
                                    </span>
                                </div>
                                @endif
                            </div>

                            @if($item->mechanicReport && $item->mechanicReport->time_finish)
                                <div class="flex items-center gap-1.5 text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-100 font-bold text-[10px] uppercase tracking-wide">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Completed
                                </div>
                            @elseif($item->mechanicReport && !$item->mechanicReport->time_finish && $item->mechanicReport->is_paused)
                                <button 
                                    wire:click="setView('create-report', {{ $item->id }})"
                                    class="text-[10px] uppercase tracking-widest font-bold bg-amber-500 text-white px-3 py-2 rounded-xl shadow-lg shadow-amber-100"
                                >
                                    ⏸ Paused
                                </button>
                            @elseif($item->mechanicReport && !$item->mechanicReport->time_finish)
                                <button 
                                    wire:click="setView('create-report', {{ $item->id }})"
                                    class="text-[10px] uppercase tracking-widest font-bold bg-emerald-600 text-white px-3 py-2 rounded-xl animate-pulse shadow-lg shadow-emerald-100"
                                >
                                    Sedang Bekerja
                                </button>
                            @elseif($item->is_approved)
                                <button 
                                    wire:click="setView('create-report', {{ $item->id }})"
                                    class="text-[10px] uppercase tracking-widest font-bold bg-slate-900 text-white px-3 py-2 rounded-xl hover:bg-emerald-600 transition-colors"
                                >
                                    Mulai Kerja
                                </button>
                            @else
                                <button 
                                    wire:click="setView('create-report', {{ $item->id }})"
                                    class="text-[10px] uppercase tracking-widest font-bold bg-amber-500 text-white px-3 py-2 rounded-xl hover:bg-amber-600 transition-colors shadow-lg shadow-amber-100"
                                >
                                    Analisis & Kerjakan
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center bg-white rounded-3xl border border-dashed border-slate-200">
                    <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <p class="text-slate-500 font-medium">Belum ada pengerjaan yang tersedia.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $analyses->links() }}
        </div>

    @elseif($view === 'create-analysis')
        <!-- Analysis Form Remains Same But With Premium Styling -->
        <div class="max-w-2xl mx-auto animate-in fade-in zoom-in-95 duration-300">
            <div class="flex items-center gap-6 mb-8">
                <button wire:click="setView('list')" class="p-2.5 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 text-slate-600 transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Input Analisis Mekanik</h1>
            </div>

            <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl shadow-slate-200/50 p-8">
                <form wire:submit="saveAnalysis" class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="col-span-full">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Unit Code</label>
                        <select wire:model="unit_code" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition-all">
                            <option value="">Pilih Unit...</option>
                            @foreach($units as $u)
                                <option value="{{ $u->unit_code }}">{{ $u->unit_code }} - {{ $u->model }}</option>
                            @endforeach
                        </select>
                        @error('unit_code') <span class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Tanggal</label>
                        <input type="date" wire:model="tanggal" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">SMU Km Awal</label>
                        <input type="number" wire:model="smu_down" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition-all">
                        @error('smu_down') <span class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Location Unit</label>
                        <select wire:model="location_unit" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition-all font-semibold">
                            <option value="">Pilih Lokasi...</option>
                            <option value="Sukamulya">Sukamulya</option>
                            <option value="Sadang">Sadang</option>
                        </select>
                        @error('location_unit') <span class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-span-full">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Masalah yang Dilaporkan</label>
                        <input type="text" wire:model="unit_driver_problem" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition-all" placeholder="Keluhan driver...">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Mekanik Utama</label>
                        <input type="text" wire:model="nama_mechanic_satu" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition-all" placeholder="PIC Mekanik">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Mekanik Pendamping</label>
                        <input type="text" wire:model="nama_mechanic_dua" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition-all" placeholder="Helper">
                    </div>

                    <div class="col-span-full">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Nama Driver</label>
                        <input type="text" wire:model="nama_driver" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition-all" placeholder="Nama Driver Pelapor">
                    </div>

                    <div class="col-span-full">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Hasil Analisis Kerusakan</label>
                        <textarea wire:model="hasil_analisis" rows="3" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition-all" placeholder="Apa temuan mekanik?"></textarea>
                        @error('hasil_analisis') <span class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-span-full">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Saran Perbaikan / Rencana</label>
                        <textarea wire:model="saran_perbaikan" rows="2" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition-all" placeholder="Apa tindakan selanjutnya?"></textarea>
                        @error('saran_perbaikan') <span class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                    </div>

                    <div class="col-span-full">
                        <div class="flex items-center justify-between mb-4">
                            <label class="block text-sm font-bold text-slate-700">Spare Part yang Dibutuhkan</label>
                            <button type="button" wire:click="addPart" class="text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-lg hover:bg-emerald-100 transition-colors">
                                + Tambah Part
                            </button>
                        </div>



                        <div class="space-y-3">
                            @foreach($requested_parts as $index => $part)
                                <div class="flex items-start gap-3 p-3 bg-slate-50 border border-slate-200 rounded-xl">
                                    <div class="flex-1">
                                        <div x-data="{ open: false }" class="relative" wire:key="analysis-part-{{ $index }}">
                                            <div 
                                                @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus())" 
                                                class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm cursor-pointer flex justify-between items-center min-h-[38px] transition-all hover:border-emerald-300 shadow-sm"
                                            >
                                                <span class="truncate pr-4">
                                                    @php
                                                        $selectedPart = $inventories->firstWhere('id', $requested_parts[$index]['inventory_id']);
                                                        if (!$selectedPart && $requested_parts[$index]['inventory_id']) {
                                                            $selectedPart = \App\Models\Inventory::find($requested_parts[$index]['inventory_id']);
                                                        }
                                                    @endphp
                                                    {{ $selectedPart ? $selectedPart->item_sparepart : 'Pilih Part...' }}
                                                </span>
                                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                </svg>
                                            </div>

                                            <div 
                                                x-show="open" 
                                                @click.away="open = false"
                                                x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="transform opacity-0 scale-95"
                                                x-transition:enter-end="transform opacity-100 scale-100"
                                                class="absolute z-50 w-full mt-2 bg-white border border-slate-200 rounded-xl shadow-2xl overflow-hidden"
                                                x-cloak
                                            >
                                                <div class="p-2 border-b border-slate-100 bg-slate-50">
                                                    <div class="relative">
                                                        <svg class="w-3.5 h-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                        </svg>
                                                        <input 
                                                            x-ref="searchInput"
                                                            type="text" 
                                                            wire:model.live.debounce.300ms="part_search"
                                                            placeholder="Ketik untuk mencari..."
                                                            class="w-full pl-8 pr-3 py-1.5 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 outline-none transition-all"
                                                            @click.stop
                                                        >
                                                    </div>
                                                </div>
                                                <div class="max-h-64 overflow-y-auto overflow-x-hidden">
                                                    <div 
                                                        wire:click="$set('requested_parts.{{ $index }}.inventory_id', '')"
                                                        @click="open = false"
                                                        class="px-4 py-2 hover:bg-slate-50 cursor-pointer text-xs text-slate-500 italic border-b border-slate-50"
                                                    >
                                                        -- Kosongkan Pilihan --
                                                    </div>
                                                    @forelse($inventories as $inv)
                                                        <div 
                                                            wire:click="$set('requested_parts.{{ $index }}.inventory_id', '{{ $inv->id }}')"
                                                            @click="open = false"
                                                            class="px-4 py-2.5 hover:bg-emerald-50 cursor-pointer text-sm flex flex-col gap-0.5 border-b border-slate-50 last:border-0 transition-colors"
                                                        >
                                                            <div class="flex justify-between items-start gap-2">
                                                                <span class="font-semibold text-slate-700 leading-tight">{{ $inv->item_sparepart }}</span>
                                                                <span class="shrink-0 text-[10px] font-bold {{ $inv->ends_balance > 5 ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50' }} px-1.5 py-0.5 rounded-full border border-current opacity-70">
                                                                    {{ (int)$inv->ends_balance }}
                                                                </span>
                                                            </div>
                                                            @if($inv->part_number)
                                                                <span class="text-[10px] text-slate-400 font-mono">{{ $inv->part_number }}</span>
                                                            @endif
                                                        </div>
                                                    @empty
                                                        <div class="px-4 py-10 text-center text-slate-400 text-xs">
                                                            <svg class="w-8 h-8 mx-auto mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                            </svg>
                                                            Tidak ada part ditemukan
                                                        </div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="w-20">
                                        <input type="number" wire:model="requested_parts.{{ $index }}.quantity" class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm" placeholder="Qty">
                                    </div>
                                    <button type="button" wire:click="removePart({{ $index }})" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="col-span-full pt-4">
                        <button type="submit" class="w-full py-4 bg-slate-900 text-white font-extrabold rounded-2xl shadow-xl hover:bg-emerald-600 transition-all">
                            Simpan Analisis
                        </button>
                    </div>
                </form>
            </div>
        </div>

    @elseif($view === 'create-report')
        <!-- Timer View -->
        <div class="max-w-2xl mx-auto animate-in fade-in zoom-in-95 duration-300">
            <div class="flex items-center gap-6 mb-8">
                <button wire:click="setView('list')" class="p-2.5 bg-white border border-slate-200 rounded-xl hover:bg-slate-50 text-slate-600 transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Status Pengerjaan: {{ $unit_code }}</h1>
            </div>

            @if(!$time_start)
                @php
                    $currentAnalysis = \App\Models\MechanicAnalysis::find($analysis_id);
                @endphp

                @if($currentAnalysis->is_approved)
                    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl p-10 text-center">
                        <div class="w-24 h-24 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-6 text-emerald-500">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-slate-900">Analisis Disetujui!</h2>
                        <p class="text-slate-500 mt-2 mb-8">Silakan mulai pengerjaan sekarang.</p>
                        
                        <!-- Read-only view of approved plan -->
                        <div class="mb-8 text-left bg-slate-50 p-6 rounded-2xl border border-slate-100">
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div><span class="text-xs font-bold text-slate-400 uppercase">Mekanik 1</span><div class="font-bold text-slate-700">{{ $nama_mechanic_satu }}</div></div>
                                <div><span class="text-xs font-bold text-slate-400 uppercase">Mekanik 2</span><div class="font-bold text-slate-700">{{ $nama_mechanic_dua ?: '-' }}</div></div>
                            </div>
                            <div class="mb-4"><span class="text-xs font-bold text-slate-400 uppercase">Analisis</span><div class="font-bold text-slate-700">{{ $hasil_analisis }}</div></div>
                            <div><span class="text-xs font-bold text-slate-400 uppercase">Rencana</span><div class="font-bold text-slate-700">{{ $saran_perbaikan }}</div></div>
                        </div>

                        <button 
                            wire:click="startWork"
                            class="px-10 py-5 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-2xl shadow-lg shadow-emerald-100 transition-all active:scale-95 text-lg w-full"
                        >
                            MULAI PEKERJAAN
                        </button>
                    </div>
                @else
                    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl p-8 sm:p-10 text-left">
                        <div class="flex items-center gap-4 mb-6 pb-6 border-b border-slate-100">
                            <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center text-amber-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            </div>
                            <div>
                                <h2 class="text-xl font-bold text-slate-900">Isi Detail Analisis</h2>
                                <p class="text-slate-500 text-sm">Masukan rencana perbaikan untuk disetujui Admin/Foreman.</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Mekanik 1 (PIC)</label>
                                    <input type="text" wire:model="nama_mechanic_satu" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none font-semibold">
                                    @error('nama_mechanic_satu') <span class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Mekanik 2 (Helper)</label>
                                    <input type="text" wire:model="nama_mechanic_dua" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none font-semibold">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">SMU Down (Km Awal)</label>
                                    <input type="number" wire:model="smu_down" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none font-semibold">
                                    @error('smu_down') <span class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Location Unit</label>
                                    <select wire:model="location_unit" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none font-semibold">
                                        <option value="">Pilih Lokasi...</option>
                                        <option value="Sukamulya">Sukamulya</option>
                                        <option value="Sadang">Sadang</option>
                                    </select>
                                    @error('location_unit') <span class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-span-full">
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Status Analisis</label>
                                    <select wire:model="status_analysis" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none font-semibold">
                                        <option value="Pending">Pending</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Completed">Completed</option>
                                    </select>
                                    @error('status_analysis') <span class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Hasil Analisis Kerusakan</label>
                                <textarea wire:model="hasil_analisis" rows="3" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none" placeholder="Jelaskan detail kerusakan..."></textarea>
                                @error('hasil_analisis') <span class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Rencana Perbaikan</label>
                                <textarea wire:model="saran_perbaikan" rows="3" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none" placeholder="Apa yang akan dilakukan?"></textarea>
                                @error('saran_perbaikan') <span class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                            </div>

                             <div class="col-span-full">
                                <div class="flex items-center justify-between mb-4">
                                    <label class="block text-sm font-bold text-slate-700">Spare Part yang Dibutuhkan</label>
                                    <button type="button" wire:click="addPart" class="text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-lg hover:bg-emerald-100 transition-colors">
                                        + Tambah Part
                                    </button>
                                </div>


                                <div class="space-y-3">
                                    @foreach($requested_parts as $index => $part)
                                        <div class="flex items-start gap-3 p-3 bg-slate-50 border border-slate-200 rounded-xl">
                                            <div class="flex-1">
                                                <div x-data="{ open: false }" class="relative" wire:key="report-part-{{ $index }}">
                                                    <div 
                                                        @click="open = !open; if(open) $nextTick(() => $refs.searchInput.focus())" 
                                                        class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm cursor-pointer flex justify-between items-center min-h-[38px] transition-all hover:border-emerald-300 shadow-sm"
                                                    >
                                                        <span class="truncate pr-4">
                                                            @php
                                                                $selectedPart = $inventories->firstWhere('id', $requested_parts[$index]['inventory_id']);
                                                                if (!$selectedPart && $requested_parts[$index]['inventory_id']) {
                                                                    $selectedPart = \App\Models\Inventory::find($requested_parts[$index]['inventory_id']);
                                                                }
                                                            @endphp
                                                            {{ $selectedPart ? $selectedPart->item_sparepart : 'Pilih Part...' }}
                                                        </span>
                                                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 shrink-0" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                        </svg>
                                                    </div>

                                                    <div 
                                                        x-show="open" 
                                                        @click.away="open = false"
                                                        x-transition:enter="transition ease-out duration-100"
                                                        x-transition:enter-start="transform opacity-0 scale-95"
                                                        x-transition:enter-end="transform opacity-100 scale-100"
                                                        class="absolute z-50 w-full mt-2 bg-white border border-slate-200 rounded-xl shadow-2xl overflow-hidden"
                                                        x-cloak
                                                    >
                                                        <div class="p-2 border-b border-slate-100 bg-slate-50">
                                                            <div class="relative">
                                                                <svg class="w-3.5 h-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                                </svg>
                                                                <input 
                                                                    x-ref="searchInput"
                                                                    type="text" 
                                                                    wire:model.live.debounce.300ms="part_search"
                                                                    placeholder="Ketik untuk mencari..."
                                                                    class="w-full pl-8 pr-3 py-1.5 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-100 focus:border-emerald-400 outline-none transition-all"
                                                                    @click.stop
                                                                >
                                                            </div>
                                                        </div>
                                                        <div class="max-h-64 overflow-y-auto overflow-x-hidden">
                                                            <div 
                                                                wire:click="$set('requested_parts.{{ $index }}.inventory_id', '')"
                                                                @click="open = false"
                                                                class="px-4 py-2 hover:bg-slate-50 cursor-pointer text-xs text-slate-500 italic border-b border-slate-50"
                                                            >
                                                                -- Kosongkan Pilihan --
                                                            </div>
                                                            @forelse($inventories as $inv)
                                                                <div 
                                                                    wire:click="$set('requested_parts.{{ $index }}.inventory_id', '{{ $inv->id }}')"
                                                                    @click="open = false"
                                                                    class="px-4 py-2.5 hover:bg-emerald-50 cursor-pointer text-sm flex flex-col gap-0.5 border-b border-slate-50 last:border-0 transition-colors"
                                                                >
                                                                    <div class="flex justify-between items-start gap-2">
                                                                        <span class="font-semibold text-slate-700 leading-tight">{{ $inv->item_sparepart }}</span>
                                                                        <span class="shrink-0 text-[10px] font-bold {{ $inv->ends_balance > 5 ? 'text-emerald-600 bg-emerald-50' : 'text-rose-600 bg-rose-50' }} px-1.5 py-0.5 rounded-full border border-current opacity-70">
                                                                            {{ (int)$inv->ends_balance }}
                                                                        </span>
                                                                    </div>
                                                                    @if($inv->part_number)
                                                                        <span class="text-[10px] text-slate-400 font-mono">{{ $inv->part_number }}</span>
                                                                    @endif
                                                                </div>
                                                            @empty
                                                                <div class="px-4 py-10 text-center text-slate-400 text-xs">
                                                                    <svg class="w-8 h-8 mx-auto mb-2 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                                    </svg>
                                                                    Tidak ada part ditemukan
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="w-20">
                                                <input type="number" wire:model="requested_parts.{{ $index }}.quantity" class="block w-full px-3 py-2 bg-white border border-slate-200 rounded-lg text-sm" placeholder="Qty">
                                            </div>
                                            <button type="button" wire:click="removePart({{ $index }})" class="p-2 text-rose-500 hover:bg-rose-50 rounded-lg">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-slate-100">
                            <button 
                                wire:click="submitAnalysisForApproval"
                                class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-2xl shadow-lg shadow-emerald-100 transition-all active:scale-[0.98]"
                            >
                                KIRIM PENGAJUAN ANALISIS
                            </button>
                            <p class="text-center text-xs text-slate-400 mt-4">Data akan dikirim ke Admin untuk verifikasi ketersediaan part.</p>
                        </div>
                    </div>
                @endif
            @else
                <div class="space-y-6"
                    x-data="{
                        timeStart: '{{ $time_start }}',
                        isPaused: {{ $is_paused ? 'true' : 'false' }},
                        accumulatedSeconds: {{ $accumulated_seconds ?? 0 }},
                        pausedAt: '{{ $paused_at ?? '' }}',
                        resumedAt: '{{ $resumed_at ?? '' }}',
                        displayTime: '00:00:00',
                        showPauseModal: false,
                        init() {
                            this.tick();
                            setInterval(() => this.tick(), 1000);
                        },
                        tick() {
                            let totalSeconds = this.accumulatedSeconds;
                            if (!this.isPaused) {
                                let ref = this.resumedAt || this.timeStart;
                                if (ref) {
                                    let now = new Date();
                                    let refDate;
                                    if (ref.length <= 8) {
                                        // time-only string like '14:30:00'
                                        let today = now.toISOString().split('T')[0];
                                        refDate = new Date(today + 'T' + ref);
                                    } else {
                                        refDate = new Date(ref);
                                    }
                                    let elapsed = Math.floor((now - refDate) / 1000);
                                    if (elapsed < 0) elapsed = 0;
                                    totalSeconds += elapsed;
                                }
                            }
                            let h = String(Math.floor(totalSeconds / 3600)).padStart(2, '0');
                            let m = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, '0');
                            let s = String(totalSeconds % 60).padStart(2, '0');
                            this.displayTime = h + ':' + m + ':' + s;
                        }
                    }"
                >
                    <!-- Real-time Counter -->
                    <div class="rounded-[2rem] p-10 text-white shadow-2xl relative overflow-hidden"
                        :class="isPaused ? 'bg-amber-800' : 'bg-emerald-900'"
                    >
                        <div class="relative z-10 text-center">
                            <span class="text-xs font-bold uppercase tracking-[0.2em]"
                                :class="isPaused ? 'text-amber-300' : 'text-emerald-300'"
                            >
                                <template x-if="isPaused">
                                    <span>⏸ PEKERJAAN DI-PAUSE</span>
                                </template>
                                <template x-if="!isPaused">
                                    <span>Waktu Pengerjaan Berjalan</span>
                                </template>
                            </span>
                            <div class="text-6xl font-black mt-4 font-mono tracking-tighter" x-text="displayTime"></div>
                            <div class="mt-4 flex items-center justify-center gap-2"
                                :class="isPaused ? 'text-amber-300' : 'text-emerald-300'"
                            >
                                <span class="w-2 h-2 rounded-full"
                                    :class="isPaused ? 'bg-amber-400' : 'bg-rose-500 animate-ping'"
                                ></span>
                                <span class="text-sm font-bold">Mulai pukul: {{ $time_start }}</span>
                            </div>

                            @if($is_paused && $pause_reason)
                            <div class="mt-4 bg-amber-900/50 border border-amber-700/50 rounded-xl p-3">
                                <span class="text-xs font-bold text-amber-200 uppercase tracking-wider">Alasan Pause:</span>
                                <p class="text-amber-100 text-sm mt-1">{{ $pause_reason }}</p>
                            </div>
                            @endif
                        </div>
                        <div class="absolute top-0 right-0 p-4 opacity-10">
                            <svg class="w-40 h-40" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                    </div>

                    <!-- Pause / Resume Controls -->
                    @if(!$is_paused)
                    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl p-6 sm:p-8">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center text-amber-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900">Pause Pekerjaan</h3>
                                <p class="text-xs text-slate-500">Pending untuk dilanjutkan besok? Tulis alasannya.</p>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <textarea 
                                wire:model="pause_reason" 
                                rows="2" 
                                class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-amber-50 focus:border-amber-500 outline-none transition-all text-sm" 
                                placeholder="Contoh: Menunggu sparepart datang, dilanjut besok pagi..."
                            ></textarea>
                            @error('pause_reason') <span class="text-rose-500 text-xs font-bold">{{ $message }}</span> @enderror
                            <button 
                                wire:click="pauseWork"
                                class="w-full py-3 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl shadow-lg shadow-amber-100 transition-all active:scale-[0.98] flex items-center justify-center gap-2"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                PAUSE PEKERJAAN
                            </button>
                        </div>
                    </div>
                    @else
                    <div class="bg-white rounded-[2rem] border border-amber-200 shadow-xl p-6 sm:p-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900">Pekerjaan Sedang Di-pause</h3>
                                <p class="text-xs text-slate-500">Lanjutkan pekerjaan atau selesaikan dengan mengisi form di bawah.</p>
                            </div>
                        </div>
                        <button 
                            wire:click="resumeWork"
                            class="w-full py-4 bg-emerald-600 hover:bg-emerald-700 text-white font-black rounded-xl shadow-lg shadow-emerald-100 transition-all active:scale-[0.98] flex items-center justify-center gap-2 text-lg"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            LANJUTKAN PEKERJAAN
                        </button>
                    </div>
                    @endif

                    <!-- Finish Form -->
                    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-xl p-8 sm:p-10">
                        <div class="flex items-center gap-3 mb-6 pb-6 border-b border-slate-100">
                            <div class="w-10 h-10 bg-rose-50 rounded-xl flex items-center justify-center text-rose-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900">Selesaikan Pekerjaan</h3>
                                <p class="text-xs text-slate-500">Isi detail dan klik tombol selesai.</p>
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Apa yang sudah dikerjakan?</label>
                                <textarea wire:model="activity" rows="4" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition-all" placeholder="Detail aktivitas perbaikan..."></textarea>
                                @error('activity') <span class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">SMU Km Akhir (Ready)</label>
                                <input type="number" wire:model="smu_ready" class="block w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition-all" placeholder="KM Unit saat selesai...">
                                @error('smu_ready') <span class="text-rose-500 text-xs mt-1 font-bold">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Foto Dokumentasi</label>
                                <input type="file" wire:model="documentation" multiple class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                                
                                @if($documentation)
                                    <div class="mt-4 grid grid-cols-4 gap-2">
                                        @foreach($documentation as $photo)
                                            <img src="{{ $photo->temporaryUrl() }}" class="w-full h-16 object-cover rounded-lg border">
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <button 
                                wire:click="saveReport"
                                type="button"
                                class="w-full py-5 bg-slate-900 hover:bg-rose-600 text-white font-black rounded-2xl shadow-xl transition-all active:scale-[0.98] text-lg uppercase tracking-wider"
                            >
                                SELESAIKAN PEKERJAAN
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
