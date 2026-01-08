@extends('layouts.app')

@section('content')
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800">Dashboard Overview</h1>
        <p class="text-slate-500">Selamat datang kembali, <strong>{{ Auth::user()->nama_lengkap }}</strong>! Berikut adalah
            ringkasan sistem Anda.</p>
    </div>

    <!-- Stats Card -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 mb-8">
        <!-- Card 1: Total Pemesanan -->
        <div class="p-6 bg-white rounded-xl shadow-sm border border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Pesanan</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $total_pemesanan }}</h3>
                </div>
                <div class="p-3 bg-pink-100 text-pink-600 rounded-lg">
                    <i data-lucide="shopping-bag" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm text-slate-500">
                <span>Semua waktu</span>
            </div>
        </div>

        <!-- Card 2: Total Pelanggan -->
        <div class="p-6 bg-white rounded-xl shadow-sm border border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Pelanggan</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $total_pelanggan }}</h3>
                </div>
                <div class="p-3 bg-purple-100 text-purple-600 rounded-lg">
                    <i data-lucide="users" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm text-slate-500">
                <span>Terdaftar di sistem</span>
            </div>
        </div>

        <!-- Card 3: Total Layanan -->
        <div class="p-6 bg-white rounded-xl shadow-sm border border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Layanan</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $total_layanan }}</h3>
                </div>
                <div class="p-3 bg-blue-100 text-blue-600 rounded-lg">
                    <i data-lucide="layers" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm text-slate-500">
                <span>Layanan aktif</span>
            </div>
        </div>

        <!-- Card 4: Total Pendapatan -->
        <div class="p-6 bg-white rounded-xl shadow-sm border border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Total Pendapatan</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-1">Rp
                        {{ number_format($total_pendapatan, 0, ',', '.') }}</h3>
                </div>
                <div class="p-3 bg-green-100 text-green-600 rounded-lg">
                    <i data-lucide="banknote" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm text-slate-500">
                <span>Akumulasi total</span>
            </div>
        </div>

        <!-- Card 5: Pending Order -->
        <div class="p-6 bg-white rounded-xl shadow-sm border border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-500">Pending Order</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $pending_order }}</h3>
                </div>
                <div class="p-3 bg-yellow-100 text-yellow-600 rounded-lg">
                    <i data-lucide="clock" class="w-6 h-6"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm text-red-500">
                <span>Perlu diproses</span>
            </div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex justify-between items-center">
            <h2 class="text-lg font-bold text-slate-800">Pesanan Terbaru</h2>
            <a href="{{ url('/pemesanan') }}" class="text-sm text-pink-600 hover:text-pink-700 font-medium">Lihat Semua</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-500">
                <thead class="text-xs text-slate-700 uppercase bg-slate-50">
                    <tr>
                        <th class="px-6 py-3">No Nota</th>
                        <th class="px-6 py-3">Pelanggan</th>
                        <th class="px-6 py-3">Layanan</th>
                        <th class="px-6 py-3">Total</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recent_orders as $order)
                        <tr class="bg-white border-b hover:bg-slate-50">
                            <td class="px-6 py-4 font-medium text-slate-900">{{ $order->no_nota }}</td>
                            <td class="px-6 py-4">{{ $order->pelanggan->nama_lengkap }}</td>
                            <td class="px-6 py-4">{{ $order->layanan->nama_layanan }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $totalNotaDash = \App\Models\Pemesanan::where('no_nota', $order->no_nota)->sum('total_harga');
                                @endphp
                                Rp {{ number_format($totalNotaDash, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                @if ($order->status_pesanan == 'Selesai')
                                    <span
                                        class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Selesai</span>
                                @elseif($order->status_pesanan == 'Pending')
                                    <span
                                        class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">Pending</span>
                                @elseif($order->status_pesanan == 'Sampai')
                                    <span
                                        class="bg-cyan-100 text-cyan-800 text-xs font-medium px-2.5 py-0.5 rounded">Sampai</span>
                                @else
                                    <span
                                        class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">{{ $order->status_pesanan }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-slate-500">Belum ada pesanan terbaru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
