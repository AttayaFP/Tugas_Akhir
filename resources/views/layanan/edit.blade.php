@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div class="flex items-center gap-4">
            <a href="{{ route('layanan.index') }}" class="p-3 glass rounded-2xl text-slate-400 hover:text-[var(--theme-primary)] transition-all">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Edit Layanan</h1>
                <p class="text-sm font-medium text-slate-500 mt-1">Perbarui detail jasa cetak: <strong>{{ $l->nama_layanan }}</strong></p>
            </div>
        </div>
    </div>

    <div class="max-w-4xl">
        <form id="main-form" action="{{ route('layanan.update', $l->id) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            @csrf
            @method('PUT')
            
            <!-- Left: Photo Upload -->
            <div class="lg:col-span-4">
                <div class="glass rounded-[2.5rem] p-8 border border-white/60 shadow-2xl shadow-slate-200/40 sticky top-28">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Foto Layanan</label>
                    
                    <div class="relative group cursor-pointer">
                        <div id="image-preview" class="w-full aspect-square rounded-[2rem] bg-slate-50 border-2 border-slate-200 flex flex-col items-center justify-center text-slate-300 overflow-hidden transition-all group-hover:border-[var(--theme-primary)] group-hover:bg-white">
                            @if($l->foto)
                                <img src="{{ Storage::url($l->foto) }}" class="w-full h-full object-cover">
                            @else
                                <i data-lucide="image-plus" class="w-12 h-12 mb-3"></i>
                                <span class="text-[10px] font-bold">KLIK UNTUK UBAH</span>
                            @endif
                        </div>
                        <input type="file" name="foto" id="foto-input" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer" onchange="previewImage(this)">
                    </div>
                    
                    <p class="text-[10px] font-medium text-slate-400 mt-6 leading-relaxed">
                        <i data-lucide="info" class="w-3 h-3 inline mr-1"></i> 
                        Unggah foto baru untuk mengganti foto lama. Biarkan kosong jika tidak ingin mengubah.
                    </p>
                </div>
            </div>

            <!-- Right: Details -->
            <div class="lg:col-span-8 space-y-6">
                <div class="glass rounded-[2.5rem] p-10 border border-white/60 shadow-2xl shadow-slate-200/40">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Nama Jasa / Layanan</label>
                            <input type="text" name="nama_layanan" value="{{ old('nama_layanan', $l->nama_layanan) }}" required placeholder="Contoh: Cetak Spanduk High-Res"
                                class="w-full glass border border-white/40 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-[var(--theme-accent)] focus:border-[var(--theme-primary)] transition-all">
                            @error('nama_layanan') <p class="text-rose-500 text-[10px] mt-2 font-bold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Harga Per Satuan</label>
                            <div class="relative">
                                <span class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">Rp</span>
                                <input type="number" name="harga_per_satuan" value="{{ old('harga_per_satuan', $l->harga_per_satuan) }}" required placeholder="0"
                                    class="w-full glass border border-white/40 rounded-2xl pl-14 pr-6 py-4 text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-[var(--theme-accent)] focus:border-[var(--theme-primary)] transition-all">
                            </div>
                            @error('harga_per_satuan') <p class="text-rose-500 text-[10px] mt-2 font-bold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Satuan</label>
                            <input type="text" name="satuan" value="{{ old('satuan', $l->satuan) }}" required placeholder="Contoh: Meter, Pcs, Rim"
                                class="w-full glass border border-white/40 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-[var(--theme-accent)] focus:border-[var(--theme-primary)] transition-all">
                            @error('satuan') <p class="text-rose-500 text-[10px] mt-2 font-bold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Minimal Order</label>
                            <input type="number" name="minimal_order" value="{{ old('minimal_order', $l->minimal_order) }}" required
                                class="w-full glass border border-white/40 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-[var(--theme-accent)] focus:border-[var(--theme-primary)] transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Estimasi Waktu</label>
                            <input type="text" name="estimasi_waktu" value="{{ old('estimasi_waktu', $l->estimasi_waktu) }}" placeholder="Contoh: 1-2 Hari"
                                class="w-full glass border border-white/40 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-[var(--theme-accent)] focus:border-[var(--theme-primary)] transition-all">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Deskripsi Layanan</label>
                            <textarea name="deskripsi" rows="4" placeholder="Jelaskan detail layanan Anda..."
                                class="w-full glass border border-white/40 rounded-3xl px-6 py-4 text-sm font-medium text-slate-600 focus:outline-none focus:ring-4 focus:ring-[var(--theme-accent)] focus:border-[var(--theme-primary)] transition-all">{{ old('deskripsi', $l->deskripsi) }}</textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 mt-12 pt-8 border-t border-slate-100">
                        <a href="{{ route('layanan.index') }}" class="px-8 py-4 text-sm font-black text-slate-400 hover:text-slate-600 transition-all uppercase tracking-widest">Batal</a>
                        <button type="button"
                            onclick="window.confirmPremium({
                                title: 'Perbarui Layanan',
                                message: 'Apakah Anda yakin ingin memperbarui detail layanan cetak ini?',
                                variant: 'warning'
                            }).then(ok => { if(ok) document.getElementById('main-form').submit(); })"
                            class="px-10 py-4 text-sm font-black text-white rounded-2xl shadow-2xl shadow-indigo-200/50 hover:scale-105 active:scale-95 transition-all duration-300"
                            style="background: linear-gradient(135deg, var(--theme-primary), var(--theme-to))">
                            SIMPAN PERUBAHAN
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('image-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                    preview.classList.remove('border-dashed');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
