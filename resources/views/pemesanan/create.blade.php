@extends('layouts.app')

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div class="flex items-center gap-4">
            <a href="{{ route('pemesanan.index') }}" class="p-3 glass rounded-2xl text-slate-400 hover:text-[var(--theme-primary)] transition-all">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight">Buat Pesanan Baru</h1>
                <p class="text-sm font-medium text-slate-500 mt-1">Input transaksi cetak multi-layanan untuk pelanggan.</p>
            </div>
        </div>
    </div>

    <form id="order-form" action="{{ route('pemesanan.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        @csrf
        
        <!-- Left: Customer & Items -->
        <div class="lg:col-span-8 space-y-8">
            <!-- Customer Selection -->
            <div class="glass rounded-[2.5rem] p-8 border border-white/60 shadow-2xl shadow-slate-200/40">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 rounded-xl bg-pink-100 flex items-center justify-center text-pink-500">
                        <i data-lucide="user" class="w-5 h-5"></i>
                    </div>
                    <h2 class="text-lg font-black text-slate-800 uppercase tracking-tight">Pilih Pelanggan</h2>
                </div>
                
                <div class="relative group">
                    <select name="id_pelanggan" id="id_pelanggan" required
                        class="w-full glass border border-white/40 rounded-2xl px-6 py-4 text-sm font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-[var(--theme-accent)] focus:border-[var(--theme-primary)] transition-all appearance-none cursor-pointer">
                        <option value="" disabled selected>Pilih Pelanggan...</option>
                        @foreach($pelanggans as $pel)
                            <option value="{{ $pel->id }}" data-kategori="{{ $pel->kategori_pelanggan }}" {{ old('id_pelanggan') == $pel->id ? 'selected' : '' }}>
                                {{ $pel->nama_lengkap }} ({{ $pel->kategori_pelanggan }})
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                        <i data-lucide="chevron-down" class="w-4 h-4"></i>
                    </div>
                </div>
            </div>

            <!-- Multi-Service Items -->
            <div class="glass rounded-[2.5rem] p-8 border border-white/60 shadow-2xl shadow-slate-200/40">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-500">
                            <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                        </div>
                        <h2 class="text-lg font-black text-slate-800 uppercase tracking-tight">Layanan Cetak</h2>
                    </div>
                    <button type="button" id="add-item-btn"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all shadow-sm">
                        + Tambah Baris
                    </button>
                </div>

                <div id="items-container" class="space-y-4">
                    <!-- Dynamic Rows Will Go Here -->
                    <div class="order-item-row group grid grid-cols-12 gap-4 items-end p-4 bg-slate-50/50 rounded-2xl border border-slate-100 transition-all">
                        <div class="col-span-12 md:col-span-7">
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">Pilih Layanan</label>
                            <div class="relative">
                                <select name="items[0][id_layanan]" required
                                    class="layanan-select w-full glass border border-white/40 rounded-xl px-4 py-3 text-xs font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-400 transition-all appearance-none">
                                    <option value="" disabled selected>Pilih...</option>
                                    @foreach($layanans as $lay)
                                        <option value="{{ $lay->id }}" data-harga="{{ $lay->harga_per_satuan }}">
                                            {{ $lay->nama_layanan }} (Rp {{ number_format($lay->harga_per_satuan, 0, ',', '.') }})
                                        </option>
                                    @endforeach
                                </select>
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-300">
                                    <i data-lucide="chevron-down" class="w-3 h-3"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-span-8 md:col-span-3">
                            <label class="block text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 px-1">Jumlah</label>
                            <input type="number" name="items[0][jumlah]" value="1" min="1" required
                                class="jumlah-input w-full glass border border-white/40 rounded-xl px-4 py-3 text-xs font-bold text-slate-700 focus:outline-none focus:ring-4 focus:ring-indigo-100 focus:border-indigo-400 transition-all text-center">
                        </div>
                        <div class="col-span-4 md:col-span-2 text-right">
                             <button type="button" class="remove-item-btn p-3 text-rose-300 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-all hidden">
                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Summary & Files -->
        <div class="lg:col-span-4 space-y-8">
            <div class="glass rounded-[2.5rem] p-8 border border-white/60 shadow-2xl shadow-slate-200/40 sticky top-28">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-500">
                        <i data-lucide="banknote" class="w-5 h-5"></i>
                    </div>
                    <h2 class="text-lg font-black text-slate-800 uppercase tracking-tight">Ringkasan Nota</h2>
                </div>

                <div class="space-y-4 mb-8">
                    <div class="flex justify-between items-center text-sm font-bold text-slate-400">
                        <span>Total Belanja</span>
                        <span id="label-total-belanja" class="text-slate-800 tracking-tight">Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center text-[10px] font-black text-indigo-400 uppercase tracking-widest">
                        <span>Min. DP (<span id="label-min-dp-persen">50</span>%)</span>
                        <span id="label-min-dp-nominal">Rp 0</span>
                    </div>
                    <div class="pt-4 border-t border-slate-100">
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3 text-center">Uang Muka (DP)</label>
                        <div class="relative group">
                            <span class="absolute left-6 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-sm">Rp</span>
                            <input type="number" name="uang_muka" id="uang_muka" value="{{ old('uang_muka', 0) }}" required
                                class="w-full glass border border-white/40 rounded-2xl pl-14 pr-6 py-4 text-sm font-black text-slate-700 focus:outline-none focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500 transition-all">
                        </div>
                        @error('uang_muka') <p class="text-rose-500 text-[10px] mt-2 font-bold text-center italic">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Metode Bayar</label>
                        <select name="metode_pembayaran" 
                            class="w-full glass border border-white/40 rounded-xl px-5 py-3 text-xs font-bold text-slate-700 focus:outline-none focus:ring-4 focus:border-emerald-500 transition-all appearance-none">
                            <option value="Tunai">Tunai</option>
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="Debit Card">Debit Card</option>
                            <option value="E-Wallet">E-Wallet (OVO/Gopay)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Foto Desain (Optional)</label>
                        <div class="relative cursor-pointer group">
                             <div class="w-full py-4 bg-slate-50 border-2 border-dashed border-slate-200 rounded-2xl flex flex-col items-center justify-center group-hover:bg-white group-hover:border-indigo-300 transition-all">
                                <i data-lucide="image" class="w-6 h-6 text-slate-400 mb-1"></i>
                                <span class="text-[9px] font-black text-slate-400 uppercase">Upload Desain</span>
                             </div>
                             <input type="file" name="foto_desain" class="absolute inset-0 opacity-0 cursor-pointer">
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit"
                            class="w-full py-4 bg-gradient-to-br from-pink-500 to-rose-600 text-white rounded-2xl font-black text-xs uppercase tracking-[0.2em] shadow-xl shadow-rose-200 hover:scale-[1.02] active:scale-95 transition-all">
                            PROSES PESANAN
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="glass rounded-[2rem] p-8 border border-white/60 shadow-lg shadow-slate-200/40">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">Catatan Pesanan</label>
                <textarea name="keterangan" rows="4" placeholder="Misal: Laminasi glossy 2 sisi..."
                    class="w-full glass border border-white/40 rounded-2xl px-5 py-4 text-xs font-medium text-slate-600 focus:outline-none focus:ring-4 transition-all">{{ old('keterangan') }}</textarea>
            </div>
        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('items-container');
            const addBtn = document.getElementById('add-item-btn');
            const pelangganSelect = document.getElementById('id_pelanggan');
            const totalLabel = document.getElementById('label-total-belanja');
            const dpPersenLabel = document.getElementById('label-min-dp-persen');
            const dpNominalLabel = document.getElementById('label-min-dp-nominal');
            const uangMukaInput = document.getElementById('uang_muka');
            
            let rowIndex = 1;

            function calculateGrandTotal() {
                let grandTotal = 0;
                document.querySelectorAll('.order-item-row').forEach(row => {
                    const select = row.querySelector('.layanan-select');
                    const qtyInput = row.querySelector('.jumlah-input');
                    const selectedOption = select.options[select.selectedIndex];
                    
                    if (selectedOption && selectedOption.dataset.harga) {
                        const harga = parseFloat(selectedOption.dataset.harga);
                        const qty = parseInt(qtyInput.value) || 0;
                        grandTotal += (harga * qty);
                    }
                });

                totalLabel.innerText = 'Rp ' + grandTotal.toLocaleString('id-ID');
                
                // DP Logic
                const selectedPelanggan = pelangganSelect.options[pelangganSelect.selectedIndex];
                const kategori = selectedPelanggan ? selectedPelanggan.dataset.kategori : 'Regular';
                
                let persen = 0.5;
                if (kategori === 'Member') persen = 0.3;
                if (kategori === 'VIP') persen = 0;

                const minDp = grandTotal * persen;
                dpPersenLabel.innerText = (persen * 100);
                dpNominalLabel.innerText = 'Rp ' + minDp.toLocaleString('id-ID');
                
                // Optional: Auto set DP if 0
                if (uangMukaInput.value == 0 || uangMukaInput.value == '') {
                    uangMukaInput.placeholder = minDp;
                }
            }

            addBtn.addEventListener('click', () => {
                const newRow = container.children[0].cloneNode(true);
                newRow.querySelector('.layanan-select').name = `items[${rowIndex}][id_layanan]`;
                newRow.querySelector('.layanan-select').value = "";
                newRow.querySelector('.jumlah-input').name = `items[${rowIndex}][jumlah]`;
                newRow.querySelector('.jumlah-input').value = 1;
                
                const removeBtn = newRow.querySelector('.remove-item-btn');
                removeBtn.classList.remove('hidden');
                removeBtn.addEventListener('click', () => {
                    newRow.remove();
                    calculateGrandTotal();
                });

                newRow.querySelector('.layanan-select').addEventListener('change', calculateGrandTotal);
                newRow.querySelector('.jumlah-input').addEventListener('input', calculateGrandTotal);

                container.appendChild(newRow);
                rowIndex++;
                
                if (window.lucide) window.lucide.createIcons();
            });

            // Initial row events
            document.querySelector('.layanan-select').addEventListener('change', calculateGrandTotal);
            document.querySelector('.jumlah-input').addEventListener('input', calculateGrandTotal);
            pelangganSelect.addEventListener('change', calculateGrandTotal);

            // Trigger once
            calculateGrandTotal();
        });
    </script>
@endsection
