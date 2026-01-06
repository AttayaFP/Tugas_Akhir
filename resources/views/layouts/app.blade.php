<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Digital Printing</title>

    <!-- Tailwind CSS (CDN for instant template usage) -->
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        @property --theme-from {
            syntax: '<color>';
            initial-value: hsla(330, 85%, 85%, 1);
            inherits: true;
        }
        @property --theme-via {
            syntax: '<color>';
            initial-value: hsla(330, 85%, 92%, 1);
            inherits: true;
        }
        @property --theme-to {
            syntax: '<color>';
            initial-value: hsla(330, 85%, 80%, 1);
            inherits: true;
        }

        :root {
            --primary: #ec4899;
            --primary-light: #fbcfe8;
            --secondary: #6366f1;
            --accent: #f472b6;
            --glass-bg: rgba(255, 255, 255, 0.6); /* Fallback */
            --glass-border: rgba(255, 255, 255, 0.4);
            
            /* Default Theme Variables (Sakura - More Vibrant) */
            --theme-from: hsla(330, 85%, 85%, 1);
            --theme-via: hsla(330, 85%, 92%, 1);
            --theme-to: hsla(330, 85%, 80%, 1);
            --theme-primary: #db2777;
            --theme-accent: rgba(219, 39, 119, 0.15);
            --theme-glass: rgba(255, 215, 230, 0.5);

            transition: --theme-from 1s ease, --theme-via 1s ease, --theme-to 1s ease, --theme-primary 1s ease;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
            background-image: 
                radial-gradient(at 0% 0%, var(--theme-from) 0, transparent 60%), 
                radial-gradient(at 50% 0%, var(--theme-via) 0, transparent 60%), 
                radial-gradient(at 100% 0%, var(--theme-to) 0, transparent 60%);
            background-attachment: fixed;
            transition: all 1s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass {
            background: var(--theme-glass);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: background 1s cubic-bezier(0.4, 0, 0.2, 1), border 1s ease;
        }

        .glass-dark {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .mesh-gradient {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.5;
            pointer-events: none;
        }

        /* Subtle Noise Texture (Increased for 'Kasar' feel) */
        .noise {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.08;
            pointer-events: none;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3%3Cfilter id='noiseFilter'%3%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3%3C/filter%3%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3%3C/svg%3E");
        }
    </style>
    
    <script>
        // Theme Persistence Logic
        (function() {
            const savedTheme = localStorage.getItem('digiprint-theme');
            if (savedTheme) {
                const themeData = JSON.parse(savedTheme);
                const root = document.documentElement;
                root.style.setProperty('--theme-from', themeData.from);
                root.style.setProperty('--theme-via', themeData.via);
                root.style.setProperty('--theme-to', themeData.to);
                root.style.setProperty('--theme-primary', themeData.primary);
                root.style.setProperty('--theme-accent', themeData.accent);
                root.style.setProperty('--theme-glass', themeData.glass);
            }
        })();
    </script>
</head>

<body class="min-h-screen text-slate-900 overflow-x-hidden">
    <div class="noise"></div>
    <div class="mesh-gradient"></div>

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('components.sidebar')

        <!-- Content Area -->
        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden">
            <!-- Navbar -->
            @include('components.navbar')

            <!-- Main Content -->
            <main class="w-full grow p-6 lg:p-10">
                @yield('content')
            </main>
        </div>
    </div>

    @php
        $alerts = [
            'success' => [
                'bg' => 'bg-emerald-500/10',
                'border' => 'border-emerald-500',
                'icon' => 'check-circle',
                'icon_bg' => 'bg-emerald-500',
                'text' => 'text-emerald-900',
                'label' => 'Berhasil'
            ],
            'error' => [
                'bg' => 'bg-rose-500/10',
                'border' => 'border-rose-500',
                'icon' => 'alert-circle',
                'icon_bg' => 'bg-rose-500',
                'text' => 'text-rose-900',
                'label' => 'Gagal'
            ],
            'warning' => [
                'bg' => 'bg-amber-500/10',
                'border' => 'border-amber-500',
                'icon' => 'alert-triangle',
                'icon_bg' => 'bg-amber-500',
                'text' => 'text-amber-900',
                'label' => 'Perhatian'
            ]
        ];
    @endphp

    @foreach(['success', 'error', 'warning'] as $type)
        @if (session($type))
            <div id="toast-{{ $type }}"
                class="fixed top-8 left-1/2 -translate-x-1/2 flex items-center w-full max-w-md p-5 glass border-t-4 {{ $alerts[$type]['border'] }} rounded-[2rem] shadow-[0_20px_50px_rgba(0,0,0,0.1)] z-[100] animate-in fade-in slide-in-from-top-10 duration-500"
                role="alert">
                <div class="inline-flex items-center justify-center flex-shrink-0 w-12 h-12 {{ $alerts[$type]['icon_bg'] }} text-white rounded-2xl shadow-lg ring-4 ring-white/50">
                    <i data-lucide="{{ $alerts[$type]['icon'] }}" class="w-6 h-6"></i>
                </div>
                <div class="ml-4 flex-1">
                    <div class="text-xs font-black uppercase tracking-widest opacity-40 mb-0.5">{{ $alerts[$type]['label'] }}</div>
                    <div class="text-sm font-bold {{ $alerts[$type]['text'] }} tracking-tight">{{ session($type) }}</div>
                </div>
                <button type="button" onclick="const t = document.getElementById('toast-{{ $type }}'); t.classList.add('animate-out', 'fade-out', 'slide-out-to-top-10'); setTimeout(() => t.remove(), 500)"
                    class="ml-auto -mx-1.5 -my-1.5 glass text-slate-400 hover:text-slate-900 rounded-xl p-2 transition-all">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <script>
                setTimeout(() => {
                    const toast = document.getElementById('toast-{{ $type }}');
                    if (toast) {
                        toast.classList.add('animate-out', 'fade-out', 'slide-out-to-top-10');
                        setTimeout(() => toast.remove(), 500);
                    }
                }, 5000);
            </script>
        @endif
    @endforeach

    <!-- Premium Confirmation Modal -->
    <div id="confirm-modal" class="fixed inset-0 z-[150] hidden">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" id="confirm-overlay"></div>
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div id="confirm-content" class="relative transform overflow-hidden rounded-[2.5rem] glass border border-white/60 bg-white/90 p-8 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg animate-in fade-in zoom-in-95 duration-300">
                <div class="flex items-center gap-4 mb-6">
                    <div id="confirm-icon-container" class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-white/50">
                        <i id="confirm-icon" data-lucide="help-circle" class="w-7 h-7 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-slate-800 tracking-tight" id="confirm-title">Konfirmasi</h3>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-widest" id="confirm-subtitle">Tindakan Diperlukan</p>
                    </div>
                </div>
                <div class="mb-8">
                    <p class="text-slate-600 font-medium leading-relaxed" id="confirm-message">Apakah Anda yakin ingin melanjutkan tindakan ini?</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <button id="confirm-cancel" class="flex-1 px-6 py-4 rounded-2xl glass border border-white/40 text-sm font-black text-slate-500 hover:bg-white/80 transition-all">BATAL</button>
                    <button id="confirm-ok" class="flex-1 px-6 py-4 rounded-2xl text-sm font-black text-white shadow-xl hover:scale-105 active:scale-95 transition-all">KONFIRMASI</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        window.confirmPremium = function(options) {
            const modal = document.getElementById('confirm-modal');
            const title = document.getElementById('confirm-title');
            const message = document.getElementById('confirm-message');
            const icon = document.getElementById('confirm-icon');
            const iconContainer = document.getElementById('confirm-icon-container');
            const okBtn = document.getElementById('confirm-ok');
            const cancelBtn = document.getElementById('confirm-cancel');
            
            const variants = {
                danger: { bg: 'bg-rose-500', color: 'text-rose-500', icon: 'alert-triangle' },
                warning: { bg: 'bg-amber-500', color: 'text-amber-500', icon: 'help-circle' },
                success: { bg: 'bg-emerald-500', color: 'text-emerald-500', icon: 'info' }
            };
            const v = variants[options.variant || 'warning'];
            
            title.innerText = options.title || 'Konfirmasi';
            message.innerText = options.message || 'Apakah Anda yakin?';
            iconContainer.className = `w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg ring-4 ring-white/50 ${v.bg}`;
            icon.setAttribute('data-lucide', v.icon);
            okBtn.className = `flex-1 px-6 py-4 rounded-2xl text-sm font-black text-white shadow-xl hover:scale-105 active:scale-95 transition-all ${v.bg}`;
            
            modal.classList.remove('hidden');
            if (window.lucide) window.lucide.createIcons();

            return new Promise((resolve) => {
                const innerOk = document.getElementById('confirm-ok');
                const innerCancel = document.getElementById('confirm-cancel');
                const innerOverlay = document.getElementById('confirm-overlay');
                
                const cleanup = (val) => {
                    modal.classList.add('hidden');
                    innerOk.replaceWith(innerOk.cloneNode(true));
                    innerCancel.replaceWith(innerCancel.cloneNode(true));
                    resolve(val);
                };
                
                document.getElementById('confirm-ok').addEventListener('click', () => cleanup(true));
                document.getElementById('confirm-cancel').addEventListener('click', () => cleanup(false));
                innerOverlay.addEventListener('click', () => cleanup(false));
            });
        };

        lucide.createIcons();
    </script>
</body>

</html>
