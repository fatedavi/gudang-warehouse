@props(['barang' => null])

<div class="grid grid-cols-1 gap-5 md:grid-cols-2">
    <div>
        <label for="kode_produk" class="label">
            Kode Produk
            <span class="ml-1 rounded bg-emerald-100 px-1.5 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-emerald-700"
                id="tag-otomatis">otomatis</span>
        </label>
        <input type="text" name="kode_produk" id="kode_produk" class="input font-mono"
               value="{{ old('kode_produk', $barang?->kode_produk) }}" placeholder="diisi otomatis">
        <p id="hint-kode" class="mt-1 text-xs text-slate-500">Kode dibuat otomatis dari jenis barang. Bisa diketik manual bila perlu.</p>
        @error('kode_produk')
            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="jenis_barang" class="label">Jenis Barang <span class="text-rose-500">*</span></label>
        <select name="jenis_barang" id="jenis_barang" required class="input"
                data-jenis-asli="{{ $barang?->jenis_barang }}" data-kode-asli="{{ $barang?->kode_produk }}">
            <option value="">-- Pilih Jenis Barang --</option>
            @foreach (\App\Models\Barang::jenisOtomatis() as $jenis)
                <option value="{{ $jenis }}" @selected(old('jenis_barang', $barang?->jenis_barang) === $jenis)>{{ $jenis }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label for="merk_produk" class="label">Merk Produk <span class="text-rose-500">*</span></label>
        <input type="text" name="merk_produk" id="merk_produk" required class="input"
               value="{{ old('merk_produk', $barang?->merk_produk) }}" placeholder="contoh: nike, adidas, aerostreet">
    </div>

    <div>
        <label for="ukuran_produk" class="label">Ukuran Produk <span class="text-rose-500">*</span></label>
        <input type="text" name="ukuran_produk" id="ukuran_produk" required class="input"
               value="{{ old('ukuran_produk', $barang?->ukuran_produk) }}" placeholder="contoh: 39, 41, S, M">
    </div>

    <div>
        <label for="warna_produk" class="label">Warna Produk <span class="text-rose-500">*</span></label>
        <input type="text" name="warna_produk" id="warna_produk" required class="input"
               value="{{ old('warna_produk', $barang?->warna_produk) }}" placeholder="contoh: hitam, putih">
    </div>

    <div>
        <label for="kondisi_barang" class="label">Kondisi Barang <span class="text-rose-500">*</span></label>
        <input type="text" name="kondisi_barang" id="kondisi_barang" required list="daftar-kondisi" class="input"
               value="{{ old('kondisi_barang', $barang?->kondisi_barang) }}" placeholder="baru">
        <datalist id="daftar-kondisi">
            <option value="baru"></option>
            <option value="bekas"></option>
            <option value="cacat pabrik"></option>
        </datalist>
    </div>

    <div>
        <label for="qty" class="label">Qty <span class="text-rose-500">*</span></label>
        <input type="number" name="qty" id="qty" required min="0" step="1" class="input font-mono"
               value="{{ old('qty', $barang?->qty) }}" placeholder="0">
    </div>

    <div>
        <label for="sisa_stok" class="label">Sisa Stok</label>
        <input type="number" name="sisa_stok" id="sisa_stok" readonly tabindex="-1" min="0" step="1"
               class="input cursor-not-allowed bg-slate-100 font-mono text-slate-500"
               value="{{ old('sisa_stok', $barang?->sisa_stok) }}">
        <p class="mt-1 text-xs text-slate-500">Keterangan otomatis, dihitung dari stok masuk dikurangi terjual & keluar.</p>
    </div>

    <div>
        <label for="keluar" class="label">Keluar</label>
        <input type="number" name="keluar" id="keluar" readonly tabindex="-1" min="0" step="1"
               class="input cursor-not-allowed bg-slate-100 font-mono text-slate-500"
               value="{{ old('keluar', $barang?->keluar ?? 0) }}">
        <p class="mt-1 text-xs text-slate-500">Jumlah barang yang diambil user penjual untuk dijual.</p>
    </div>

    <div>
        <label for="terjual" class="label">Terjual</label>
        <input type="number" name="terjual" id="terjual" readonly tabindex="-1" min="0" step="1"
               class="input cursor-not-allowed bg-slate-100 font-mono text-slate-500"
               value="{{ old('terjual', $barang?->terjual ?? 0) }}">
        <p class="mt-1 text-xs text-slate-500">Otomatis, bertambah saat penjual mencatat barang laku.</p>
    </div>

    <div>
        <label for="harga_gudang" class="label">Harga Gudang <span class="text-rose-500">*</span></label>
        <input type="number" name="harga_gudang" id="harga_gudang" required min="0" step="1" class="input font-mono"
               value="{{ old('harga_gudang', $barang?->harga_gudang) }}" placeholder="0">
    </div>

    <div>
        <label for="harga_jual" class="label">Harga Jual <span class="text-rose-500">*</span></label>
        <input type="number" name="harga_jual" id="harga_jual" required min="0" step="1" class="input font-mono"
               value="{{ old('harga_jual', $barang?->harga_jual) }}" placeholder="0">
    </div>

    <div>
        <label for="margin_kotor" class="label">Margin Kotor</label>
        <input type="number" name="margin_kotor" id="margin_kotor" readonly tabindex="-1" min="0" step="1"
               class="input cursor-not-allowed bg-slate-100 font-mono text-slate-500" value="{{ old('margin_kotor', $barang?->margin_kotor) }}" placeholder="0">
    </div>

    <div>
        <label for="margin_persen" class="label">Margin (persen)</label>
        <input type="number" name="margin_persen" id="margin_persen" readonly tabindex="-1" min="0" max="100" step="0.01"
               class="input cursor-not-allowed bg-slate-100 font-mono text-slate-500" value="{{ old('margin_persen', $barang?->margin_persen) }}" placeholder="0.00">
    </div>
    <p class="text-xs text-slate-500 md:col-span-2">Margin dihitung otomatis dari harga jual dikurangi harga gudang.</p>
</div>

@push('scripts')
    <script>
        (function () {
            const jenisInput = document.getElementById('jenis_barang');
            const kodeInput = document.getElementById('kode_produk');
            const hintKode = document.getElementById('hint-kode');

            if (!jenisInput || !kodeInput) {
                return;
            }

            const jenisAsli = jenisInput.dataset.jenisAsli || null;
            const kodeAsli = kodeInput.value;
            const daftarJenis = @json(\App\Models\Barang::jenisOtomatis());

            async function isiKode(jenis) {
                if (jenis === '') {
                    kodeInput.value = '';
                    return;
                }

                if (jenis === jenisAsli && kodeAsli) {
                    kodeInput.value = kodeAsli;
                    hintKode.textContent = 'Kode dibuat otomatis dari jenis barang.';
                    return;
                }

                if (!daftarJenis.includes(jenis)) {
                    kodeInput.value = '';
                    hintKode.textContent = 'Jenis tidak dikenali untuk kode otomatis. Kode wajib diisi manual.';
                    return;
                }

                try {
                    const res = await fetch('{{ route('barang.kode') }}?jenis=' + encodeURIComponent(jenis));
                    const data = await res.json();
                    if (data.tersedia && data.kode) {
                        kodeInput.value = data.kode;
                    }
                } catch (e) {
                    // biarkan kosong
                }
            }

            jenisInput.addEventListener('change', function () {
                isiKode(jenisInput.value.trim());
            });

            if (jenisInput.value.trim() !== '') {
                isiKode(jenisInput.value.trim());
            }
        })();

        (function () {
            const hargaGudang = document.getElementById('harga_gudang');
            const hargaJual = document.getElementById('harga_jual');
            const marginKotor = document.getElementById('margin_kotor');
            const marginPersen = document.getElementById('margin_persen');

            if (!hargaGudang || !hargaJual || !marginKotor || !marginPersen) {
                return;
            }

            function hitungMargin() {
                const gudang = parseFloat(hargaGudang.value) || 0;
                const jual = parseFloat(hargaJual.value) || 0;
                const kotor = Math.max(0, jual - gudang);
                marginKotor.value = kotor;
                marginPersen.value = gudang > 0 ? (kotor / gudang * 100).toFixed(2) : '0.00';
            }

            hargaGudang.addEventListener('input', hitungMargin);
            hargaJual.addEventListener('input', hitungMargin);
            hitungMargin();
        })();
    </script>
@endpush