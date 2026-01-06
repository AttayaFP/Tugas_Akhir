<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Digital Printing</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-pink-50 flex items-center justify-center h-screen">

    <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-xl border border-pink-100">
        <div class="text-center mb-8">
            <div class="inline-flex p-3 bg-pink-100 rounded-xl mb-4 text-pink-500">
                <i data-lucide="printer" class="w-8 h-8"></i>
            </div>
            <h1 class="text-2xl font-bold text-slate-800">Welcome Back!</h1>
            <p class="text-slate-500 text-sm mt-2">Silakan login untuk masuk ke dashboard.</p>
        </div>

        <form action="{{ url('/login') }}" method="POST">
            @csrf

            <div class="mb-5">
                <label for="email" class="block mb-2 text-sm font-medium text-slate-700">Email Address</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <i data-lucide="mail" class="w-5 h-5"></i>
                    </span>
                    <input type="email" name="email" id="email" required
                        class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-300 focus:border-pink-400 transition"
                        placeholder="Masukkan email">
                </div>
                @error('email')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password" class="block mb-2 text-sm font-medium text-slate-700">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <i data-lucide="lock" class="w-5 h-5"></i>
                    </span>
                    <input type="password" name="password" id="password" required
                        class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-300 focus:border-pink-400 transition"
                        placeholder="••••••••">
                </div>
            </div>

            <button type="submit"
                class="w-full py-3 px-4 bg-pink-500 hover:bg-pink-600 text-white font-semibold rounded-lg shadow-md shadow-pink-200 transition duration-300 transform hover:-translate-y-1">
                Masuk Dashboard
            </button>
        </form>

        <div class="mt-8 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} Digital Printing System
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>
</body>

</html>
