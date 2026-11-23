<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Histori Stok — Kios Sembako Azwa</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #fff;
            color: #1f2937;
            font-size: 12px;
            line-height: 1.5;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Header */
        .print-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #16a34a;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.625rem;
        }

        .brand-logo {
            width: 42px;
            height: 42px;
            background: #16a34a;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 0.75rem;
        }

        .brand-name { font-weight: 700; font-size: 1rem; color: #111827; }
        .brand-sub  { font-size: 0.7rem; color: #6b7280; }

        .print-meta { text-align: right; }
        .print-meta .doc-title { font-weight: 700; font-size: 0.9rem; color: #111827; }
        .print-meta .doc-sub   { font-size: 0.75rem; color: #6b7280; margin-top: 0.15rem; }

        /* Summary */
        .summary-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .summary-box {
            flex: 1;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
        }

        .summary-box .s-label {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #9ca3af;
            margin-bottom: 0.25rem;
        }

        .summary-box .s-value {
            font-size: 1rem;
            font-weight: 700;
            color: #111827;
        }

        .summary-box.green .s-value { color: #16a34a; }
        .summary-box.red .s-value   { color: #dc2626; }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5rem;
        }

        thead th {
            background: #f3f4f6;
            padding: 0.625rem 0.75rem;
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #6b7280;
            border: 1px solid #e5e7eb;
            text-align: left;
        }

        tbody td {
            padding: 0.55rem 0.75rem;
            border: 1px solid #e5e7eb;
            color: #374151;
            vertical-align: middle;
        }

        tbody tr:nth-child(even) td { background: #f9fafb; }

        .badge-in  { color: #15803d; font-weight: 700; }
        .badge-out { color: #b91c1c; font-weight: 700; }

        /* Footer */
        .print-footer {
            display: flex;
            justify-content: space-between;
            padding-top: 1rem;
            border-top: 1px solid #e5e7eb;
            font-size: 0.72rem;
            color: #9ca3af;
        }

        /* Print button (hidden on print) */
        .no-print {
            position: fixed;
            top: 1rem;
            right: 1rem;
            display: flex;
            gap: 0.5rem;
        }

        .btn-print {
            padding: 0.5rem 1.25rem;
            background: #16a34a;
            color: white;
            border: none;
            border-radius: 0.375rem;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-back {
            padding: 0.5rem 1rem;
            background: white;
            color: #374151;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
            font-size: 0.85rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
        }

        @media print {
            .no-print { display: none !important; }
            body { font-size: 11px; }
            .container { padding: 0.5rem 1rem; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <a href="{{ route('stock.index') }}" class="btn-back">← Kembali</a>
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
        <div class="print-meta">
            <div class="doc-title">LAPORAN HISTORI TRANSAKSI STOK</div>
            <div class="doc-sub">Periode: {{ $dateLabel }}</div>
            <div class="doc-sub">Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB</div>
            <div class="doc-sub">Oleh: {{ Auth::user()->name }} (Admin)</div>
        </div>
    </div>

    {{-- SUMMARY --}}
    @php
        $totalIn  = $transactions->where('type', 'in')->sum('quantity');
        $totalOut = $transactions->where('type', 'out')->sum('quantity');
        $totalRev = $transactions->where('type', 'out')->sum(fn($t) => $t->quantity * ($t->product->price ?? 0));
    @endphp
    <div class="summary-row">
        <div class="summary-box">
            <div class="s-label">Total Transaksi</div>
            <div class="s-value">{{ $transactions->count() }}</div>
        </div>
        <div class="summary-box green">
            <div class="s-label">Total Barang Masuk</div>
            <div class="s-value">{{ number_format($totalIn, 0, ',', '.') }} pcs</div>
        </div>
        <div class="summary-box red">
            <div class="s-label">Total Barang Keluar</div>
            <div class="s-value">{{ number_format($totalOut, 0, ',', '.') }} pcs</div>
        </div>
        <div class="summary-box">
            <div class="s-label">Total Pendapatan</div>
            <div class="s-value">Rp {{ number_format($totalRev, 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- TABLE --}}
    <table>
        <thead>
            <tr>
                <th style="width:30px;">No</th>
                <th>Waktu</th>
                <th>Tipe</th>
                <th>SKU</th>
                <th>Nama Produk</th>
                <th style="text-align:center;">Jumlah</th>
                <th style="text-align:right;">Harga Satuan</th>
                <th style="text-align:right;">Subtotal</th>
                <th>Operator</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $i => $trx)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td style="white-space:nowrap;">{{ $trx->created_at->format('d/m/Y H:i') }}</td>
                <td>
                    @if($trx->type === 'in')
                        <span class="badge-in">↓ Masuk</span>
                    @else
                        <span class="badge-out">↑ Keluar</span>
                    @endif
                </td>
                <td style="font-family:monospace;font-size:.85em;">{{ $trx->product->sku ?? '-' }}</td>
                <td style="font-weight:600;">{{ $trx->product->name ?? 'Dihapus' }}</td>
                <td style="text-align:center;font-weight:700;">
                    {{ $trx->type === 'in' ? '+' : '-' }}{{ $trx->quantity }}
                </td>
                <td style="text-align:right;">
                    Rp {{ number_format($trx->product->price ?? 0, 0, ',', '.') }}
                </td>
                <td style="text-align:right;font-weight:600;">
                    @if($trx->type === 'out')
                    Rp {{ number_format($trx->quantity * ($trx->product->price ?? 0), 0, ',', '.') }}
                    @else
                    —
                    @endif
                </td>
                <td>{{ $trx->user->name ?? '-' }}</td>
                <td>{{ $trx->notes ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="10" style="text-align:center;padding:1.5rem;color:#9ca3af;">
                    Tidak ada transaksi untuk periode ini.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- SIGNATURE --}}
    <div style="display:flex;justify-content:flex-end;margin-bottom:2rem;">
        <div style="text-align:center;min-width:180px;">
            <div style="font-size:.75rem;color:#6b7280;margin-bottom:3rem;">
                {{ now()->translatedFormat('d F Y') }}
            </div>
            <div style="border-top:1px solid #374151;padding-top:.25rem;font-size:.75rem;font-weight:600;">
                {{ Auth::user()->name }}
            </div>
            <div style="font-size:.7rem;color:#9ca3af;">Admin Kios Sembako Azwa</div>
        </div>
    </div>

    {{-- FOOTER --}}
    <div class="print-footer">
        <span>Kios Sembako Azwa — Sistem Manajemen Inventori</span>
        <span>Dokumen ini dicetak secara otomatis oleh sistem</span>
    </div>

</div>

<script>
    // Auto-print saat halaman dibuka
    window.onload = function () {
        // Uncomment baris di bawah jika ingin auto-print langsung
        // window.print();
    };
</script>
</body>
</html>
