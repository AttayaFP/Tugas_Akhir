@forelse($pelanggan as $p)
    <tr class="group bg-white/50 border-b border-white/20 hover:bg-white/80 transition-all duration-300">
        <td class="px-6 py-5">
            <div class="flex items-center gap-4">
                <div class="relative">
                    <div class="w-12 h-12 rounded-2xl overflow-hidden ring-2 ring-white shadow-lg">
                        @if ($p->foto)
                            <img src="{{ Storage::url($p->foto) }}" class="object-cover w-full h-full" alt="foto">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200 text-slate-400">
                                <i data-lucide="user" class="w-6 h-6"></i>
                            </div>
                        @endif
                    </div>
                </div>
                <div>
                    <div class="font-black text-slate-800 tracking-tight">{{ $p->nama_lengkap }}</div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-0.5">ID: #{{ str_pad($p->id, 5, '0', STR_PAD_LEFT) }}</div>
                </div>
            </div>
        </td>
        <td class="px-6 py-5">
            <div class="flex flex-col gap-1.5">
                <div class="flex items-center gap-2 text-slate-600 font-bold text-xs">
                    <i data-lucide="phone" class="w-3.5 h-3.5 text-slate-400"></i>
                    {{ $p->no_telepon }}
                </div>
                <div class="flex items-center gap-2 text-slate-400 font-medium text-[11px]">
                    <i data-lucide="mail" class="w-3.5 h-3.5 opacity-50"></i>
                    {{ $p->email }}
                </div>
            </div>
        </td>
        <td class="px-6 py-5">
            @php
                $color = match($p->kategori_pelanggan) {
                    'VIP' => [
                        'bg' => 'bg-amber-100', 
                        'text' => 'text-amber-700', 
                        'border' => 'border-amber-200',
                        'icon' => 'crown'
                    ],
                    'Member' => [
                        'bg' => 'bg-indigo-100', 
                        'text' => 'text-indigo-700', 
                        'border' => 'border-indigo-200',
                        'icon' => 'star'
                    ],
                    default => [
                        'bg' => 'bg-emerald-100', 
                        'text' => 'text-emerald-700', 
                        'border' => 'border-emerald-200',
                        'icon' => 'user'
                    ],
                };
            @endphp
            <div class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter {{ $color['bg'] }} {{ $color['text'] }} border {{ $color['border'] }} shadow-sm">
                <i data-lucide="{{ $color['icon'] }}" class="w-3 h-3 mr-1.5 opacity-80"></i>
                {{ $p->kategori_pelanggan }}
            </div>
            <div class="text-[10px] font-mono text-slate-400 mt-1.5 ml-1">{{ $p->kode_member ?? '-' }}</div>
        </td>
        <td class="px-6 py-5 text-right">
            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all transform translate-x-4 group-hover:translate-x-0">
                <a href="{{ route('pelanggan.edit', $p->id) }}" class="p-2.5 glass text-slate-400 hover:text-[var(--theme-primary)] rounded-xl hover:scale-110 active:scale-90 transition-all">
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                </a>
                <button type="button" 
                    onclick="window.confirmPremium({
                        title: 'Hapus Pelanggan',
                        message: 'Apakah Anda yakin ingin menghapus data {{ $p->nama_lengkap }}? Tindakan ini tidak dapat dibatalkan.',
                        variant: 'danger'
                    }).then(ok => { if(ok) document.getElementById('delete-form-{{ $p->id }}').submit(); })"
                    class="p-2.5 glass text-rose-400 hover:text-rose-600 rounded-xl hover:scale-110 active:scale-90 transition-all">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
                <form id="delete-form-{{ $p->id }}" action="{{ route('pelanggan.destroy', $p->id) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="px-6 py-20 text-center">
            <div class="flex flex-col items-center justify-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                    <i data-lucide="users" class="w-10 h-10 text-slate-200"></i>
                </div>
                <h4 class="text-xl font-black text-slate-800">Tidak ada pelanggan</h4>
                <p class="text-sm font-medium text-slate-400 mt-1">Coba cari dengan kata kunci lain atau tambah pelanggan baru.</p>
            </div>
        </td>
    </tr>
@endforelse
