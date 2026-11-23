@extends('layouts.app')

@section('title', 'Histori Stok')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Histori Transaksi Stok</h1>
        <p class="page-sub">Semua riwayat barang masuk dan keluar</p>
    </div>
    <div class="page-actions">
        <button class="btn btn-primary" onclick="openModal('modal-barang-masuk')">
            <i class="bi bi-arrow-down-circle"></i> + Barang Masuk
        </button>
        <button class="btn btn-secondary" onclick="openModal('modal-barang-keluar')" style="border-color:#fca5a5;color:#dc2626;">
            <i class="bi bi-arrow-up-circle"></i> + Barang Keluar
        </button>
        @if(Auth::user()->isAdmin())
        <a href="{{ route('stock.print', array_merge(['period' => $period], request()->only('filter_date','filter_month','filter_year'))) }}"
           target="_blank" class="btn btn-secondary">
            <i class="bi bi-printer"></i> Cetak
        </a>
        @endif
    </div>
</div>

{{-- ====== FILTER PERIODE ====== --}}
<form method="GET" action="{{ route('stock.index') }}" id="filter-form">

    {{-- Tombol pilih periode --}}
    <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.875rem;flex-wrap:wrap;">
        <span class="text-muted" style="font-size:.8rem;font-weight:500;">Periode:</span>

        <button type="button"
            class="period-btn {{ $period === 'all' ? 'active' : '' }}"
            onclick="setPeriod('all')">Semua</button>

        <button type="button"
            class="period-btn {{ $period === 'daily' ? 'active' : '' }}"
            onclick="setPeriod('daily')">Harian</button>

        <button type="button"
            class="period-btn {{ $period === 'monthly' ? 'active' : '' }}"
            onclick="setPeriod('monthly')">Bulanan</button>

        <button type="button"
            class="period-btn {{ $period === 'yearly' ? 'active' : '' }}"
            onclick="setPeriod('yearly')">Tahunan</button>

        <input type="hidden" name="period" id="period-input" value="{{ $period }}">
    </div>

    {{-- Dropdown tambahan (muncul sesuai periode) --}}

    {{-- Harian: pilih tanggal --}}
    <div id="filter-daily" style="display:{{ $period === 'daily' ? 'flex' : 'none' }};align-items:center;gap:.625rem;margin-bottom:.875rem;flex-wrap:wrap;">
        <label style="font-size:.8rem;font-weight:600;color:#374151;">Pilih Tanggal:</label>
        <input
            type="date"
            name="filter_date"
            id="filter_date"
            class="form-control"
            style="width:auto;min-width:180px;"
            value="{{ $filterDate ?? date('Y-m-d') }}"
        >
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="bi bi-funnel"></i> Terapkan
        </button>
    </div>

    {{-- Bulanan: pilih bulan + tahun --}}
    <div id="filter-monthly" style="display:{{ $period === 'monthly' ? 'flex' : 'none' }};align-items:center;gap:.625rem;margin-bottom:.875rem;flex-wrap:wrap;">
        <label style="font-size:.8rem;font-weight:600;color:#374151;">Pilih Bulan:</label>
        <select name="filter_month" class="select-input">
            @foreach(['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'] as $mi => $mn)
            <option value="{{ $mi + 1 }}" {{ ($filterMonth ?? date('n')) == ($mi + 1) ? 'selected' : '' }}>
                {{ $mn }}
            </option>
            @endforeach
        </select>

        <select name="filter_year" id="filter_year_monthly" class="select-input">
            @foreach($yearOptions as $y)
            <option value="{{ $y }}" {{ ($filterYear ?? date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-primary btn-sm">
            <i class="bi bi-funnel"></i> Terapkan
        </button>
    </div>

    {{-- Tahunan: pilih tahun --}}
    <div id="filter-yearly" style="display:{{ $period === 'yearly' ? 'flex' : 'none' }};align-items:center;gap:.625rem;margin-bottom:.875rem;flex-wrap:wrap;">
        <label style="font-size:.8rem;font-weight:600;color:#374151;">Pilih Tahun:</label>
        <select name="filter_year" id="filter_year_yearly" class="select-input">
            @foreach($yearOptions as $y)
            <option value="{{ $y }}" {{ ($filterYear ?? date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-primary btn-sm">
            <i class="bi bi-funnel"></i> Terapkan
        </button>
    </div>

</form>

{{-- Info periode aktif + jumlah data --}}
<div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1.25rem;flex-wrap:wrap;">
    <span class="badge badge-success" style="font-size:.78rem;">
        <i class="bi bi-calendar3"></i> {{ $dateLabel }}
    </span>
    <span class="badge badge-info" style="font-size:.75rem;">
        {{ $transactions->count() }} transaksi
    </span>
    @if($period !== 'all')
    <a href="{{ route('stock.index') }}" class="text-muted" style="font-size:.78rem;text-decoration:none;">
        <i class="bi bi-x-circle"></i> Reset filter
    </a>
    @endif
</div>

{{-- TABLE --}}
<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Tipe</th>
                    <th>SKU &amp; Produk</th>
                    <th style="text-align:center;">Jumlah</th>
                    <th>Operator</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($transactions as $trx)
                <tr>
                    <td style="white-space:nowrap;">
                        <div style="font-size:.82rem;color:#374151;font-weight:500;">
                            {{ $trx->created_at->format('d/m/Y') }}
                        </div>
                        <div style="font-size:.75rem;color:#9ca3af;">
                            {{ $trx->created_at->format('H:i') }}
                        </div>
                    </td>
                    <td>
                        @if($trx->type === 'in')
                        <span class="badge badge-success">
                            <i class="bi bi-arrow-down-circle"></i> Masuk
                        </span>
                        @else
                        <span class="badge badge-danger">
                            <i class="bi bi-arrow-up-circle"></i> Keluar
                        </span>
                        @endif
                    </td>
                    <td>
                        <div class="fw-600" style="font-size:.875rem;">{{ $trx->product->name ?? 'Produk Dihapus' }}</div>
                        <div style="font-size:.75rem;color:#9ca3af;font-family:monospace;">
                            {{ $trx->product->sku ?? '-' }}
                        </div>
                    </td>
                    <td style="text-align:center;">
                        <span class="fw-600 {{ $trx->type === 'in' ? 'text-green' : 'text-red' }}">
                            {{ $trx->type === 'in' ? '+' : '-' }}{{ $trx->quantity }}
                        </span>
                        <span class="text-muted" style="font-size:.75rem;">pcs</span>
                    </td>
                    <td style="font-size:.82rem;">{{ $trx->user->name ?? '-' }}</td>
                    <td style="font-size:.82rem;color:#6b7280;max-width:200px;">
                        {{ $trx->notes ?? '-' }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <span class="empty-icon">📋</span>
                            <p>Belum ada histori transaksi untuk periode ini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ====== MODAL BARANG MASUK ====== --}}
<div class="modal-overlay" id="modal-barang-masuk">
    <div class="modal-box">
        <div class="modal-header">
            <h3 style="color:#16a34a;"><i class="bi bi-arrow-down-circle"></i> &nbsp;Input Barang Masuk</h3>
            <button class="modal-close" onclick="closeModal('modal-barang-masuk')">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('stock.in') }}">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="in_product">Produk <span style="color:#ef4444">*</span></label>
                    <select id="in_product" name="product_id" class="form-control select-input" style="width:100%;padding:.55rem .875rem;" required>
                        <option value="">-- Pilih Produk --</option>
                        @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} (Stok: {{ $p->stock }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="in_qty">Jumlah Masuk <span style="color:#ef4444">*</span></label>
                    <input type="number" id="in_qty" name="quantity" class="form-control" min="1" placeholder="cth: 50" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="in_notes">Keterangan</label>
                    <input type="text" id="in_notes" name="notes" class="form-control" placeholder="cth: Restok dari supplier">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-barang-masuk')">Batal</button>
                <button type="submit" class="btn btn-primary" style="background:#16a34a;">
                    <i class="bi bi-check-lg"></i> Catat Masuk
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ====== MODAL BARANG KELUAR ====== --}}
<div class="modal-overlay" id="modal-barang-keluar">
    <div class="modal-box">
        <div class="modal-header">
            <h3 style="color:#dc2626;"><i class="bi bi-arrow-up-circle"></i> &nbsp;Input Barang Keluar</h3>
            <button class="modal-close" onclick="closeModal('modal-barang-keluar')">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('stock.out') }}">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="out_product">Produk <span style="color:#ef4444">*</span></label>
                    <select id="out_product" name="product_id" class="form-control select-input" style="width:100%;padding:.55rem .875rem;" required>
                        <option value="">-- Pilih Produk --</option>
                        @foreach($products as $p)
                        <option value="{{ $p->id }}">{{ $p->name }} (Stok: {{ $p->stock }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="out_qty">Jumlah Keluar <span style="color:#ef4444">*</span></label>
                    <input type="number" id="out_qty" name="quantity" class="form-control" min="1" placeholder="cth: 5" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="out_notes">Keterangan</label>
                    <input type="text" id="out_notes" name="notes" class="form-control" placeholder="cth: Terjual ke pelanggan">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-barang-keluar')">Batal</button>
                <button type="submit" class="btn btn-primary" style="background:#dc2626;">
                    <i class="bi bi-check-lg"></i> Catat Keluar
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // ==============================
    // Modal helpers
    // ==============================
    function openModal(id) {
        document.getElementById(id).classList.add('open');
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
    }
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) closeModal(this.id);
        });
    });

    // ==============================
    // Period filter logic
    // ==============================
    function setPeriod(period) {
        // Update hidden input
        document.getElementById('period-input').value = period;

        // Update tombol aktif
        document.querySelectorAll('.period-btn').forEach(btn => btn.classList.remove('active'));
        event.currentTarget.classList.add('active');

        // Sembunyikan semua panel filter
        document.getElementById('filter-daily').style.display   = 'none';
        document.getElementById('filter-monthly').style.display = 'none';
        document.getElementById('filter-yearly').style.display  = 'none';

        // Tampilkan panel sesuai periode
        if (period === 'daily')   document.getElementById('filter-daily').style.display   = 'flex';
        if (period === 'monthly') document.getElementById('filter-monthly').style.display = 'flex';
        if (period === 'yearly')  document.getElementById('filter-yearly').style.display  = 'flex';

        // Jika pilih "Semua" → langsung submit
        if (period === 'all') {
            document.getElementById('filter-form').submit();
        }
    }
</script>
@endsection
