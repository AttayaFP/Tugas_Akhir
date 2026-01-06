<header
    class="flex items-center justify-between px-8 py-5 glass border-b border-white/20 dark:glass-dark dark:border-slate-700 sticky top-0 z-40">
    <div class="flex items-center gap-6">
        <button class="text-slate-500 p-2 hover:bg-white/50 rounded-xl focus:outline-none lg:hidden transition-colors">
            <i data-lucide="menu" class="w-6 h-6"></i>
        </button>

        <form id="navbar-search-form" action="{{ route('pemesanan.index') }}" method="GET" class="relative hidden sm:block group">
            <span class="absolute inset-y-0 left-0 flex items-center pl-4">
                <i data-lucide="search" class="w-4 h-4 text-slate-400 group-focus-within:text-[var(--theme-primary)] transition-colors"></i>
            </span>
            <input id="navbar-search-input" name="search" value="{{ request('search') }}"
                class="w-64 pl-11 pr-4 py-2.5 text-xs font-medium text-slate-600 bg-white/50 border border-white/40 rounded-2xl focus:border-opacity-50 focus:bg-white focus:ring-4 focus:outline-none transition-all duration-300 placeholder:text-slate-400"
                style="--tw-ring-color: var(--theme-primary); focus-border-color: var(--theme-primary)"
                type="text" placeholder="Search for anything...">
        </form>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const navForm = document.getElementById('navbar-search-form');
                const navInput = document.getElementById('navbar-search-input');
                let navTimeout = null;

                if (navInput && navForm) {
                    navInput.addEventListener('input', function() {
                        // Only auto-submit if NOT on the pemesanan index page
                        // On the index page, a separate AJAX listener handles it
                        if (!window.location.pathname.includes('/pemesanan')) {
                            clearTimeout(navTimeout);
                            navTimeout = setTimeout(() => {
                                navForm.submit();
                            }, 800);
                        }
                    });

                    // Handle enter key manually to prevent refresh if already on page
                    navForm.addEventListener('submit', function(e) {
                         if (window.location.pathname.includes('/pemesanan')) {
                            e.preventDefault();
                         }
                    });
            });
        </script>
    </div>

    <div class="flex items-center gap-6">
        <!-- High-End Theme Switcher -->
        <div class="relative group" id="theme-switcher">
            <button class="flex items-center gap-3 px-4 py-2.5 glass border-white/40 rounded-2xl shadow-sm hover:shadow-md transition-all duration-500 group/btn overflow-hidden relative">
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover/btn:translate-x-full transition-transform duration-1000"></div>
                <div class="relative flex items-center gap-2.5">
                    <i data-lucide="palette" class="w-5 h-5 transition-transform duration-500 group-hover/btn:rotate-12" style="color: var(--theme-primary)"></i>
                    <span class="text-[11px] font-black uppercase tracking-[0.15em] text-slate-700">Studio Tema</span>
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 opacity-50 text-amber-500 animate-pulse"></i>
                </div>
            </button>

            <!-- Compact Aesthetic Gallery Dropdown -->
            <div class="absolute right-0 mt-4 w-72 glass rounded-[2rem] shadow-2xl border border-white/50 overflow-hidden opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-700 z-50 transform group-hover:translate-y-0 translate-y-3 scale-95 group-hover:scale-100 origin-top-right backdrop-blur-3xl">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-4 px-1">
                        <div>
                            <h4 class="text-[9px] font-black text-slate-800 uppercase tracking-widest leading-none">Aesthetic Gallery</h4>
                        </div>
                        <div class="flex gap-1">
                            <div class="w-1.5 h-1.5 rounded-full bg-pink-400"></div>
                            <div class="w-1.5 h-1.5 rounded-full bg-indigo-400"></div>
                            <div class="w-1.5 h-1.5 rounded-full bg-amber-400"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-4 gap-2">
                        <!-- Sakura -->
                        <button onclick="changeTheme('sakura')" class="theme-btn group/theme flex flex-col items-center gap-1.5 p-1.5 rounded-xl transition-all duration-300 hover:bg-white/60" data-theme="sakura">
                            <div class="w-10 h-10 rounded-xl shadow-sm transition-transform duration-300 group-hover/theme:scale-110 flex items-center justify-center relative overflow-hidden" 
                                style="background: linear-gradient(135deg, hsla(330, 85%, 85%, 1), hsla(330, 85%, 70%, 1))">
                                <i data-lucide="check" class="w-4 h-4 text-white active-check hidden"></i>
                            </div>
                            <span class="text-[8px] font-bold uppercase tracking-tighter text-slate-500">Sakura</span>
                        </button>

                        <!-- Ocean -->
                        <button onclick="changeTheme('ocean')" class="theme-btn group/theme flex flex-col items-center gap-1.5 p-1.5 rounded-xl transition-all duration-300 hover:bg-white/60" data-theme="ocean">
                            <div class="w-10 h-10 rounded-xl shadow-sm transition-transform duration-300 group-hover/theme:scale-110 flex items-center justify-center relative overflow-hidden" 
                                style="background: linear-gradient(135deg, hsla(215, 90%, 82%, 1), hsla(215, 90%, 65%, 1))">
                                <i data-lucide="check" class="w-4 h-4 text-white active-check hidden"></i>
                            </div>
                            <span class="text-[8px] font-bold uppercase tracking-tighter text-slate-500">Ocean</span>
                        </button>

                        <!-- Sunset -->
                        <button onclick="changeTheme('sunset')" class="theme-btn group/theme flex flex-col items-center gap-1.5 p-1.5 rounded-xl transition-all duration-300 hover:bg-white/60" data-theme="sunset">
                            <div class="w-10 h-10 rounded-xl shadow-sm transition-transform duration-300 group-hover/theme:scale-110 flex items-center justify-center relative overflow-hidden" 
                                style="background: linear-gradient(135deg, hsla(35, 95%, 82%, 1), hsla(35, 95%, 65%, 1))">
                                <i data-lucide="check" class="w-4 h-4 text-white active-check hidden"></i>
                            </div>
                            <span class="text-[8px] font-bold uppercase tracking-tighter text-slate-500">Sunset</span>
                        </button>

                        <!-- Forest -->
                        <button onclick="changeTheme('forest')" class="theme-btn group/theme flex flex-col items-center gap-1.5 p-1.5 rounded-xl transition-all duration-300 hover:bg-white/60" data-theme="forest">
                            <div class="w-10 h-10 rounded-xl shadow-sm transition-transform duration-300 group-hover/theme:scale-110 flex items-center justify-center relative overflow-hidden" 
                                style="background: linear-gradient(135deg, hsla(145, 80%, 82%, 1), hsla(145, 80%, 65%, 1))">
                                <i data-lucide="check" class="w-4 h-4 text-white active-check hidden"></i>
                            </div>
                            <span class="text-[8px] font-bold uppercase tracking-tighter text-slate-500">Forest</span>
                        </button>

                        <!-- Lavender -->
                        <button onclick="changeTheme('lavender')" class="theme-btn group/theme flex flex-col items-center gap-1.5 p-1.5 rounded-xl transition-all duration-300 hover:bg-white/60" data-theme="lavender">
                            <div class="w-10 h-10 rounded-xl shadow-sm transition-transform duration-300 group-hover/theme:scale-110 flex items-center justify-center relative overflow-hidden" 
                                style="background: linear-gradient(135deg, hsla(265, 85%, 85%, 1), hsla(265, 85%, 70%, 1))">
                                <i data-lucide="check" class="w-4 h-4 text-white active-check hidden"></i>
                            </div>
                            <span class="text-[8px] font-bold uppercase tracking-tighter text-slate-500">Violet</span>
                        </button>

                        <!-- Rose Gold -->
                        <button onclick="changeTheme('rosegold')" class="theme-btn group/theme flex flex-col items-center gap-1.5 p-1.5 rounded-xl transition-all duration-300 hover:bg-white/60" data-theme="rosegold">
                            <div class="w-10 h-10 rounded-xl shadow-sm transition-transform duration-300 group-hover/theme:scale-110 flex items-center justify-center relative overflow-hidden" 
                                style="background: linear-gradient(135deg, hsla(15, 85%, 82%, 1), hsla(15, 85%, 65%, 1))">
                                <i data-lucide="check" class="w-4 h-4 text-white active-check hidden"></i>
                            </div>
                            <span class="text-[8px] font-bold uppercase tracking-tighter text-slate-500">Rose</span>
                        </button>

                        <!-- Cyber -->
                        <button onclick="changeTheme('cyber')" class="theme-btn group/theme flex flex-col items-center gap-1.5 p-1.5 rounded-xl transition-all duration-300 hover:bg-white/60" data-theme="cyber">
                            <div class="w-10 h-10 rounded-xl shadow-sm transition-transform duration-300 group-hover/theme:scale-110 flex items-center justify-center relative overflow-hidden" 
                                style="background: linear-gradient(135deg, hsla(185, 95%, 80%, 1), hsla(210, 95%, 70%, 1))">
                                <i data-lucide="check" class="w-4 h-4 text-white active-check hidden"></i>
                            </div>
                            <span class="text-[8px] font-bold uppercase tracking-tighter text-slate-500">Cyber</span>
                        </button>

                        <!-- Frost -->
                        <button onclick="changeTheme('frost')" class="theme-btn group/theme flex flex-col items-center gap-1.5 p-1.5 rounded-xl transition-all duration-300 hover:bg-white/60" data-theme="frost">
                            <div class="w-10 h-10 rounded-xl shadow-sm transition-transform duration-300 group-hover/theme:scale-110 flex items-center justify-center relative overflow-hidden" 
                                style="background: linear-gradient(135deg, hsla(210, 80%, 90%, 1), hsla(210, 80%, 80%, 1))">
                                <i data-lucide="check" class="w-4 h-4 text-white active-check hidden"></i>
                            </div>
                            <span class="text-[8px] font-bold uppercase tracking-tighter text-slate-500">Frost</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2 pr-6 border-r border-slate-200/50">
            <button class="relative p-2.5 text-slate-500 hover:bg-opacity-20 rounded-xl transition-all duration-300" style="--tw-bg-opacity: 0.1; background-color: var(--theme-accent); color: var(--theme-primary)">
                <i data-lucide="bell" class="w-5 h-5"></i>
                <span class="absolute top-2 right-2 w-2 h-2 border-2 border-white rounded-full" style="background-color: var(--theme-primary)"></span>
            </button>
            <button class="p-2.5 text-slate-500 hover:bg-slate-100 rounded-xl transition-all duration-300">
                <i data-lucide="settings" class="w-5 h-5"></i>
            </button>
        </div>

        <div class="flex items-center gap-4 pl-2">
            <div class="hidden md:block text-right">
                <h3 class="text-xs font-black text-slate-800 leading-tight tracking-tight">{{ Auth::user()->nama_lengkap }}</h3>
                <p class="text-[10px] font-bold uppercase tracking-widest mt-0.5" style="color: var(--theme-primary)">Administrator</p>
            </div>
            <div class="relative p-0.5 rounded-2xl shadow-md group cursor-pointer active:scale-95 transition-all" style="background: linear-gradient(to top right, var(--theme-primary), var(--secondary))">
                <div class="w-11 h-11 overflow-hidden rounded-[14px]">
                    @if (Auth::user()->foto)
                        <img src="{{ asset('storage/' . Auth::user()->foto) }}" class="object-cover w-full h-full transform group-hover:scale-110 transition-transform duration-500"
                            alt="avatar">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama_lengkap) }}&background=fdf2f8&color=db2777&bold=true"
                            class="object-cover w-full h-full transform group-hover:scale-110 transition-transform duration-500" alt="avatar">
                    @endif
                </div>
            </div>
        </div>
    </div>
