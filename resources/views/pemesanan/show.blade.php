@extends('layouts.app')

@section('content')
    <div class="mb-10">
        <a href="{{ route('pemesanan.index') }}" class="inline-flex items-center px-4 py-2 bg-white/50 hover:bg-white text-slate-500 hover:text-pink-600 transition-all rounded-xl border border-white/40 mb-4 group shadow-sm">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2 group-hover:-translate-x-1 transition-transform"></i>
            <span class="text-xs font-black uppercase tracking-widest">Kembali ke Riwayat</span>
        </a>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight">Detail Pesanan <span class="text-pink-500">#{{ $p->no_nota }}</span></h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-8">
            <div class="glass rounded-[2rem] shadow-2xl shadow-slate-200/50 border border-white/60 overflow-hidden">
                <div class="px-8 py-6 border-b border-white/30 flex justify-between items-center bg-white/30">
                    <h2 class="font-black text-slate-800 flex items-center tracking-tight">
                        <i data-lucide="shopping-bag" class="w-5 h-5 mr-3 text-pink-500"></i>
                        Informasi Layanan
                    </h2>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest bg-slate-100 px-3 py-1 rounded-full">Dipesan pada {{ $p->tanggal_pesan }}</span>
                </div>
                <div class="p-8">
                    <div class="space-y-6">
                        @foreach($orderItems as $item)
                            <div class="flex justify-between items-center p-4 bg-slate-50/50 rounded-2xl border border-slate-100 transition-hover hover:bg-white hover:shadow-md transition-all">
                                <div>
                                    <label class="text-[9px] uppercase font-bold text-slate-400 tracking-[0.2em] block mb-1">Layanan #{{ $loop->iteration }}</label>
                                    <p class="text-base font-black text-slate-800 tracking-tight">{{ $item->layanan->nama_layanan ?? 'Unknown' }}</p>
                                    <p class="text-[10px] font-bold text-slate-400">Harga Satuan: Rp {{ number_format($item->layanan->harga_per_satuan ?? 0, 0, ',', '.') }}</p>
                                </div>
                                <div class="text-right">
                                    <label class="text-[9px] uppercase font-bold text-slate-400 tracking-[0.2em] block mb-1">Jumlah</label>
                                    <p class="text-base font-black text-slate-800 tracking-tight">{{ $item->jumlah }} <span class="text-[10px] font-medium text-slate-400">Pcs</span></p>
                                    <p class="text-[10px] font-bold text-slate-800">Subtotal: Rp {{ number_format(($item->layanan->harga_per_satuan ?? 0) * $item->jumlah, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8 grid grid-cols-2 gap-6">
                        <div class="bg-indigo-50/50 rounded-2xl p-6 border border-indigo-100/50">
                            <label class="text-[10px] uppercase font-bold text-indigo-400 tracking-[0.2em] block mb-3">Catatan Khusus</label>
                            <p class="text-sm text-slate-700 leading-relaxed font-medium italic">
                                {{ $p->keterangan ?: 'Tidak ada catatan khusus.' }}
                            </p>
                        </div>
                        <div class="bg-blue-50/50 rounded-2xl p-6 border border-blue-100/50">
                            <label class="text-[10px] uppercase font-bold text-blue-400 tracking-[0.2em] block mb-3">Metode Pembayaran</label>
                            <div class="flex items-center">
                                <i data-lucide="credit-card" class="w-4 h-4 mr-3 text-blue-500"></i>
                                <p class="text-sm text-slate-700 font-black uppercase tracking-tight">
                                    {{ $p->metode_pembayaran ?: 'Belum ditentukan' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="glass rounded-[2rem] shadow-2xl shadow-slate-200/50 border border-white/60 overflow-hidden">
                <div class="px-8 py-6 border-b border-white/30 bg-white/30">
                    <h2 class="font-black text-slate-800 flex items-center tracking-tight">
                        <i data-lucide="image" class="w-5 h-5 mr-3 text-blue-500"></i>
                        Lampiran Desain / Bukti
                    </h2>
                </div>
                <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="text-[10px] uppercase font-bold text-slate-400 tracking-[0.2em] block mb-4 text-center">Bukti Pembayaran</label>
                        @if ($p->bukti_pembayaran)
                            <div class="relative group block">
                                <img src="{{ asset('storage/' . $p->bukti_pembayaran) }}" alt="Bukti Pembayaran"
                                    class="relative w-full rounded-[1.5rem] shadow-xl border-4 border-white transform group-hover:scale-[1.02] transition-transform duration-500 min-h-[200px] object-cover">
                                <a href="{{ asset('storage/' . $p->bukti_pembayaran) }}" target="_blank"
                                    class="mt-4 w-full flex items-center justify-center px-6 py-2 bg-slate-100 text-slate-600 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-slate-200 transition-all">
                                    <i data-lucide="external-link" class="w-3 h-3 mr-2"></i> Lihat Full View
                                </a>
                            </div>
                        @else
                            <div class="py-12 bg-slate-50/50 rounded-[2rem] border-4 border-dashed border-slate-200/50 flex flex-col items-center justify-center">
                                <i data-lucide="image-off" class="w-10 h-10 mb-2 text-slate-200"></i>
                                <p class="text-slate-400 font-bold uppercase tracking-widest text-[8px]">Tidak ada bukti</p>
                            </div>
                        @endif
                    </div>
                    <div>
                        <label class="text-[10px] uppercase font-bold text-slate-400 tracking-[0.2em] block mb-4 text-center">Foto Desain</label>
                        @if ($p->foto_desain)
                            <div class="relative group block">
                                <img src="{{ asset('storage/' . $p->foto_desain) }}" alt="Foto Desain"
                                    class="relative w-full rounded-[1.5rem] shadow-xl border-4 border-white transform group-hover:scale-[1.02] transition-transform duration-500 min-h-[200px] object-cover">
                                <a href="{{ asset('storage/' . $p->foto_desain) }}" target="_blank"
                                    class="mt-4 w-full flex items-center justify-center px-6 py-2 bg-slate-100 text-slate-600 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-slate-200 transition-all">
                                    <i data-lucide="external-link" class="w-3 h-3 mr-2"></i> Lihat Full View
                                </a>
                            </div>
                        @else
                            <div class="py-12 bg-slate-50/50 rounded-[2rem] border-4 border-dashed border-slate-200/50 flex flex-col items-center justify-center">
                                <i data-lucide="camera-off" class="w-10 h-10 mb-2 text-slate-200"></i>
                                <p class="text-slate-400 font-bold uppercase tracking-widest text-[8px]">Tidak ada desain</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="space-y-8">
            <div class="glass rounded-[2rem] shadow-2xl shadow-slate-200/50 border border-white/60 overflow-hidden">
                <div class="p-8">
                    <div class="mb-10">
                        <label class="text-[10px] uppercase font-bold text-slate-400 tracking-[0.2em] block mb-4">Kemajuan Pesanan</label>
                        <form action="{{ route('pemesanan.toggle-status', $p->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full group">
                                @if ($p->status_pesanan == 'Selesai')
                                    <div class="p-5 bg-gradient-to-br from-emerald-400 to-teal-500 text-white rounded-2xl font-black flex items-center justify-center shadow-lg shadow-emerald-200 group-hover:scale-[1.02] active:scale-95 transition-all">
                                        <i data-lucide="check-circle" class="w-6 h-6 mr-3"></i> SELESAI
                                    </div>
                                @elseif($p->status_pesanan == 'Proses')
                                    <div class="p-5 bg-gradient-to-br from-blue-400 to-indigo-500 text-white rounded-2xl font-black flex items-center justify-center shadow-lg shadow-blue-200 group-hover:scale-[1.02] active:scale-95 transition-all">
                                        <i data-lucide="refresh-cw" class="w-6 h-6 mr-3 animate-spin-slow"></i> PROSES
                                    </div>
                                @elseif($p->status_pesanan == 'Dikirim')
                                    <div class="p-5 bg-gradient-to-br from-purple-400 to-fuchsia-500 text-white rounded-2xl font-black flex items-center justify-center shadow-lg shadow-purple-200 group-hover:scale-[1.02] active:scale-95 transition-all">
                                        <i data-lucide="truck" class="w-6 h-6 mr-3"></i> DIKIRIM
                                    </div>
                                @else
                                    <div class="p-5 bg-gradient-to-br from-slate-400 to-slate-600 text-white rounded-2xl font-black flex items-center justify-center shadow-lg shadow-slate-200 group-hover:scale-[1.02] active:scale-95 transition-all">
                                        <i data-lucide="clock" class="w-6 h-6 mr-3"></i> PENDING
                                    </div>
                                @endif
                            </button>
                        </form>
                        <p class="text-[9px] text-slate-400 mt-4 text-center font-bold tracking-widest uppercase">Klik untuk menukar status</p>
                    </div>

                    <div class="pt-8 border-t border-white/30">
                        <label class="text-[10px] uppercase font-bold text-slate-400 tracking-[0.2em] block mb-4">Status Finansial</label>
                        <div class="flex items-center justify-between mb-6">
                            @if ($p->status_pembayaran == 'Lunas')
                                <span class="px-4 py-1.5 bg-green-500 text-white text-[10px] font-black rounded-full shadow-lg shadow-green-100">LUNAS</span>
                            @elseif($p->status_pembayaran == 'DP')
                                <span class="px-4 py-1.5 bg-orange-500 text-white text-[10px] font-black rounded-full shadow-lg shadow-orange-100">DP PAYMENT</span>
                            @else
                                <span class="px-4 py-1.5 bg-rose-500 text-white text-[10px] font-black rounded-full shadow-lg shadow-rose-100">UNPAID</span>
                            @endif
                            <span class="text-lg font-black text-slate-800 tracking-tighter">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</span>
                        </div>
                        
                        @if($p->status_pembayaran !== 'Lunas')
                            <form action="{{ route('pemesanan.mark-paid', $p->id) }}" method="POST" class="mb-4">
                                @csrf
                                <button type="submit" class="w-full py-4 bg-gradient-to-r from-pink-500 to-rose-500 text-white rounded-2xl font-black shadow-xl shadow-pink-200 hover:scale-[1.02] active:scale-95 transition-all text-xs tracking-widest">
                                    LUNASKAN SEKARANG
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('pemesanan.faktur', $p->id) }}" target="_blank" class="w-full flex items-center justify-center py-4 bg-white text-indigo-600 rounded-2xl font-black border-2 border-indigo-100 shadow-xl shadow-indigo-50/50 hover:bg-indigo-50 hover:scale-[1.02] active:scale-95 transition-all text-xs tracking-widest">
                            <i data-lucide="printer" class="w-4 h-4 mr-2"></i> CETAK FAKTUR PDF
                        </a>
                    </div>
                </div>
            </div>

            <div class="glass rounded-[2rem] shadow-2xl shadow-slate-200/50 border border-white/60 overflow-hidden">
                <div class="p-8 text-center uppercase">
                    <label class="text-[10px] font-black text-slate-400 tracking-[0.2em] block mb-6">Profil Customer</label>
                    <div class="relative w-24 h-24 mx-auto mb-4 group p-1 bg-gradient-to-tr from-pink-500 to-indigo-500 rounded-[2rem] shadow-lg">
                        <div class="w-full h-full rounded-[1.8rem] bg-white flex items-center justify-center text-slate-800 font-bold text-2xl overflow-hidden">
                             @if($p->pelanggan->foto)
                                <img src="{{ asset('storage/' . $p->pelanggan->foto) }}" class="w-full h-full object-cover">
                             @else
                                {{ strtoupper(substr($p->pelanggan->nama_lengkap ?? 'G', 0, 1)) }}
                             @endif
                        </div>
                    </div>
                    <p class="font-black text-slate-800 text-lg tracking-tight mb-1">{{ $p->pelanggan->nama_lengkap ?? 'Guest' }}</p>
                    <span class="inline-block px-3 py-1 bg-pink-100 text-pink-600 text-[10px] font-black rounded-full tracking-widest mb-6">{{ $p->pelanggan->kategori_pelanggan }}</span>
                    
                    <div class="space-y-4 pt-6 border-t border-white/30">
                        <div class="flex items-center text-slate-600 text-sm font-bold bg-white/50 p-3 rounded-xl border border-white/40">
                            <i data-lucide="phone" class="w-4 h-4 mr-3 text-pink-500"></i>
                            {{ $p->pelanggan->no_telepon ?? '-' }}
                        </div>
                        <div class="flex items-center text-slate-600 text-sm font-bold bg-white/50 p-3 rounded-xl border border-white/40">
                            <i data-lucide="mail" class="w-4 h-4 mr-3 text-indigo-500"></i>
                            {{ explode('@', $p->pelanggan->email ?? '-')[0] }}...
                        </div>
                    </div>
                </div>
            </div>
            
            <form action="{{ route('pemesanan.destroy', $p->id) }}" method="POST"
                onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesanan ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="w-full py-4 bg-white hover:bg-rose-50 text-rose-500 rounded-2xl font-bold border border-rose-100 transition-all active:scale-95 text-xs tracking-widest">
                    <i data-lucide="trash-2" class="w-4 h-4 inline mr-2"></i> DELETE ORDER
                </button>
            </form>
        </div>
    </div>
@endsection
