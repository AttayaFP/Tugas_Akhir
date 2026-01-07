@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-3xl font-black text-slate-800 tracking-tight">Laporan</h2>
            <p class="text-slate-500 font-medium">Cetak laporan transaksi dan master data</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Card 1: Laporan Pemesanan -->
        <div class="glass p-8 rounded-[2.5rem] border border-white/50 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <i data-lucide="shopping-bag" class="w-32 h-32 text-slate-900"></i>
            </div>
            
            <div class="relative z-10">
                <div class="w-14 h-14 bg-indigo-500/10 rounded-2xl flex items-center justify-center mb-6 text-indigo-600">
                    <i data-lucide="file-text" class="w-7 h-7"></i>
                </div>
                
                <h3 class="text-xl font-black text-slate-800 mb-2">Laporan Pemesanan</h3>
                <p class="text-slate-500 mb-6 text-sm">Rekap riwayat transaksi pemesanan lengkap dengan filter tanggal.</p>

                <form action="{{ route('laporan.cetak-pemesanan') }}" method="GET" target="_blank" class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-1.5">Dari</label>
                            <input type="date" name="start_date" class="w-full bg-white/50 border border-white/40 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-1.5">Sampai</label>
                            <input type="date" name="end_date" class="w-full bg-white/50 border border-white/40 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/20">
                        </div>
                    </div>
                    <button type="submit" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-sm shadow-xl shadow-indigo-500/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                        <i data-lucide="printer" class="w-4 h-4"></i>
                        <span>Cetak Laporan</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Card 2: Laporan Pendapatan -->
        <div class="glass p-8 rounded-[2.5rem] border border-white/50 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <i data-lucide="banknote" class="w-32 h-32 text-emerald-900"></i>
            </div>
            
            <div class="relative z-10">
                <div class="w-14 h-14 bg-emerald-500/10 rounded-2xl flex items-center justify-center mb-6 text-emerald-600">
                    <i data-lucide="trending-up" class="w-7 h-7"></i>
                </div>
                
                <h3 class="text-xl font-black text-slate-800 mb-2">Laporan Pendapatan</h3>
                <p class="text-slate-500 mb-6 text-sm">Ringkasan total pendapatan harian.</p>

                <form action="{{ route('laporan.cetak-pendapatan') }}" method="GET" target="_blank" class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-1.5">Dari</label>
                            <input type="date" name="start_date" class="w-full bg-white/50 border border-white/40 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-400 uppercase tracking-widest mb-1.5">Sampai</label>
                            <input type="date" name="end_date" class="w-full bg-white/50 border border-white/40 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-700 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                        </div>
                    </div>
                    <button type="submit" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm shadow-xl shadow-emerald-500/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                        <i data-lucide="printer" class="w-4 h-4"></i>
                        <span>Cetak Laporan</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Card 3: Master Data Pelanggan -->
        <div class="glass p-8 rounded-[2.5rem] border border-white/50 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <i data-lucide="users" class="w-32 h-32 text-blue-900"></i>
            </div>
            <div class="relative z-10">
                <div class="w-14 h-14 bg-blue-500/10 rounded-2xl flex items-center justify-center mb-6 text-blue-600">
                    <i data-lucide="user-check" class="w-7 h-7"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-2">Data Pelanggan</h3>
                <p class="text-slate-500 mb-6 text-sm">Cetak daftar seluruh pelanggan yang terdaftar.</p>
                <a href="{{ route('laporan.cetak-pelanggan') }}" target="_blank" class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-sm shadow-xl shadow-blue-500/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    <span>Cetak Data Pelanggan</span>
                </a>
            </div>
        </div>

        <!-- Card 4: Master Data Layanan -->
        <div class="glass p-8 rounded-[2.5rem] border border-white/50 relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:scale-110 transition-transform duration-500">
                <i data-lucide="layers" class="w-32 h-32 text-pink-900"></i>
            </div>
            <div class="relative z-10">
                <div class="w-14 h-14 bg-pink-500/10 rounded-2xl flex items-center justify-center mb-6 text-pink-600">
                    <i data-lucide="package" class="w-7 h-7"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-2">Data Layanan</h3>
                <p class="text-slate-500 mb-6 text-sm">Cetak daftar seluruh layanan / produk yang tersedia.</p>
                <a href="{{ route('laporan.cetak-layanan') }}" target="_blank" class="w-full py-3.5 bg-pink-600 hover:bg-pink-700 text-white rounded-xl font-bold text-sm shadow-xl shadow-pink-500/20 transition-all active:scale-95 flex items-center justify-center gap-2">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    <span>Cetak Data Layanan</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
