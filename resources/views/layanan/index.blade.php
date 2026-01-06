@extends('layouts.app')

@section('content')
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8 mb-12">
        <div>
            <h1 class="text-4xl font-black text-slate-800 tracking-tight">Daftar Layanan</h1>
            <p class="text-sm font-medium text-slate-500 mt-2">Kelola dan pantau katalog jasa cetak digital Anda.</p>
        </div>
        
        <div class="flex flex-wrap md:flex-nowrap items-center gap-4">
            <form id="layanan-filter-form" action="{{ route('layanan.index') }}" method="GET" class="relative group min-w-[300px]">
                <span class="absolute inset-y-0 left-0 flex items-center pl-4">
                    <i data-lucide="search" class="w-4 h-4 text-slate-400 group-focus-within:text-[var(--theme-primary)] transition-colors"></i>
                </span>
                <input id="layanan-search-input" name="search" value="{{ request('search') }}" type="text" placeholder="Cari layanan atau deskripsi..."
                    class="w-full glass border border-white/40 rounded-2xl pl-11 pr-4 py-3 text-xs font-bold text-slate-600 focus:outline-none focus:ring-4 focus:ring-[var(--theme-accent)] focus:border-[var(--theme-primary)] transition-all">
            </form>

            <a href="{{ route('layanan.create') }}"
                class="flex items-center px-6 py-3 text-xs font-black text-white rounded-2xl shadow-xl shadow-slate-200/50 hover:scale-105 active:scale-95 transition-all duration-300"
                style="background: linear-gradient(135deg, var(--theme-primary), var(--theme-to))">
                <i data-lucide="plus" class="w-4 h-4 mr-2"></i>
                <span>TAMBAH BARU</span>
            </a>
        </div>
    </div>


    <div id="layanan-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
        @include('layanan.partials._grid')
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('layanan-filter-form');
            const grid = document.getElementById('layanan-grid');
            const searchInput = document.getElementById('layanan-search-input');
            const navInput = document.getElementById('navbar-search-input');
            let timeout = null;

            async function performAjaxSearch() {
                const formData = new FormData(form);
                const params = new URLSearchParams(formData);
                
                // Loading State
                grid.style.opacity = '0.4';
                grid.style.filter = 'blur(4px)';
                grid.style.transition = 'all 0.3s ease';

                try {
                    const response = await fetch(`${form.action}?${params.toString()}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    const html = await response.text();
                    grid.innerHTML = html;
                    
                    if (window.lucide) window.lucide.createIcons();

                    // Update URL
                    window.history.replaceState({}, '', `${window.location.pathname}?${params.toString()}`);
                } catch (error) {
                    console.error('Layanan Search failed:', error);
                } finally {
                    grid.style.opacity = '1';
                    grid.style.filter = 'none';
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
