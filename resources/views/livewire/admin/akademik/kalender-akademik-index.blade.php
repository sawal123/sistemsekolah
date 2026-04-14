<div>
    <div class="flex flex-col md:flex-row justify-between md:items-end gap-4 mb-6">
        <div>
            <h1 class="txt-primary text-[22px] font-extrabold flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"></path></svg>
                Kalender Akademik
            </h1>
            <p class="txt-muted text-[13px] mt-1">Kelola hari libur nasional, libur sekolah, dan acara operasional lainnya.</p>
        </div>
        <div class="flex items-center gap-3">
            <x-ui.button wire:click="syncNationalHolidays" variant="secondary" wire:loading.attr="disabled">
                <div wire:loading wire:target="syncNationalHolidays" class="mr-2">
                    <svg class="animate-spin h-4 w-4 text-indigo-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <svg wire:loading.remove wire:target="syncNationalHolidays" class="w-4 h-4 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Sinkronisasi API
            </x-ui.button>
            <x-ui.button wire:click="openAddModal" variant="primary">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Hari Libur
            </x-ui.button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
        {{-- Sisi Kiri: Visual Calendar --}}
        <div class="lg:col-span-2 space-y-6">
            <x-ui.card class="!p-0 overflow-visible relative z-10 border border-indigo-500/10">
                <div class="p-5 flex items-center justify-between border-b border-gray-100 dark:border-white/5">
                    <h3 class="txt-primary font-bold text-lg flex items-center gap-2">
                        <span class="w-2 h-6 bg-indigo-500 rounded-full"></span>
                        {{ $monthName }}
                    </h3>
                    <div class="flex items-center gap-4">
                        <div class="hidden md:flex items-center gap-1.5 px-3 py-1.5 bg-red-500/10 rounded-lg border border-red-500/20">
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2-2v12a2 2 0 002 2z"></path></svg>
                            <span class="text-xs font-bold text-red-600 dark:text-red-400">{{ $totalLiburBulanIni }} Hari Libur</span>
                        </div>
                        <div class="flex items-center gap-1">
                            <button wire:click="prevMonth" class="p-2 hover:bg-gray-100 dark:hover:bg-white/5 rounded-xl transition-all">
                                <svg class="w-5 h-5 txt-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                            </button>
                            <button wire:click="nextMonth" class="p-2 hover:bg-gray-100 dark:hover:bg-white/5 rounded-xl transition-all">
                                <svg class="w-5 h-5 txt-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="p-4">
                    <div class="grid grid-cols-7 mb-2">
                        @foreach(['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $day)
                            <div class="text-center py-2 text-[10px] font-bold uppercase tracking-widest {{ $loop->last ? 'text-red-500' : 'txt-muted' }}">{{ $day }}</div>
                        @endforeach
                    </div>

                    <div class="grid grid-cols-7 gap-1 md:gap-2">
                        {{-- Empty days at start --}}
                        @php 
                            $startDay = $firstDayOfMonth->dayOfWeekIso; // 1 (Mon) to 7 (Sun)
                        @endphp
                        
                        @for($i = 1; $i < $startDay; $i++)
                            <div class="aspect-square rounded-xl bg-gray-50/30 dark:bg-white/[0.01] border border-transparent"></div>
                        @endfor

                        {{-- Days in month --}}
                        @for($d = 1; $d <= $daysInMonth; $d++)
                            @php 
                                $dateObj = Carbon\Carbon::create($viewYear, $viewMonth, $d);
                                $dateStr = $dateObj->format('Y-m-d');
                                $isSunday = $dateObj->isSunday();
                                $isToday = $dateStr === now()->format('Y-m-d');
                                $event = $monthlyEvents[$dateStr] ?? null;
                                
                                $bgClass = 'bg-white dark:bg-[#0c0e1a]/40';
                                $borderClass = 'border-gray-200 dark:border-white/5';
                                $textClass = $isSunday ? 'text-red-600 dark:text-red-400' : 'txt-primary';
                                
                                if($event) {
                                    if($event->jenis_libur === 'nasional') {
                                        $bgClass = 'bg-red-50 dark:bg-red-500/10';
                                        $borderClass = 'border-red-200 dark:border-red-500/20';
                                        $textClass = 'text-red-600 dark:text-red-400';
                                    } elseif($event->jenis_libur === 'sekolah') {
                                        $bgClass = 'bg-amber-50 dark:bg-amber-500/10';
                                        $borderClass = 'border-amber-200 dark:border-amber-500/20';
                                    } else {
                                        $bgClass = 'bg-indigo-50 dark:bg-indigo-500/10';
                                        $borderClass = 'border-indigo-200 dark:border-indigo-500/20';
                                    }
                                }

                                if($isToday) {
                                    $borderClass = 'border-indigo-500 ring-2 ring-indigo-500/20';
                                }
                            @endphp

                            <button wire:click="selectDate('{{ $dateStr }}')" 
                                class="group aspect-square rounded-xl text-left p-1.5 md:p-2 border transition-all hover:shadow-lg hover:scale-[1.02] flex flex-col justify-between {{ $bgClass }} {{ $borderClass }}">
                                <span class="text-xs md:text-sm font-extrabold {{ $textClass }}">{{ $d }}</span>
                                
                                @if($event)
                                    <div class="w-full flex justify-end">
                                        <div class="w-1.5 h-1.5 md:w-2 md:h-2 rounded-full @if($event->jenis_libur === 'nasional') bg-red-500 @elseif($event->jenis_libur === 'sekolah') bg-amber-500 @else bg-indigo-500 @endif shadow-sm"></div>
                                    </div>
                                    <div class="hidden md:block truncate text-[9px] font-bold mt-1 opacity-80 txt-primary leading-tight">
                                        {{ $event->keterangan }}
                                    </div>
                                @endif
                            </button>
                        @endfor
                    </div>
                </div>
                
                <div class="p-4 bg-gray-50/50 dark:bg-black/20 flex flex-wrap gap-4 border-t border-gray-100 dark:border-white/5">
                    <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-red-500"></span> <span class="text-[11px] font-bold txt-muted">Libur Nasional</span></div>
                    <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-amber-500"></span> <span class="text-[11px] font-bold txt-muted">Libur Sekolah</span></div>
                    <div class="flex items-center gap-2"><span class="w-2 h-2 rounded-full bg-indigo-500"></span> <span class="text-[11px] font-bold txt-muted">Event / Darurat</span></div>
                </div>
            </x-ui.card>
        </div>

        {{-- Sisi Kanan: Filter & Ringkasan Tabel --}}
        <div class="space-y-6">
            <x-ui.card class="border border-indigo-500/10 overflow-visible relative z-20">
                <div class="space-y-4">
                    <div class="relative z-30">
                        <x-ui.label value="Status / Jenis" />
                        <x-ui.select wire:model.live="filterJenis" :options="['' => 'Semua Jenis', 'nasional' => 'Libur Nasional', 'sekolah' => 'Libur Sekolah', 'darurat' => 'Darurat']" />
                    </div>
                    <div>
                        <x-ui.label value="Cari Keterangan" />
                        <div class="relative">
                            <input wire:model.live="search" type="text" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-[#0c0e1a]/50 txt-primary text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Ketik nama event...">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>
                </div>
            </x-ui.card>

            <div class="glass-card shadow-2xl rounded-2xl overflow-hidden border border-white/20 dark:border-white/10 relative z-0">
                <div class="p-4 border-b border-gray-100 dark:border-white/5 bg-gray-50/50 dark:bg-black/10">
                    <h4 class="text-xs font-black uppercase tracking-widest txt-secondary">Ringkasan Daftar</h4>
                </div>
                <div class="overflow-x-auto w-full custom-scrollbar">
                    <table class="w-full text-left border-collapse bg-white/50 dark:bg-[#0c0e1a]/50">
                        <tbody class="divide-y divide-gray-200 dark:divide-white/5 text-[11px]">
                            @forelse($events as $event)
                                <tr class="hover:bg-indigo-50/30 dark:hover:bg-white/[0.02] transition-colors">
                                    <td class="py-3 px-4">
                                        <p class="txt-primary font-bold">{{ $event->tanggal->translatedFormat('d M Y') }}</p>
                                        <p class="txt-muted leading-tight mt-0.5 line-clamp-1 truncate w-40">{{ $event->keterangan }}</p>
                                    </td>
                                    <td class="py-3 px-4 text-right">
                                        <div class="flex items-center justify-end gap-1">
                                            <button wire:click="openEditModal({{ $event->id }})" class="p-1 px-2 text-indigo-500 bg-indigo-500/10 rounded-lg hover:bg-indigo-500/20 transition">Edit</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="py-12 text-center txt-muted italic">Tidak ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($events->hasPages())
                    <div class="px-4 py-2 border-t border-gray-200 dark:border-white/5 bg-gray-50/50 dark:bg-black/20">
                        {{ $events->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Modal Form --}}
    <x-ui.modal name="calendar-modal" maxWidth="lg">
        <form wire:submit="save" class="p-6">
            <h3 class="text-xl font-bold txt-primary mb-1 border-b border-gray-200 dark:border-white/10 pb-4">
                {{ $editId ? 'Edit Data Kalender' : 'Tambah Hari Libur / Event' }}
            </h3>

            <div class="space-y-4 mt-4 text-left">
                <div>
                    <x-ui.label value="Tanggal" />
                    <input type="date" wire:model="tanggal" class="w-full rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-[#0c0e1a]/50 p-2.5 txt-primary text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <x-ui.input-error for="tanggal" />
                </div>

                <div>
                    <x-ui.label value="Keterangan / Nama Event" />
                    <input type="text" wire:model="keterangan" class="w-full rounded-xl border border-gray-300 dark:border-white/10 bg-white dark:bg-[#0c0e1a]/50 p-2.5 txt-primary text-sm shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" placeholder="Contoh: Libur Hari Raya...">
                    <x-ui.input-error for="keterangan" />
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <x-ui.label value="Jenis" />
                        <x-ui.select wire:model="jenis_libur" :options="['nasional' => 'Nasional', 'sekolah' => 'Sekolah', 'darurat' => 'Darurat']" />
                    </div>
                    <div>
                        <x-ui.label value="Status Libur" />
                        <label class="relative inline-flex items-center cursor-pointer mt-2">
                            <input type="checkbox" wire:model="is_libur" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 dark:peer-focus:ring-indigo-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-indigo-600"></div>
                            <span class="ml-3 text-sm font-medium txt-primary">{{ $is_libur ? 'Sekolah Libur' : 'Tetap Masuk' }}</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 mt-6 border-t border-gray-200 dark:border-white/10">
                <x-ui.button type="button" variant="secondary" x-on:click="$dispatch('close-modal', 'calendar-modal')">Batal</x-ui.button>
                <x-ui.button type="submit" variant="primary">Simpan Data</x-ui.button>
            </div>
        </form>
    </x-ui.modal>
</div>
