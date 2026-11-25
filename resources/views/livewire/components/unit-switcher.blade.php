<div>
    @if($isSuperAdmin)
        <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full border" style="background:rgba(99,102,241,0.08);border-color:rgba(99,102,241,0.2);">
            <span class="text-[11px] font-bold" style="color:var(--accent-from);">Unit:</span>
            <select
                wire:model.live="selectedUnitId"
                aria-label="Pilih Unit Sekolah"
                class="bg-transparent text-xs font-bold outline-none cursor-pointer pr-1"
                style="color:var(--text-primary);"
            >
                <option value="" class="text-slate-900">Semua Unit</option>
                @foreach($units as $unit)
                    <option value="{{ $unit->id }}" class="text-slate-900">{{ $unit->kode }} — {{ $unit->nama }}</option>
                @endforeach
            </select>
        </div>
    @elseif($lockedUnit)
        <div class="flex items-center gap-1.5 px-3 py-1 rounded-full border text-xs font-bold" style="background:rgba(16,185,129,0.1);border-color:rgba(16,185,129,0.25);color:#059669;">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
            <span>Unit {{ $lockedUnit->kode }}</span>
        </div>
    @endif
</div>
