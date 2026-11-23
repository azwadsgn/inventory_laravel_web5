<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan — Kios Sembako Azwa</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #fff; color: #1f2937; font-size: 12px; line-height: 1.5; }
        .container { max-width: 960px; margin: 0 auto; padding: 2rem; }

        /* Header */
        .print-header { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 2px solid #16a34a; }
        .brand { display: flex; align-items: center; gap: .625rem; }
        .brand-logo { width: 42px; height: 42px; background: #16a34a; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-weight: 800; font-size: .75rem; }
        .brand-name { font-weight: 700; font-size: 1rem; }
        .brand-sub  { font-size: .7rem; color: #6b7280; }
        .doc-title  { font-weight: 700; font-size: .95rem; color: #111827; text-align:right; }
        .doc-sub    { font-size: .72rem; color: #6b7280; text-align:right; margin-top:.15rem; }

        /* Summary */
        .summary-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: .75rem; margin-bottom: 1.5rem; }
        .summary-box { border: 1px solid #e5e7eb; border-radius: .5rem; padding: .75rem; }
        .summary-box .s-label { font-size: .62rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #9ca3af; margin-bottom: .25rem; }
        .summary-box .s-value { font-size: .95rem; font-weight: 700; color: #111827; }
        .summary-box.green .s-value { color: #16a34a; }

        /* Table */
        table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; font-size: 11px; }
        thead th { background: #f3f4f6; padding: .5rem .625rem; font-size: .62rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #6b7280; border: 1px solid #e5e7eb; text-align: left; }
        tbody td { padding: .45rem .625rem; border: 1px solid #e5e7eb; color: #374151; vertical-align: middle; }
        tbody tr:nth-child(even) td { background: #f9fafb; }
        tfoot td { padding: .625rem; font-weight: 700; background: #f0fdf4; border: 1px solid #bbf7d0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-green { color: #16a34a; }
        .text-red   { color: #dc2626; }
        .fw-bold    { font-weight: 700; }

        /* Footer */
        .print-footer { display: flex; justify-content: space-between; padding-top: 1rem; border-top: 1px solid #e5e7eb; font-size: .7rem; color: #9ca3af; margin-top: 1rem; }
        .signature-row { display: flex; justify-content: flex-end; margin-bottom: 2rem; }
        .signature-box { text-align: center; min-width: 180px; }
        .signature-date { font-size: .72rem; color: #6b7280; margin-bottom: 3rem; }
        .signature-name { border-top: 1px solid #374151; padding-top: .25rem; font-size: .75rem; font-weight: 700; }
        .signature-role { font-size: .7rem; color: #9ca3af; }

        /* No-print */
        .no-print { position: fixed; top: 1rem; right: 1rem; display: flex; gap: .5rem; }
        .btn-print { padding: .5rem 1.25rem; background: #16a34a; color: white; border: none; border-radius: .375rem; font-size: .85rem; font-weight: 600; cursor: pointer; }
        .btn-back  { padding: .5rem 1rem; background: white; color: #374151; border: 1px solid #d1d5db; border-radius: .375rem; font-size: .85rem; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; }

        @media print {
            .no-print { display: none !important; }
            body { font-size: 11px; }
            .container { padding: .5rem 1rem; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <a href="{{ route('laporan.index') }}" class="btn-back">← Kembali</a>
    <button class="btn-print" onclick="window.print()">🖨 Cetak Sekarang</button>
</div>

<div class="container">

    {{-- HEADER --}}
    <div class="print-header">
        <div class="brand">
            <div class="brand-logo">KSA</div>
            <div>
                <div class="brand-name">Kios Sembako Azwa</div>
                <div class="brand-sub">Sistem Manajemen Inventori</div>
            </div>
        </div>
        <div>
            <div class="doc-title">LAPORAN KEUANGAN</div>
            <div class="doc-sub">Periode: {{ $dateLabel }}</div>
            <div class="doc-sub">Dicetak: {{ now()->format('d/m/Y H:i') }} WIB</div>
            <div class="doc-sub">Oleh: {{ Auth::user()->name }} (Admin)</div>
        </div>
    </div>

    {{-- SUMMARY --}}
    <div class="summary-row">
        <div class="summary-box green">
            <div class="s-label">Total Pendapatan</div>
            <div class="s-value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
        </div>
        <div class="summary-box">
            <div class="s-label">Transaksi Penjualan</div>
            <div class="s-value">{{ $totalTransaksi }}</div>
        </div>
        <div class="summary-box">
            <div class="s-label">Item Terjual</div>
            <div class="s-value">{{ number_format($totalItemTerjual, 0, ',', '.') }} pcs</div>
        </div>
        <div class="summary-box">
            <div class="s-label">Rata-rata/Transaksi</div>
            <div class="s-value">Rp {{ $totalTransaksi > 0 ? number_format($totalPendapatan / $totalTransaksi, 0, ',', '.') : 0 }}</div>
        </div>
    </div>

    {{-- TABLE --}}
    <table>
        <thead>
            <tr>
                <th style="width:28px;">No</th>
                <th>Waktu</th>
                <th>SKU</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Harga Satuan</th>
                <th class="text-right">Subtotal</th>
                <th>Operator</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $i => $trx)
            @php $subtotal = $trx->quantity * ($trx->product->price ?? 0); @endphp
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td style="white-space:nowrap;">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                <td style="font-family:monospace;">{{ $trx->product->sku ?? '-' }}</td>
                <td class="fw-bold">{{ $trx->product->name ?? 'Dihapus' }}</td>
                <td>{{ $trx->product->category->name ?? '-' }}</td>
                <td class="text-center text-red fw-bold">{{ $trx->quantity }}</td>
                <td class="text-right">Rp {{ number_format($trx->product->price ?? 0, 0, ',', '.') }}</td>
                <td class="text-right fw-bold text-green">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                <td>{{ $trx->user->name ?? '-' }}</td>
                <td>{{ $trx->notes ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" class="text-center" style="padding:1.5rem;color:#9ca3af;">
                    Tidak ada transaksi penjualan untuk periode ini.
                </td>
            </tr>
            @endforelse
        </tbody>
        @if($transactions->count() > 0)
        <tfoot>
            <tr>
                <td colspan="7" style="text-align:right;font-weight:700;color:#374151;">TOTAL PENDAPATAN</td>
                <td class="text-right text-green" style="font-size:1rem;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
        @endif
    </table>

    {{-- SIGNATURE --}}
    <div class="signature-row">
        <div class="signature-box">
            <div class="signature-date">{{ now()->format('d/m/Y') }}</div>
            <div class="signature-name">{{ Auth::user()->name }}</div>
            <div class="signature-role">Admin — Kios Sembako Azwa</div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="print-footer">
        <span>Kios Sembako Azwa — Sistem Manajemen Inventori</span>
        <span>Dokumen ini dicetak secara otomatis oleh sistem</span>
    </div>

</div>
</body>
</html>