</header>

<script>
    const themes = {
        sakura: {
            from: 'hsla(330, 85%, 85%, 1)',
            via: 'hsla(330, 85%, 92%, 1)',
            to: 'hsla(330, 85%, 80%, 1)',
            primary: '#db2777',
            accent: 'rgba(219, 39, 119, 0.15)',
            glass: 'rgba(255, 215, 230, 0.5)'
        },
        ocean: {
            from: 'hsla(215, 90%, 82%, 1)',
            via: 'hsla(215, 90%, 90%, 1)',
            to: 'hsla(215, 90%, 75%, 1)',
            primary: '#2563eb',
            accent: 'rgba(37, 99, 235, 0.15)',
            glass: 'rgba(219, 234, 254, 0.5)'
        },
        sunset: {
            from: 'hsla(35, 95%, 82%, 1)',
            via: 'hsla(35, 95%, 90%, 1)',
            to: 'hsla(35, 95%, 75%, 1)',
            primary: '#d97706',
            accent: 'rgba(217, 119, 6, 0.15)',
            glass: 'rgba(254, 243, 199, 0.5)'
        },
        forest: {
            from: 'hsla(145, 80%, 82%, 1)',
            via: 'hsla(145, 80%, 90%, 1)',
            to: 'hsla(145, 80%, 75%, 1)',
            primary: '#059669',
            accent: 'rgba(5, 150, 105, 0.15)',
            glass: 'rgba(209, 250, 229, 0.5)'
        },
        lavender: {
            from: 'hsla(265, 85%, 85%, 1)',
            via: 'hsla(265, 85%, 92%, 1)',
            to: 'hsla(265, 85%, 80%, 1)',
            primary: '#7c3aed',
            accent: 'rgba(124, 58, 237, 0.15)',
            glass: 'rgba(237, 233, 254, 0.5)'
        },
        rosegold: {
            from: 'hsla(15, 85%, 82%, 1)',
            via: 'hsla(15, 85%, 90%, 1)',
            to: 'hsla(15, 85%, 75%, 1)',
            primary: '#ea580c',
            accent: 'rgba(234, 88, 12, 0.15)',
            glass: 'rgba(255, 237, 213, 0.5)'
        },
        cyber: {
            from: 'hsla(185, 95%, 80%, 1)',
            via: 'hsla(300, 95%, 88%, 1)',
            to: 'hsla(210, 95%, 78%, 1)',
            primary: '#0891b2',
            accent: 'rgba(8, 145, 178, 0.15)',
            glass: 'rgba(207, 250, 254, 0.5)'
        },
        frost: {
            from: 'hsla(210, 80%, 90%, 1)',
            via: 'hsla(210, 80%, 95%, 1)',
            to: 'hsla(210, 80%, 85%, 1)',
            primary: '#475569',
            accent: 'rgba(71, 85, 105, 0.15)',
            glass: 'rgba(241, 245, 249, 0.5)'
        }
    };

    function changeTheme(themeKey) {
        const themeData = themes[themeKey];
        const root = document.documentElement;
        
        root.style.setProperty('--theme-from', themeData.from);
        root.style.setProperty('--theme-via', themeData.via);
        root.style.setProperty('--theme-to', themeData.to);
        root.style.setProperty('--theme-primary', themeData.primary);
        root.style.setProperty('--theme-accent', themeData.accent);
        root.style.setProperty('--theme-glass', themeData.glass);
        
        localStorage.setItem('digiprint-theme', JSON.stringify({key: themeKey, ...themeData}));
        updateActiveIndicator(themeKey);
        
        // Update all theme-dependent elements manually if needed or let CSS variables do it
        // The palette icon color is now bound to CSS variable style="color: var(--theme-primary)"
    }

    function updateActiveIndicator(activeKey) {
        document.querySelectorAll('.theme-btn').forEach(btn => {
            const check = btn.querySelector('.active-check');
            if (btn.dataset.theme === activeKey) {
                check.classList.remove('hidden');
                btn.classList.add('bg-white/80', 'shadow-sm');
            } else {
                check.classList.add('hidden');
                btn.classList.remove('bg-white/80', 'shadow-sm');
            }
        });
    }

    // Initialize Active Indicator on Load
    document.addEventListener('DOMContentLoaded', () => {
        const savedTheme = localStorage.getItem('digiprint-theme');
        if (savedTheme) {
            const { key } = JSON.parse(savedTheme);
            updateActiveIndicator(key);
        } else {
            updateActiveIndicator('sakura');
        }
    });
</script>
