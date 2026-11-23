@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

{{-- PAGE HEADER --}}
<div class="flex-between" style="margin-bottom:1.5rem;">
    <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-sub">
            Kios Sembako Azwa &middot;
            {{ $dateLabel }}
        </p>
    </div>

    {{-- FILTER PERIODE --}}
    <div class="period-filter">
        <a href="{{ route('dashboard', ['period' => 'all']) }}"
           class="period-btn {{ $period === 'all' ? 'active' : '' }}">Semua</a>
        <a href="{{ route('dashboard', ['period' => 'daily']) }}"
           class="period-btn {{ $period === 'daily' ? 'active' : '' }}">Harian</a>
        <a href="{{ route('dashboard', ['period' => 'monthly']) }}"
           class="period-btn {{ $period === 'monthly' ? 'active' : '' }}">Bulanan</a>
        <a href="{{ route('dashboard', ['period' => 'yearly']) }}"
           class="period-btn {{ $period === 'yearly' ? 'active' : '' }}">Tahunan</a>
    </div>
</div>

{{-- WELCOME BANNER --}}
<div class="welcome-banner">
    <h2>Selamat Datang, {{ Auth::user()->isAdmin() ? 'Admin' : 'Staff' }}!</h2>
    <p>Sistem Manajemen Inventori &middot; Kios Sembako Azwa</p>
    <div class="welcome-badge">
        <i class="bi bi-clock"></i>
        Periode: {{ $dateLabel }}
    </div>
</div>

{{-- STAT CARDS --}}
<div class="stats-grid">

    @if(Auth::user()->isAdmin())
    <div class="stat-card yellow">
        <div class="stat-label">Total Pendapatan</div>
        <div class="stat-value">
            <span class="unit">Rp</span> {{ number_format($totalRevenue, 0, ',', '.') }}
        </div>
        <div class="stat-sub">Dari transaksi barang keluar</div>
    </div>
    @endif

    <div class="stat-card green">
        <div class="stat-label">Item Produk</div>
        <div class="stat-value">{{ $totalProducts }} <span class="unit">jenis</span></div>
        <div class="stat-sub">Total jenis produk terdaftar</div>
    </div>

    <div class="stat-card blue">
        <div class="stat-label">Total Stok Gudang</div>
        <div class="stat-value">{{ number_format($totalStock, 0, ',', '.') }} <span class="unit">pcs</span></div>
        <div class="stat-sub">Ketersediaan semua produk</div>
    </div>

    <div class="stat-card purple">
        <div class="stat-label">Transaksi Stok</div>
        <div class="stat-value">{{ $transactionsIn + $transactionsOut }}</div>
        <div class="stat-sub">
            <span class="badge-in"><i class="bi bi-arrow-down-circle"></i> IN {{ $transactionsIn }}</span>
            &nbsp;
            <span class="badge-out"><i class="bi bi-arrow-up-circle"></i> OUT {{ $transactionsOut }}</span>
        </div>
    </div>
</div>

{{-- CHARTS ROW --}}
<div class="charts-grid">

    @if(Auth::user()->isAdmin())
    <div class="card">
        <div class="card-header">
            <span class="fw-600" style="font-size:.9rem;">Grafik Pendapatan</span>
            <span class="text-green" style="font-size:.8rem;">{{ $dateLabel }}</span>
        </div>
        <div class="card-body">
            @if(array_sum($revenueChartData['data']) > 0)
                <canvas id="revenueChart" height="200"></canvas>
            @else
                <div class="empty-state" style="padding:2rem 1rem;">
                    <span class="empty-icon">📊</span>
                    <p>Belum ada data pendapatan untuk periode ini.</p>
                </div>
            @endif
        </div>
    </div>
    @else
    <div class="card">
        <div class="card-header">
            <span class="fw-600" style="font-size:.9rem;">Grafik Transaksi Stok</span>
            <span class="text-green" style="font-size:.8rem;">{{ $dateLabel }}</span>
        </div>
        <div class="card-body">
            @if(($transactionsIn + $transactionsOut) > 0)
                <canvas id="stockChart" height="200"></canvas>
            @else
                <div class="empty-state" style="padding:2rem 1rem;">
                    <span class="empty-icon">📊</span>
                    <p>Belum ada data transaksi untuk periode ini.</p>
                </div>
            @endif
        </div>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <span class="fw-600" style="font-size:.9rem;">Produk Terlaris</span>
            <span class="text-green" style="font-size:.8rem;">Top 5</span>
        </div>
        <div class="card-body">
            @if(count($topProductsData['data']) > 0)
                <canvas id="topChart" height="250"></canvas>
            @else
                <div class="empty-state" style="padding:2rem 1rem;">
                    <span class="empty-icon">🏆</span>
                    <p>Belum ada data penjualan untuk periode ini.</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- RANKING PRODUK TERLARIS --}}
