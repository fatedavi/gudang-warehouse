<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan {{ $judul }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #1e293b; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px solid #0f766e; padding-bottom: 15px; }
        .header h1 { font-size: 20px; color: #0f766e; margin-bottom: 4px; }
        .header h2 { font-size: 15px; color: #334155; font-weight: normal; }
        .header .date { font-size: 11px; color: #64748b; margin-top: 6px; }
        .summary { display: flex; justify-content: space-between; margin-bottom: 15px; gap: 10px; }
        .summary-box { flex: 1; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 10px; text-align: center; }
        .summary-box .label { font-size: 9px; text-transform: uppercase; color: #94a3b8; letter-spacing: 0.5px; }
        .summary-box .value { font-size: 16px; font-weight: bold; margin-top: 2px; }
        .summary-box.baru .value { color: #0369a1; }
        .summary-box.lama .value { color: #b45309; }
        .summary-box.terjual .value { color: #475569; }
        .summary-box.total .value { color: #0f766e; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        thead th { background: #f1f5f9; border: 1px solid #cbd5e1; padding: 7px 6px; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; color: #475569; text-align: left; }
        thead th.num { text-align: right; }
        tbody td { border: 1px solid #e2e8f0; padding: 6px; font-size: 10px; }
        tbody td.num { text-align: right; font-family: monospace; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody tr:hover { background: #f1f5f9; }
        .status { display: inline-block; padding: 1px 6px; border-radius: 3px; font-size: 9px; font-weight: bold; }
        .status-baru { background: #e0f2fe; color: #0369a1; }
        .status-lama { background: #fef3c7; color: #b45309; }
        .status-campuran { background: #ede9fe; color: #7c3aed; }
        .status-terjual { background: #f1f5f9; color: #475569; }
        .footer { margin-top: 20px; border-top: 2px solid #e2e8f0; padding-top: 10px; display: flex; justify-content: space-between; font-size: 9px; color: #94a3b8; }
        .footer .total-row { font-weight: bold; font-size: 10px; color: #334155; }
        .empty { text-align: center; padding: 40px; color: #94a3b8; font-size: 12px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>GudangFlow</h1>
        <h2>Laporan {{ $judul }}</h2>
        <p class="date">Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
    </div>

    <div class="summary">
        <div class="summary-box total">
            <div class="label">Total Data</div>
            <div class="value">{{ count($barangs) }}</div>
        </div>
        <div class="summary-box total">
            <div class="label">Total Qty</div>
            <div class="value">{{ number_format($barangs->sum('qty'), 0, ',', '.') }}</div>
        </div>
        <div class="summary-box total">
            <div class="label">Total Terjual</div>
            <div class="value">{{ number_format($barangs->sum('terjual'), 0, ',', '.') }}</div>
        </div>
        <div class="summary-box total">
            <div class="label">Total Nilai Jual</div>
            <div class="value">Rp {{ number_format($barangs->sum('harga_jual'), 0, ',', '.') }}</div>
        </div>
    </div>

    @if ($barangs->isEmpty())
        <div class="empty">Tidak ada data untuk ditampilkan.</div>
    @else
        <table>
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Kode Produk</th>
                    <th>Jenis Barang</th>
                    <th>Merk</th>
                    <th>Ukuran</th>
                    <th>Warna</th>
                    <th class="num">Qty</th>
                    <th class="num">Terjual</th>
                    <th class="num">Keluar</th>
                    <th class="num">Sisa Stok</th>
                    <th>Unit Baru / Lama</th>
                    <th class="num">Harga Jual</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($barangs as $i => $barang)
                    <tr>
                        <td style="text-align: center;">{{ $i + 1 }}</td>
                        <td style="font-family: monospace; font-size: 9px;">{{ $barang->kodeTampil() }}</td>
                        <td>{{ $barang->jenis_barang }}</td>
                        <td>{{ $barang->merk_produk }}</td>
                        <td>{{ $barang->ukuran_produk }}</td>
                        <td>{{ $barang->warna_produk }}</td>
                        <td class="num">{{ number_format($barang->qty, 0, ',', '.') }}</td>
                        <td class="num">{{ number_format($barang->terjual, 0, ',', '.') }}</td>
                        <td class="num">{{ number_format($barang->keluar, 0, ',', '.') }}</td>
                        <td class="num" style="font-weight: bold;">{{ number_format($barang->sisa_stok, 0, ',', '.') }}</td>
                        <td>
                            @if ($barang->stokBelumKeluar() > 0)
                                <span style="color: #0369a1;">Baru {{ $barang->stokBelumKeluar() }}</span>
                            @endif
                            @if ($barang->stokBelumKeluar() > 0 && $barang->stokSudahKeluar() > 0)
                                <span style="color: #cbd5e1;"> / </span>
                            @endif
                            @if ($barang->stokSudahKeluar() > 0)
                                <span style="color: #b45309;">Lama {{ $barang->stokSudahKeluar() }}</span>
                            @endif
                        </td>
                        <td class="num">Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</td>
                        <td>
                            @php $st = $barang->status; @endphp
                            <span class="status status-{{ $st }}">
                                {{ match($st) { 'baru' => 'BARU', 'lama' => 'LAMA', 'campuran' => 'BARU + LAMA', default => 'TERJUAL' } }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        <div>GudangFlow &mdash; Sistem Manajemen Gudang</div>
        <div class="total-row">Total: {{ count($barangs) }} item</div>
    </div>
</body>
</html>
