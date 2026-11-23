@extends('layouts.app')

@section('title', 'Daftar Produk')

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">Daftar Produk</h1>
        <p class="page-sub">Manajemen produk kios sembako</p>
    </div>
    <div class="page-actions">
        @if(Auth::user()->isAdmin())
        <button class="btn btn-primary" onclick="openModal('modal-tambah-produk')">
            <i class="bi bi-plus-lg"></i> + Tambah Produk
        </button>
        @endif
    </div>
</div>

{{-- TOOLBAR SEARCH & FILTER --}}
<form method="GET" action="{{ route('products.index') }}" class="toolbar">
    <div class="search-box" style="flex:1; min-width:200px; max-width:320px;">
        <i class="bi bi-search search-icon"></i>
        <input
            type="text"
            name="search"
            class="form-control"
            placeholder="Cari nama atau SKU..."
            value="{{ request('search') }}"
        >
    </div>

    <select name="category_id" class="select-input">
        <option value="all">Semua Kategori</option>
        @foreach($categories as $cat)
        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
            {{ $cat->name }}
        </option>
        @endforeach
    </select>

    <button type="submit" class="btn btn-secondary">
        <i class="bi bi-funnel"></i> Filter
    </button>

    @if(request('search') || request('category_id'))
    <a href="{{ route('products.index') }}" class="btn btn-secondary">
        <i class="bi bi-x"></i> Reset
    </a>
    @endif
</form>

{{-- TABLE --}}
<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>Nama Produk</th>
                    <th>Kategori</th>
                    <th style="text-align:right;">Harga</th>
                    <th style="text-align:center;">Stok</th>
                    @if(Auth::user()->isAdmin())
                    <th style="text-align:center;">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>
                        <span style="font-family:monospace;font-size:.82rem;background:#f3f4f6;padding:.15rem .45rem;border-radius:.25rem;color:#374151;font-weight:600;">
                            {{ $product->sku }}
                        </span>
                    </td>
                    <td class="fw-600">{{ $product->name }}</td>
                    <td>
                        <span class="badge badge-info">{{ $product->category->name ?? '-' }}</span>
                    </td>
                    <td style="text-align:right;" class="fw-600">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </td>
                    <td style="text-align:center;">
                        @if($product->stock <= 0)
                            <span class="badge badge-danger"><i class="bi bi-exclamation-circle"></i> Habis</span>
                        @elseif($product->stock <= 10)
                            <span class="badge badge-warning"><i class="bi bi-exclamation-triangle"></i> {{ $product->stock }}</span>
                        @else
                            <span class="badge badge-success"><i class="bi bi-check-circle"></i> {{ $product->stock }}</span>
                        @endif
                    </td>
                    @if(Auth::user()->isAdmin())
                    <td style="text-align:center;">
                        <div class="action-btns" style="justify-content:center;">
                            <button class="btn btn-secondary btn-sm"
                                onclick="openEditModal(
                                    {{ $product->id }},
                                    '{{ addslashes($product->sku) }}',
                                    '{{ addslashes($product->name) }}',
                                    {{ $product->category_id }},
                                    {{ $product->price }}
                                )">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="{{ route('products.destroy', $product) }}"
                                  onsubmit="return confirm('Hapus produk ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                    @endif
                </tr>
                @empty
                <tr>
                    <td colspan="{{ Auth::user()->isAdmin() ? 6 : 5 }}">
                        <div class="empty-state">
                            <span class="empty-icon">📦</span>
                            <p>Belum ada produk yang ditemukan.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Total count --}}
@if($products->count() > 0)
<p class="text-muted" style="font-size:.8rem;margin-top:.75rem;">
    Menampilkan {{ $products->count() }} produk
</p>
@endif

{{-- ====== MODAL TAMBAH PRODUK (admin only) ====== --}}
@if(Auth::user()->isAdmin())
<div class="modal-overlay" id="modal-tambah-produk">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="bi bi-plus-circle text-green"></i> &nbsp;Tambah Produk Baru</h3>
            <button class="modal-close" onclick="closeModal('modal-tambah-produk')">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('products.store') }}">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="sku">SKU <span style="color:#ef4444">*</span></label>
                    <input type="text" id="sku" name="sku" class="form-control {{ $errors->has('sku') ? 'is-invalid' : '' }}"
                           value="{{ old('sku') }}" placeholder="cth: SKU-001" required>
                    @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="name">Nama Produk <span style="color:#ef4444">*</span></label>
                    <input type="text" id="name" name="name" class="form-control {{ $errors->has('name') ? 'is-invalid' : '' }}"
                           value="{{ old('name') }}" placeholder="cth: Beras Premium 5kg" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="category_id">Kategori <span style="color:#ef4444">*</span></label>
                    <select id="category_id" name="category_id" class="form-control select-input" style="width:100%;padding:.55rem .875rem;" required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                    <div class="form-group">
                        <label class="form-label" for="price">Harga (Rp) <span style="color:#ef4444">*</span></label>
                        <input type="number" id="price" name="price" class="form-control {{ $errors->has('price') ? 'is-invalid' : '' }}"
                               value="{{ old('price') }}" placeholder="0" min="0" step="100" required>
                        @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="stock">Stok Awal <span style="color:#ef4444">*</span></label>
                        <input type="number" id="stock" name="stock" class="form-control {{ $errors->has('stock') ? 'is-invalid' : '' }}"
                               value="{{ old('stock', 0) }}" placeholder="0" min="0" required>
                        @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-tambah-produk')">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Simpan Produk
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ====== MODAL EDIT PRODUK ====== --}}
<div class="modal-overlay" id="modal-edit-produk">
    <div class="modal-box">
        <div class="modal-header">
            <h3><i class="bi bi-pencil text-green"></i> &nbsp;Edit Produk</h3>
            <button class="modal-close" onclick="closeModal('modal-edit-produk')">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <form method="POST" id="form-edit-produk">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label" for="edit_sku">SKU <span style="color:#ef4444">*</span></label>
                    <input type="text" id="edit_sku" name="sku" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_name">Nama Produk <span style="color:#ef4444">*</span></label>
                    <input type="text" id="edit_name" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_category">Kategori <span style="color:#ef4444">*</span></label>
                    <select id="edit_category" name="category_id" class="form-control select-input" style="width:100%;padding:.55rem .875rem;" required>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" for="edit_price">Harga (Rp) <span style="color:#ef4444">*</span></label>
                    <input type="number" id="edit_price" name="price" class="form-control" min="0" step="100" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('modal-edit-produk')">Batal</button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
    function openModal(id) {
        document.getElementById(id).classList.add('open');
    }

    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
    }

    // Tutup modal saat klik overlay
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) closeModal(this.id);
        });
    });

    @if(Auth::user()->isAdmin())
    function openEditModal(id, sku, name, categoryId, price) {
        const form = document.getElementById('form-edit-produk');
        form.action = '/products/' + id;
        document.getElementById('edit_sku').value      = sku;
        document.getElementById('edit_name').value     = name;
        document.getElementById('edit_price').value    = price;
        document.getElementById('edit_category').value = categoryId;
        openModal('modal-edit-produk');
    }
    @endif

    // Auto-buka modal jika ada error validasi dari server
    @if($errors->any() && old('_method') === 'PUT')
        openModal('modal-edit-produk');
    @elseif($errors->any())
        openModal('modal-tambah-produk');
    @endif
</script>
@endsection