<div class="card">
    <div class="card-header">
        <span class="fw-600" style="font-size:.9rem;">Ranking Produk Terlaris</span>
        <a href="{{ route('stock.index') }}" class="btn btn-secondary btn-sm">
            <i class="bi bi-list-ul"></i> Lihat Semua
        </a>
    </div>
    @if(count($topProductsData['labels']) > 0)
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:50px;">No</th>
                    <th>Nama Produk</th>
                    <th style="text-align:right;">Terjual</th>
                    <th style="text-align:right;">Persentase</th>
                </tr>
            </thead>
            <tbody>
                @php $totalSold = array_sum($topProductsData['data']); @endphp
                @foreach($topProductsData['labels'] as $i => $name)
                <tr>
                    <td>
                        @php
                            $colors = ['#16a34a','#22c55e','#4ade80','#86efac','#bbf7d0'];
                            $textColors = ['white','white','#166534','#166534','#166534'];
                            $bg = $colors[$i] ?? '#e5e7eb';
                            $tc = $textColors[$i] ?? '#166534';
                        @endphp
                        <div style="width:24px;height:24px;border-radius:50%;background:{{ $bg }};color:{{ $tc }};display:flex;align-items:center;justify-content:center;font-size:.75rem;font-weight:700;">
                            {{ $i + 1 }}
                        </div>
                    </td>
                    <td class="fw-600">{{ $name }}</td>
                    <td style="text-align:right;" class="text-green fw-600">
                        {{ number_format($topProductsData['data'][$i], 0, ',', '.') }} pcs
                    </td>
                    <td style="text-align:right;">
                        @php
                            $pct = $totalSold > 0 ? round(($topProductsData['data'][$i] / $totalSold) * 100) : 0;
                        @endphp
                        <div style="display:flex;align-items:center;justify-content:flex-end;gap:.5rem;">
                            <div style="width:80px;height:6px;background:#e5e7eb;border-radius:3px;overflow:hidden;">
                                <div style="width:{{ $pct }}%;height:100%;background:#16a34a;border-radius:3px;"></div>
                            </div>
                            <span class="text-muted" style="font-size:.8rem;min-width:35px;text-align:right;">{{ $pct }}%</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="empty-state">
        <span class="empty-icon">🏆</span>
        <p>Belum ada data penjualan untuk periode ini.</p>
    </div>
    @endif
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#6b7280';

    const greenGradient = (ctx, area) => {
        const gradient = ctx.createLinearGradient(0, area.top, 0, area.bottom);
        gradient.addColorStop(0, 'rgba(22,163,74,0.25)');
        gradient.addColorStop(1, 'rgba(22,163,74,0.02)');
        return gradient;
    };

    @php $isAdmin = Auth::user()->isAdmin(); @endphp
    @php $hasRevenue = array_sum($revenueChartData['data']) > 0; @endphp
    @php $hasTransactions = ($transactionsIn + $transactionsOut) > 0; @endphp
    @php $hasTopProducts = count($topProductsData['data']) > 0; @endphp

    @if($isAdmin && $hasRevenue)
    (function() {
        const revCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revCtx, {
            type: 'line',
            data: {
                labels: @json($revenueChartData['labels']),
                datasets: [{
                    label: 'Pendapatan',
                    data: @json($revenueChartData['data']),
                    borderColor: '#16a34a',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#16a34a',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    backgroundColor: function(ctx) {
                        const chart = ctx.chart;
                        const {ctx: c, chartArea} = chart;
                        if (!chartArea) return 'rgba(22,163,74,0.1)';
                        return greenGradient(c, chartArea);
                    },
                    tension: 0.4,
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
                            callback: val => 'Rp ' + (val >= 1000000
                                ? (val / 1000000).toFixed(1) + 'jt'
                                : val.toLocaleString('id-ID'))
                        }
                    },
                    x: { grid: { display: false } }
                }
            }
        });
    })();
    @endif

    @if(!$isAdmin && $hasTransactions)
    (function() {
        const stCtx = document.getElementById('stockChart').getContext('2d');
        new Chart(stCtx, {
            type: 'bar',
            data: {
                labels: @json($revenueChartData['labels']),
                datasets: [{
                    label: 'Transaksi',
                    data: @json($revenueChartData['data']),
                    backgroundColor: 'rgba(22,163,74,0.75)',
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f3f4f6' } },
                    x: { grid: { display: false } }
                }
            }
        });
    })();
    @endif

    @if($hasTopProducts)
    (function() {
        const topCtx = document.getElementById('topChart').getContext('2d');
        new Chart(topCtx, {
            type: 'doughnut',
            data: {
                labels: @json($topProductsData['labels']),
                datasets: [{
                    data: @json($topProductsData['data']),
                    backgroundColor: ['#16a34a','#22c55e','#4ade80','#86efac','#bbf7d0'],
                    borderWidth: 0,
                    hoverOffset: 6,
                }]
            },
            options: {
                responsive: true,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, padding: 12, font: { size: 11 } }
                    }
                }
            }
        });
    })();
    @endif
</script>
@endsection
