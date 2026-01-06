@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div class="flex items-center gap-4">
            <a href="{{ route('pelanggan.index') }}" class="p-3 glass rounded-2xl text-slate-400 hover:text-[var(--theme-primary)] transition-all">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Edit Pelanggan</h1>
                <p class="text-sm font-medium text-slate-500 mt-1">Perbarui data: <strong>{{ $p->nama_lengkap }}</strong></p>
            </div>
        </div>
    </div>

    <div class="max-w-4xl">
        <form id="main-form" action="{{ route('pelanggan.update', $p->id) }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            @csrf
            @method('PUT')
            
            <!-- Left: Profile Photo -->
            <div class="lg:col-span-4">
                <div class="glass rounded-[2.5rem] p-8 border border-white/60 shadow-2xl shadow-slate-200/40 sticky top-28 text-center">
                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-6">Foto Profil</label>
                    
                    <div class="relative group mx-auto w-40 h-40">
                        <div id="image-preview" class="w-full h-full rounded-[2.5rem] bg-slate-50 border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-slate-300 overflow-hidden transition-all group-hover:border-[var(--theme-primary)] group-hover:bg-white shadow-inner">
                            @if($p->foto)
                                <img src="{{ Storage::url($p->foto) }}" class="w-full h-full object-cover">
                            @else
                                <i data-lucide="camera" class="w-10 h-10 mb-2"></i>
                                <span class="text-[8px] font-black uppercase">Click to Change</span>
                            @endif
                        </div>
                        <input type="file" name="foto" id="foto-input" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer" onchange="previewImage(this)">
                    </div>
                    
                    <div class="mt-8 space-y-4">
                        <div class="p-4 bg-slate-50/50 rounded-2xl border border-white/40">
                            <span class="text-[10px] font-black text-slate-400 block uppercase mb-1">Status Saat Ini</span>
                            @php
                                $badgeStyle = match($p->kategori_pelanggan) {
                                    'VIP' => [
                                        'class' => 'bg-amber-100 text-amber-700 border-amber-200',
                                        'icon' => 'crown'
                                    ],
                                    'Member' => [
                                        'class' => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                                        'icon' => 'star'
                                    ],
                                    default => [
                                        'class' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                        'icon' => 'user'
                                    ],
                                };
                            @endphp
                            <div id="membership-badge" class="inline-flex items-center px-4 py-1.5 rounded-full text-[10px] font-black uppercase border {{ $badgeStyle['class'] }}">
                                <i data-lucide="{{ $badgeStyle['icon'] }}" class="w-3 h-3 mr-1.5 opacity-80"></i>
                                {{ $p->kategori_pelanggan }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Details -->
            <div class="lg:col-span-8 space-y-6">
                <div class="glass rounded-[2.5rem] p-10 border border-white/60 shadow-2xl shadow-slate-200/40">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $p->nama_lengkap) }}" required
                                class="w-full glass border border-white/40 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-[var(--theme-accent)] focus:border-[var(--theme-primary)] transition-all">
                            @error('nama_lengkap') <p class="text-rose-500 text-[10px] mt-2 font-bold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Nomor Telepon / WA</label>
                            <input type="text" name="no_telepon" value="{{ old('no_telepon', $p->no_telepon) }}" required
                                class="w-full glass border border-white/40 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-[var(--theme-accent)] focus:border-[var(--theme-primary)] transition-all">
                            @error('no_telepon') <p class="text-rose-500 text-[10px] mt-2 font-bold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Email</label>
                            <input type="email" name="email" value="{{ old('email', $p->email) }}" required
                                class="w-full glass border border-white/40 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-[var(--theme-accent)] focus:border-[var(--theme-primary)] transition-all">
                            @error('email') <p class="text-rose-500 text-[10px] mt-2 font-bold">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Kode Member (Kosongkan jika Regular)</label>
                            <input type="text" name="kode_member" id="kode-member-input" value="{{ old('kode_member', $p->kode_member) }}"
                                class="w-full glass border border-white/40 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-[var(--theme-accent)] focus:border-[var(--theme-primary)] transition-all font-mono uppercase">
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Password Baru (Biarkan kosong jika tidak diganti)</label>
                            <input type="password" name="password" placeholder="••••••••"
                                class="w-full glass border border-white/40 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-[var(--theme-accent)] focus:border-[var(--theme-primary)] transition-all">
                            @error('password') <p class="text-rose-500 text-[10px] mt-2 font-bold">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Alamat Lengkap</label>
                            <textarea name="alamat" rows="3"
                                class="w-full glass border border-white/40 rounded-3xl px-6 py-4 text-sm font-medium text-slate-600 focus:outline-none focus:ring-4 focus:ring-[var(--theme-accent)] focus:border-[var(--theme-primary)] transition-all">{{ old('alamat', $p->alamat) }}</textarea>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 mt-12 pt-8 border-t border-slate-100">
                        <a href="{{ route('pelanggan.index') }}" class="px-8 py-4 text-sm font-black text-slate-400 hover:text-slate-600 transition-all uppercase tracking-widest">Batal</a>
                        <button type="button"
                            onclick="window.confirmPremium({
                                title: 'Simpan Perubahan',
                                message: 'Apakah Anda yakin ingin memperbarui data pelanggan ini?',
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

        document.getElementById('kode-member-input').addEventListener('input', function(e) {
            const val = e.target.value.toUpperCase();
            const badge = document.getElementById('membership-badge');
            
            if (val.startsWith('M')) {
                badge.innerHTML = '<i data-lucide="star" class="w-3 h-3 mr-1.5 opacity-80"></i> MEMBER';
                badge.className = 'inline-flex items-center px-4 py-1.5 rounded-full text-[10px] font-black uppercase bg-indigo-100 text-indigo-700 border border-indigo-200';
            } else if (val.startsWith('V')) {
                badge.innerHTML = '<i data-lucide="crown" class="w-3 h-3 mr-1.5 opacity-80"></i> VIP';
                badge.className = 'inline-flex items-center px-4 py-1.5 rounded-full text-[10px] font-black uppercase bg-amber-100 text-amber-700 border border-amber-200';
            } else {
                badge.innerHTML = '<i data-lucide="user" class="w-3 h-3 mr-1.5 opacity-80"></i> REGULAR';
                badge.className = 'inline-flex items-center px-4 py-1.5 rounded-full text-[10px] font-black uppercase bg-emerald-100 text-emerald-700 border border-emerald-200';
            }
            if (window.lucide) window.lucide.createIcons();
        });
    </script>
@endsection
