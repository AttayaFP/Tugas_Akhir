<aside
    class="flex flex-col w-72 h-screen px-6 py-8 overflow-y-auto glass border-r border-white/20 rtl:border-r-0 rtl:border-l dark:glass-dark dark:border-slate-700 transition-all duration-300">
    <a href="{{ url('/dashboard') }}" class="flex items-center gap-3 mb-12 px-2">
        <div class="p-2.5 rounded-xl shadow-lg transition-all duration-500" style="background: linear-gradient(to top right, var(--theme-primary), var(--secondary)); shadow-color: var(--theme-primary)">
            <i data-lucide="printer" class="w-7 h-7 text-white"></i>
        </div>
        <div class="flex flex-col">
            <span class="text-2xl font-black text-slate-800 tracking-tight dark:text-white">DigiPrint V2</span>
            <span class="text-[10px] font-bold uppercase tracking-widest transition-colors duration-500" style="color: var(--theme-primary)">Management System</span>
        </div>
    </a>

    <div class="flex flex-col justify-between flex-1">
        <nav class="space-y-1.5">
            <a href="{{ url('/dashboard') }}"
                class="flex items-center px-4 py-3 transition-all duration-500 rounded-2xl group {{ request()->is('dashboard') ? 'glass shadow-sm border-white/40' : 'text-slate-600 hover:bg-white/50' }}"
                style="{{ request()->is('dashboard') ? 'background-color: var(--theme-accent); color: var(--theme-primary); font-weight: 900;' : '' }}">
                <i data-lucide="layout-dashboard" class="w-5 h-5 group-hover:scale-110 transition-transform" style="{{ request()->is('dashboard') ? 'color: var(--theme-primary)' : '' }}"></i>
                <span class="mx-3 text-sm">Dashboard</span>
            </a>

            <div class="pt-6 pb-2 px-4">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Master Data</p>
            </div>

            <a href="{{ url('/pelanggan') }}"
                class="flex items-center px-4 py-3 transition-all duration-500 rounded-2xl group {{ request()->is('pelanggan*') ? 'glass shadow-sm border-white/40' : 'text-slate-600 hover:bg-white/50' }}"
                style="{{ request()->is('pelanggan*') ? 'background-color: var(--theme-accent); color: var(--theme-primary); font-weight: 900;' : '' }}">
                <i data-lucide="users" class="w-5 h-5 group-hover:scale-110 transition-transform" style="{{ request()->is('pelanggan*') ? 'color: var(--theme-primary)' : '' }}"></i>
                <span class="mx-3 text-sm">Pelanggan</span>
            </a>

            <a href="{{ url('/layanan') }}"
                class="flex items-center px-4 py-3 transition-all duration-500 rounded-2xl group {{ request()->is('layanan*') ? 'glass shadow-sm border-white/40' : 'text-slate-600 hover:bg-white/50' }}"
                style="{{ request()->is('layanan*') ? 'background-color: var(--theme-accent); color: var(--theme-primary); font-weight: 900;' : '' }}">
                <i data-lucide="package" class="w-5 h-5 group-hover:scale-110 transition-transform" style="{{ request()->is('layanan*') ? 'color: var(--theme-primary)' : '' }}"></i>
                <span class="mx-3 text-sm">Layanan</span>
            </a>

            <div class="pt-6 pb-2 px-4">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Transaksi</p>
            </div>

            <a href="{{ url('/pemesanan') }}"
                class="flex items-center px-4 py-3 transition-all duration-500 rounded-2xl group {{ request()->is('pemesanan*') ? 'glass shadow-sm border-white/40' : 'text-slate-600 hover:bg-white/50' }}"
                style="{{ request()->is('pemesanan*') ? 'background-color: var(--theme-accent); color: var(--theme-primary); font-weight: 900;' : '' }}">
                <i data-lucide="shopping-cart" class="w-5 h-5 group-hover:scale-110 transition-transform" style="{{ request()->is('pemesanan*') ? 'color: var(--theme-primary)' : '' }}"></i>
                <span class="mx-3 text-sm">Pemesanan</span>
            </a>

            <div class="pt-6 pb-2 px-4">
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Laporan</p>
            </div>

            <a href="{{ url('/laporan') }}"
                class="flex items-center px-4 py-3 transition-all duration-500 rounded-2xl group {{ request()->is('laporan*') ? 'glass shadow-sm border-white/40' : 'text-slate-600 hover:bg-white/50' }}"
                style="{{ request()->is('laporan*') ? 'background-color: var(--theme-accent); color: var(--theme-primary); font-weight: 900;' : '' }}">
                <i data-lucide="file-bar-chart" class="w-5 h-5 group-hover:scale-110 transition-transform" style="{{ request()->is('laporan*') ? 'color: var(--theme-primary)' : '' }}"></i>
                <span class="mx-3 text-sm">Laporan</span>
            </a>
        </nav>

        <div class="mt-auto pt-10 px-2 space-y-4">
            <div class="p-4 rounded-3xl border border-white/50 transition-all duration-500" style="background: linear-gradient(to bottom right, var(--theme-accent), transparent)">
                <h3 class="text-xs font-bold text-slate-800">Support Center</h3>
                <p class="mt-1 text-[10px] text-slate-500 leading-relaxed font-medium">Butuh bantuan teknis? Hubungi tim pengembang.</p>
                <button class="mt-3 w-full py-2 bg-white/80 hover:bg-white text-[10px] font-bold text-slate-700 rounded-xl transition-all shadow-sm">BANTUAN</button>
            </div>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full flex items-center px-4 py-3 text-slate-500 transition-all duration-300 rounded-2xl hover:bg-rose-50 hover:text-rose-600 group">
                    <i data-lucide="log-out" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                    <span class="mx-3 text-sm font-medium">Logout System</span>
                </button>
            </form>
        </div>
    </div>
</aside>
