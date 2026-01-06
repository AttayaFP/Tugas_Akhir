@extends('layouts.app')

@section('content')
    <form id="filter-form" action="{{ route('pemesanan.index') }}" method="GET" class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Riwayat Pemesanan</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Kelola dan pantau semua transaksi cetak Anda.</p>
        </div>
        <div class="flex flex-wrap md:flex-nowrap gap-3">
            <div class="relative group min-w-[200px]">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-[var(--theme-primary)] transition-colors"></i>
                <input type="text" name="search" id="search-input" value="{{ request('search') }}" placeholder="Nota / Pelanggan..."
                    class="w-full glass border border-white/40 rounded-2xl pl-10 pr-4 py-2.5 text-xs font-bold text-slate-600 focus:outline-none focus:ring-4 focus:ring-[var(--theme-accent)] focus:border-[var(--theme-primary)] transition-all">
            </div>
            <div class="relative group">
                <i data-lucide="calendar" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 group-focus-within:text-[var(--theme-primary)] transition-colors"></i>
                <input type="date" name="tanggal" id="date-input" value="{{ request('tanggal') }}"
                    class="glass border border-white/40 rounded-2xl pl-10 pr-4 py-2.5 text-xs font-bold text-slate-600 focus:outline-none focus:ring-4 focus:ring-[var(--theme-accent)] focus:border-[var(--theme-primary)] transition-all">
            </div>
            <button type="submit"
                class="flex items-center px-6 py-2.5 text-xs font-bold text-white rounded-2xl shadow-lg shadow-slate-200/50 hover:scale-105 active:scale-95 transition-all duration-300"
                style="background: linear-gradient(135deg, var(--theme-primary), var(--theme-to))">
                <i data-lucide="filter" class="w-4 h-4 mr-2"></i>
                <span>Cari Data</span>
            </button>
            @if(request('search') || request('tanggal'))
                <a href="{{ route('pemesanan.index') }}" 
                    class="flex items-center px-4 py-2.5 text-xs font-bold text-slate-500 bg-white/50 border border-white/40 rounded-2xl hover:bg-white transition-all">
                    <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                </a>
            @endif
        </div>
    </form>

    <div class="glass rounded-[2rem] shadow-2xl shadow-slate-200/60 border border-white/60 overflow-hidden mb-10">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-slate-500">
                <thead class="text-xs text-slate-700 uppercase bg-slate-50">
                    <tr>
                        <th class="px-6 py-3">Nota & Tanggal</th>
                        <th class="px-6 py-3">Pelanggan</th>
                        <th class="px-6 py-3">Item Pesanan</th>
                        <th class="px-6 py-3">Total & Pembayaran</th>
                        <th class="px-6 py-3">Status</th>
                        <th class="px-6 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="pemesanan-table-body">
                    @include('pemesanan.partials._table')
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('filter-form');
            const tableBody = document.getElementById('pemesanan-table-body');
            const searchInput = document.getElementById('search-input');
            const dateInput = document.getElementById('date-input');
            const navInput = document.getElementById('navbar-search-input');
            let timeout = null;

            async function performAjaxSearch() {
                const formData = new FormData(form);
                const params = new URLSearchParams(formData);
                
                // Add loading effect
                tableBody.style.opacity = '0.5';
                tableBody.style.filter = 'blur(2px)';

                try {
                    const response = await fetch(`${form.action}?${params.toString()}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const html = await response.text();
                    tableBody.innerHTML = html;
                    
                    // Re-initialize Lucide Icons
                    if (window.lucide) {
                        window.lucide.createIcons();
                    }

                    // Update URL without refresh
                    window.history.replaceState({}, '', `${window.location.pathname}?${params.toString()}`);
                } catch (error) {
                    console.error('Search failed:', error);
                } finally {
                    tableBody.style.opacity = '1';
                    tableBody.style.filter = 'none';
                }
            }

            function debounceSearch() {
                clearTimeout(timeout);
                timeout = setTimeout(performAjaxSearch, 500); 
            }

            if (searchInput) {
                searchInput.addEventListener('input', debounceSearch);
            }

            if (dateInput) {
                dateInput.addEventListener('change', performAjaxSearch);
            }

            // Listen for changes from Navbar Search as well if we're on this page
            if (navInput) {
                navInput.addEventListener('input', (e) => {
                    if (searchInput) {
                        searchInput.value = e.target.value;
                        debounceSearch();
                    }
                });
            }

            // Handle Form Reset Link via AJAX
            const resetBtn = document.querySelector('a[href="{{ route('pemesanan.index') }}"]');
            if (resetBtn) {
                resetBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (searchInput) searchInput.value = '';
                    if (dateInput) dateInput.value = '';
                    performAjaxSearch();
                });
            }
        });
    </script>
@endsection
