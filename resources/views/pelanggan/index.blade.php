@extends('layouts.app')

@section('content')
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 mb-10">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Database Pelanggan</h1>
            <p class="text-sm font-medium text-slate-500 mt-1">Kelola informasi pelanggan dan status keanggotaan.</p>
        </div>
        
        <div class="flex flex-wrap md:flex-nowrap items-center gap-3">
            <form id="pelanggan-filter-form" action="{{ route('pelanggan.index') }}" method="GET" class="relative group min-w-[300px]">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 group-focus-within:text-[var(--theme-primary)] transition-colors"></i>
                </span>
                <input id="pelanggan-search-input" name="search" value="{{ request('search') }}" type="text" placeholder="Cari nama, email, atau ID member..."
                    class="w-full glass border border-white/40 rounded-2xl pl-10 pr-4 py-2.5 text-xs font-bold text-slate-600 focus:outline-none focus:ring-4 focus:ring-[var(--theme-accent)] focus:border-[var(--theme-primary)] transition-all">
            </form>

            <a href="{{ route('pelanggan.create') }}"
                class="flex items-center px-6 py-2.5 text-xs font-bold text-white rounded-2xl shadow-xl shadow-slate-200/50 hover:scale-105 active:scale-95 transition-all duration-300"
                style="background: linear-gradient(135deg, var(--theme-primary), var(--theme-to))">
                <i data-lucide="user-plus" class="w-4 h-4 mr-2"></i>
                <span>TAMBAH BARU</span>
            </a>
        </div>
    </div>


    <div class="glass rounded-[2rem] border border-white/60 shadow-2xl shadow-slate-200/40 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-[10px] font-black uppercase tracking-widest text-slate-400 bg-slate-50/50 border-b border-white/20">
                    <tr>
                        <th class="px-6 py-5">Nama Pelanggan</th>
                        <th class="px-6 py-5">Informasi Kontak</th>
                        <th class="px-6 py-5">Kategori / Kode</th>
                        <th class="px-6 py-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="pelanggan-table-body">
                    @include('pelanggan.partials._table')
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('pelanggan-filter-form');
            const tableBody = document.getElementById('pelanggan-table-body');
            const searchInput = document.getElementById('pelanggan-search-input');
            const navInput = document.getElementById('navbar-search-input');
            let timeout = null;

            async function performAjaxSearch() {
                const formData = new FormData(form);
                const params = new URLSearchParams(formData);
                
                // Loading State
                tableBody.style.opacity = '0.4';
                tableBody.style.filter = 'blur(4px)';
                tableBody.style.transition = 'all 0.3s ease';

                try {
                    const response = await fetch(`${form.action}?${params.toString()}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const html = await response.text();
                    tableBody.innerHTML = html;
                    // Re-initialize Lucide Icons after AJAX update
                    if (window.lucide) {
                        window.lucide.createIcons();
                    }
                    // Update URL
                    window.history.replaceState({}, '', `${window.location.pathname}?${params.toString()}`);
                } catch (error) {
                    console.error('Pelanggan Search failed:', error);
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
                searchInput.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') e.preventDefault();
                });
            }

            // Sync with Navbar Search
            if (navInput) {
                navInput.addEventListener('input', (e) => {
                    if (searchInput) {
                        searchInput.value = e.target.value;
                        debounceSearch();
                    }
                });
            }
        });
    </script>
@endsection
