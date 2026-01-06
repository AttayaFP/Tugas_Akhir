@forelse($layanan as $l)
    <div class="group h-full">
        <div class="h-full glass rounded-[2.5rem] border border-white/60 shadow-2xl shadow-slate-200/40 overflow-hidden flex flex-col transition-all duration-500 hover:-translate-y-2 hover:shadow-var">
            <style>
                .hover-shadow-var:hover {
                    box-shadow: 0 25px 50px -12px var(--theme-primary-alpha);
                }
            </style>
            
            <div class="h-56 relative overflow-hidden bg-slate-100">
                @if ($l->foto)
                    <img src="{{ Storage::url($l->foto) }}"
                        class="object-cover w-full h-full transition-transform duration-700 group-hover:scale-110"
                        alt="{{ $l->nama_layanan }}">
                @else
                    <div class="w-full h-full flex items-center justify-center text-slate-300">
                        <i data-lucide="image" class="w-16 h-16 opacity-20"></i>
                    </div>
                @endif
                <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                
                <div class="absolute top-4 right-4 flex gap-2">
                     <a href="{{ route('layanan.edit', $l->id) }}" class="p-2.5 glass-dark text-white rounded-xl hover:scale-110 active:scale-90 transition-all shadow-xl">
                        <i data-lucide="edit-3" class="w-4 h-4"></i>
                    </a>
                    <button type="button" 
                        onclick="window.confirmPremium({
                            title: 'Hapus Layanan',
                            message: 'Apakah Anda yakin ingin menghapus layanan {{ $l->nama_layanan }}? Tindakan ini tidak dapat dibatalkan.',
                            variant: 'danger'
                        }).then(ok => { if(ok) document.getElementById('delete-form-{{ $l->id }}').submit(); })"
                        class="p-2.5 bg-rose-500/80 backdrop-blur-md text-white rounded-xl hover:bg-rose-600 hover:scale-110 active:scale-90 transition-all shadow-xl">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                    <form id="delete-form-{{ $l->id }}" action="{{ route('layanan.destroy', $l->id) }}" method="POST" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </div>

            <div class="p-8 flex-1 flex flex-col">
                <div class="flex justify-between items-start mb-3">
                    <h3 class="text-xl font-black text-slate-800 leading-tight">{{ $l->nama_layanan }}</h3>
                </div>
                
                <p class="text-sm font-medium text-slate-500 mb-6 line-clamp-2 leading-relaxed h-10">{{ $l->deskripsi }}</p>

                <div class="mt-auto">
                    <div class="flex items-center gap-4 mb-5 p-3 rounded-2xl bg-slate-50/50 border border-slate-100">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: var(--theme-accent)">
                            <i data-lucide="tag" class="w-5 h-5 text-[var(--theme-primary)]"></i>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-black text-slate-400 tracking-widest block">Harga / Satuan</span>
                            <span class="text-lg font-black text-slate-800">
                                Rp {{ number_format($l->harga_per_satuan, 0, ',', '.') }}
                                <span class="text-xs font-medium text-slate-400">/ {{ $l->satuan }}</span>
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3 text-xs font-bold text-slate-400">
                        <span class="flex items-center">
                            <i data-lucide="shopping-cart" class="w-3.5 h-3.5 mr-1.5"></i>
                            Min: {{ $l->minimal_order }}
                        </span>
                        <span class="w-1.5 h-1.5 rounded-full bg-slate-200"></span>
                        <span class="flex items-center">
                            <i data-lucide="clock" class="w-3.5 h-3.5 mr-1.5"></i>
                            {{ $l->estimasi_waktu ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="col-span-full py-24 glass rounded-[3rem] border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-center">
        <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mb-6">
            <i data-lucide="package-search" class="w-12 h-12 text-slate-300"></i>
        </div>
        <h3 class="text-2xl font-black text-slate-800">Layanan tidak ditemukan</h3>
        <p class="text-sm font-medium text-slate-500 mt-2">Coba sesuaikan kata kunci pencarian Anda.</p>
    </div>
@endforelse
