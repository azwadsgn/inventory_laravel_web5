@extends('layouts.app')

@section('title', 'Laporan Keuangan')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Laporan Keuangan</h1>
        <p class="page-sub">Ringkasan pendapatan &amp; transaksi penjualan · {{ $dateLabel }}</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('laporan.print', array_merge(['period' => $period], array_filter(['filter_date' => $filterDate, 'filter_month' => $filterMonth, 'filter_year' => $filterYear]))) }}"
           target="_blank" class="btn btn-secondary">
            <i class="bi bi-printer"></i> Cetak Laporan
        </a>
    </div>
</div>

{{-- ====== FILTER PERIODE ====== --}}
<form method="GET" action="{{ route('laporan.index') }}" id="filter-form"
      style="background:white;border:1px solid #e5e7eb;border-radius:.75rem;padding:1.25rem;margin-bottom:1.5rem;box-shadow:0 1px 2px rgba(0,0,0,.05);">

    <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.875rem;flex-wrap:wrap;">
        <span style="font-size:.8rem;font-weight:600;color:#374151;">Filter Periode:</span>

        <button type="button" class="period-btn {{ $period === 'all'     ? 'active' : '' }}" onclick="setPeriod('all')">Semua</button>
        <button type="button" class="period-btn {{ $period === 'daily'   ? 'active' : '' }}" onclick="setPeriod('daily')">Harian</button>
        <button type="button" class="period-btn {{ $period === 'monthly' ? 'active' : '' }}" onclick="setPeriod('monthly')">Bulanan</button>
        <button type="button" class="period-btn {{ $period === 'yearly'  ? 'active' : '' }}" onclick="setPeriod('yearly')">Tahunan</button>

        <input type="hidden" name="period" id="period-input" value="{{ $period }}">
    </div>

    {{-- Harian --}}
    <div id="filter-daily" style="display:{{ $period==='daily' ? 'flex' : 'none' }};align-items:center;gap:.625rem;flex-wrap:wrap;">
        <label style="font-size:.8rem;font-weight:600;color:#374151;">Tanggal:</label>
        <input type="date" name="filter_date" class="form-control" style="width:auto;min-width:180px;"
               value="{{ $filterDate ?? date('Y-m-d') }}">
        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i> Terapkan</button>
    </div>

    {{-- Bulanan --}}
    <div id="filter-monthly" style="display:{{ $period==='monthly' ? 'flex' : 'none' }};align-items:center;gap:.625rem;flex-wrap:wrap;">
        <label style="font-size:.8rem;font-weight:600;color:#374151;">Bulan:</label>
        <select name="filter_month" class="select-input">
            @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $mi => $mn)
            <option value="{{ $mi+1 }}" {{ ($filterMonth ?? date('n')) == ($mi+1) ? 'selected' : '' }}>{{ $mn }}</option>
            @endforeach
        </select>
        <select name="filter_year" class="select-input">
            @foreach($yearOptions as $y)
            <option value="{{ $y }}" {{ ($filterYear ?? date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i> Terapkan</button>
    </div>

    {{-- Tahunan --}}
    <div id="filter-yearly" style="display:{{ $period==='yearly' ? 'flex' : 'none' }};align-items:center;gap:.625rem;flex-wrap:wrap;">
        <label style="font-size:.8rem;font-weight:600;color:#374151;">Tahun:</label>
        <select name="filter_year" class="select-input">
            @foreach($yearOptions as $y)
            <option value="{{ $y }}" {{ ($filterYear ?? date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-funnel"></i> Terapkan</button>
    </div>
</form>

{{-- ====== SUMMARY CARDS ====== --}}
<div class="stats-grid" style="margin-bottom:1.5rem;">

    <div class="stat-card yellow">
        <div class="stat-label">Total Pendapatan</div>
        <div class="stat-value" style="font-size:1.5rem;">
            <span class="unit">Rp</span> {{ number_format($totalPendapatan, 0, ',', '.') }}
        </div>
        <div class="stat-sub">{{ $dateLabel }}</div>
    </div>

    <div class="stat-card green">
        <div class="stat-label">Transaksi Penjualan</div>
        <div class="stat-value">{{ number_format($totalTransaksi, 0, ',', '.') }}</div>
        <div class="stat-sub">Total transaksi keluar</div>
    </div>

    <div class="stat-card blue">
        <div class="stat-label">Item Terjual</div>
        <div class="stat-value">{{ number_format($totalItemTerjual, 0, ',', '.') }} <span class="unit">pcs</span></div>
        <div class="stat-sub">Total unit barang keluar</div>
    </div>

    <div class="stat-card purple">
        <div class="stat-label">Rata-rata per Transaksi</div>
        <div class="stat-value" style="font-size:1.35rem;">
            <span class="unit" style="font-size:.85rem;">Rp</span> {{ number_format($rataRata, 0, ',', '.') }}
        </div>
        <div class="stat-sub">Nilai rata-rata penjualan</div>
    </div>
</div>

{{-- ====== GRAFIK + TOP PRODUK ====== --}}
<div class="charts-grid" style="margin-bottom:1.5rem;">

    {{-- Grafik Pendapatan --}}
    <div class="card">
        <div class="card-header">
            <span class="fw-600" style="font-size:.9rem;"><i class="bi bi-graph-up-arrow text-green"></i> &nbsp;Grafik Pendapatan</span>
            <span class="text-green" style="font-size:.8rem;">{{ $dateLabel }}</span>
        </div>
        <div class="card-body">
            @if(array_sum($chartData) > 0)
                <canvas id="revenueChart" height="220"></canvas>
            @else
                <div class="empty-state" style="padding:2rem 1rem;">
                    <span class="empty-icon">📊</span>
                    <p>Belum ada data pendapatan untuk periode ini.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Top Produk berdasarkan Pendapatan --}}
    <div class="card">
        <div class="card-header">
            <span class="fw-600" style="font-size:.9rem;"><i class="bi bi-trophy text-green"></i> &nbsp;Produk Terlaris</span>
            <span class="text-green" style="font-size:.8rem;">Top 5</span>
        </div>
        <div class="card-body" style="padding:.75rem;">
            @if(count($topProduk) > 0)
            @php $maxPendapatan = collect($topProduk)->max('pendapatan'); @endphp
            @foreach($topProduk as $i => $p)
            <div style="margin-bottom:1rem;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.3rem;">
                    <div style="display:flex;align-items:center;gap:.5rem;">
                        <div style="width:22px;height:22px;border-radius:50%;background:{{ ['#16a34a','#22c55e','#4ade80','#86efac','#bbf7d0'][$i] }};color:{{ $i<2?'white':'#166534' }};display:flex;align-items:center;justify-content:center;font-size:.7rem;font-weight:700;flex-shrink:0;">{{ $i+1 }}</div>
                        <span style="font-size:.82rem;font-weight:600;color:#1f2937;">{{ $p['nama'] }}</span>
                    </div>
                    <span style="font-size:.78rem;font-weight:700;color:#16a34a;">Rp {{ number_format($p['pendapatan'], 0, ',', '.') }}</span>
                </div>
                <div style="height:6px;background:#e5e7eb;border-radius:3px;overflow:hidden;">
                    <div style="width:{{ $maxPendapatan > 0 ? round(($p['pendapatan']/$maxPendapatan)*100) : 0 }}%;height:100%;background:{{ ['#16a34a','#22c55e','#4ade80','#86efac','#bbf7d0'][$i] }};border-radius:3px;"></div>
                </div>
                <div style="font-size:.72rem;color:#9ca3af;margin-top:.2rem;">{{ $p['qty'] }} pcs terjual · {{ $p['kategori'] }}</div>
            </div>
            @endforeach
            @else
            <div class="empty-state" style="padding:2rem 1rem;">
                <span class="empty-icon">🏆</span>
                <p>Belum ada data penjualan.</p>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- ====== TABEL DETAIL TRANSAKSI ====== --}}
<div class="card">
    <div class="card-header">
        <span class="fw-600" style="font-size:.9rem;"><i class="bi bi-table"></i> &nbsp;Detail Transaksi Penjualan</span>
        <span class="badge badge-info">{{ $transactions->count() }} transaksi</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>SKU &amp; Produk</th>
                    <th>Kategori</th>
                    <th style="text-align:center;">Qty</th>
                    <th style="text-align:right;">Harga Satuan</th>
                    <th style="text-align:right;">Subtotal</th>
                    <th>Operator</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                @php $subtotal = $trx->quantity * ($trx->product->price ?? 0); @endphp
                <tr>
                    <td style="white-space:nowrap;">
                        <div style="font-size:.82rem;font-weight:500;">{{ $trx->created_at->format('d/m/Y') }}</div>
                        <div style="font-size:.75rem;color:#9ca3af;">{{ $trx->created_at->format('H:i') }}</div>
                    </td>
                    <td>
                        <div class="fw-600" style="font-size:.875rem;">{{ $trx->product->name ?? 'Dihapus' }}</div>
                        <div style="font-size:.72rem;color:#9ca3af;font-family:monospace;">{{ $trx->product->sku ?? '-' }}</div>
                    </td>
                    <td><span class="badge badge-info">{{ $trx->product->category->name ?? '-' }}</span></td>
                    <td style="text-align:center;" class="fw-600 text-red">-{{ $trx->quantity }} pcs</td>
                    <td style="text-align:right;">Rp {{ number_format($trx->product->price ?? 0, 0, ',', '.') }}</td>
                    <td style="text-align:right;" class="fw-600 text-green">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                    <td style="font-size:.82rem;">{{ $trx->user->name ?? '-' }}</td>
                    <td style="font-size:.82rem;color:#6b7280;">{{ $trx->notes ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-state">
                            <span class="empty-icon">💰</span>
                            <p>Belum ada transaksi penjualan untuk periode ini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($transactions->count() > 0)
            <tfoot>
                <tr style="background:#f0fdf4;border-top:2px solid #16a34a;">
                    <td colspan="5" style="padding:.875rem 1rem;font-weight:700;color:#374151;font-size:.875rem;">
                        TOTAL PENDAPATAN
                    </td>
                    <td style="padding:.875rem 1rem;text-align:right;font-weight:700;color:#16a34a;font-size:1rem;">
                        Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                    </td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
@php
    // Precompute chart colors di PHP agar tidak error di @json()
    $bgColors = array_map(function($v) {
        return $v > 0 ? 'rgba(22,163,74,0.75)' : 'rgba(22,163,74,0.15)';
    }, $chartData);
    $hasData  = array_sum($chartData) > 0;
@endphp
<script>
    // ── Period filter ──
    function setPeriod(period) {
        document.getElementById('period-input').value = period;
        document.querySelectorAll('.period-btn').forEach(b => b.classList.remove('active'));
        event.currentTarget.classList.add('active');
        ['daily','monthly','yearly'].forEach(p => {
            document.getElementById('filter-' + p).style.display = 'none';
        });
        if (period !== 'all') {
            document.getElementById('filter-' + period).style.display = 'flex';
        } else {
            document.getElementById('filter-form').submit();
        }
    }

    // ── Chart ──
    @if($hasData)
    (function() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(22,163,74,0.25)');
        gradient.addColorStop(1, 'rgba(22,163,74,0.02)');

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartLabels),
                datasets: [{
                    label: 'Pendapatan',
                    data: @json($chartData),
                    backgroundColor: @json($bgColors),
                    borderRadius: 5,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: ctx => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6' },
                        ticks: {
                            callback: function(val) { return val >= 1000000 ? 'Rp ' + (val/1000000).toFixed(1) + 'jt' : 'Rp ' + val.toLocaleString('id-ID'); }
                        }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    })();
    @endif
</script>
@endsection
