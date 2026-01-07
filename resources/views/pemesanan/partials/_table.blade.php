@forelse($pemesanan as $p)
    <tr class="bg-white border-b hover:bg-slate-50 transition-colors">
        <td class="px-6 py-4">
            <div class="font-bold text-slate-900">{{ $p->no_nota }}</div>
            <div class="text-xs text-slate-400">{{ $p->tanggal_pesan->format('d M Y') }}</div>
        </td>
        <td class="px-6 py-4">
            <div class="font-medium text-slate-800">{{ $p->pelanggan->nama_lengkap ?? 'Guest' }}</div>
            <div class="text-xs text-slate-400">{{ $p->pelanggan->no_telepon ?? '-' }}</div>
        </td>
        <td class="px-6 py-4">
            @php
                $itemCount = \App\Models\Pemesanan::where('no_nota', $p->no_nota)->count();
            @endphp
            <div class="text-slate-900 font-bold">
                @if($itemCount > 1)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-100 text-indigo-700 mr-1">
                        {{ $itemCount }} LAYANAN
                    </span>
                @endif
                {{ $p->layanan->nama_layanan ?? 'Unknown' }}
            </div>
            <div class="text-xs text-slate-500">
                @if($itemCount > 1)
                    & item lainnya...
                @else
                    Qty: {{ $p->jumlah }} 
                @endif
                | Bukti: {{ $p->bukti_pembayaran ? 'Ada' : 'Tidak' }}
                @if($p->foto_desain)
                    <span class="ml-1 inline-flex items-center text-blue-500 font-bold">
                        <i data-lucide="image" class="w-3 h-3 mr-0.5"></i> Desain
                    </span>
                @endif
            </div>
        </td>
        <td class="px-6 py-4">
            @php
                $totalNota = \App\Models\Pemesanan::where('no_nota', $p->no_nota)->sum('total_harga');
            @endphp
            <div class="font-bold text-slate-800">Rp {{ number_format($totalNota, 0, ',', '.') }}
            </div>
            <div class="text-[10px] text-slate-400 mt-0.5">Metode: <span class="font-bold text-slate-500 capitalize">{{ $p->metode_pembayaran ?: '-' }}</span></div>
            <div class="mt-2 flex items-center gap-2">
                @if ($p->status_pembayaran == 'Lunas')
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700 uppercase tracking-wider">
                        <i data-lucide="check-circle" class="w-3 h-3 mr-1"></i> Lunas
                    </span>
                @elseif($p->status_pembayaran == 'DP')
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-orange-100 text-orange-700 uppercase tracking-wider">
                        <i data-lucide="pie-chart" class="w-3 h-3 mr-1"></i> DP
                        {{ number_format($p->uang_muka, 0, ',', '.') }}
                    </span>
                    <form action="{{ route('pemesanan.mark-paid', $p->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" title="Tandai Lunas" class="text-xs text-green-500 hover:text-green-700 font-bold">
                            LUNASKAN?
                        </button>
                    </form>
                @else
                    <span
                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 uppercase tracking-wider">
                        <i data-lucide="x-circle" class="w-3 h-3 mr-1"></i> Belum Lunas
                    </span>
                    <form action="{{ route('pemesanan.mark-paid', $p->id) }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" title="Tandai Lunas" class="text-xs text-green-500 hover:text-green-700 font-bold">
                            LUNASKAN?
                        </button>
                    </form>
                @endif
            </div>
        </td>
        <td class="px-6 py-4">
            <form action="{{ route('pemesanan.toggle-status', $p->id) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-left">
                    @if ($p->status_pesanan == 'Selesai')
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 hover:bg-emerald-200 transition">
                            <i data-lucide="check" class="w-3 h-3 mr-1"></i> Selesai
                        </span>
                    @elseif($p->status_pesanan == 'Proses')
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800 hover:bg-blue-200 transition">
                            <i data-lucide="refresh-cw" class="w-3 h-3 mr-1"></i> Proses
                        </span>
                    @elseif($p->status_pesanan == 'Dikirim')
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 hover:bg-indigo-200 transition">
                            <i data-lucide="truck" class="w-4 h-4 mr-1"></i> Dikirim
                        </span>
                    @else
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-800 hover:bg-amber-200 transition">
                            <i data-lucide="clock" class="w-3 h-3 mr-1"></i> {{ $p->status_pesanan }}
                        </span>
                    @endif
                </button>
            </form>
        </td>
        <td class="px-6 py-4 text-right">
            <div class="flex items-center justify-end gap-2">
                <a href="{{ route('pemesanan.show', $p->id) }}"
                    class="p-2 text-slate-400 hover:text-blue-500 hover:bg-blue-50 rounded-lg transition-all">
                    <i data-lucide="eye" class="w-4 h-4"></i>
                </a>
                <form action="{{ route('pemesanan.destroy', $p->id) }}" method="POST"
                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="p-2 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-lg transition-all">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="px-6 py-20 text-center">
            <div class="flex flex-col items-center">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                    <i data-lucide="search-x" class="w-10 h-10 text-slate-300"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800">Tidak ada pesanan ditemukan</h3>
                <p class="text-sm text-slate-500 mt-1">Coba sesuaikan filter atau kata kunci pencarian Anda.</p>
            </div>
        </td>
    </tr>
@endforelse
