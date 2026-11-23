<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StockTransactionController extends Controller
{
    // Tampilkan histori transaksi
    public function index(Request $request)
    {
        $period     = $request->get('period', 'all');
        $filterDate = $request->get('filter_date');   // YYYY-MM-DD  (harian)
        $filterMonth= $request->get('filter_month');  // 1-12        (bulanan)
        $filterYear = $request->get('filter_year');   // YYYY        (bulanan & tahunan)

        $query     = StockTransaction::with(['product', 'user'])->latest();
        $dateLabel = 'Semua Waktu';

        switch ($period) {

            case 'daily':
                if ($filterDate) {
                    $date = Carbon::parse($filterDate);
                } else {
                    $date = Carbon::today();
                    $filterDate = $date->format('Y-m-d');
                }
                $query->whereDate('created_at', $date);
                $dateLabel = 'Harian — ' . $date->format('d/m/Y');
                break;

            case 'monthly':
                $month = $filterMonth ? (int)$filterMonth : Carbon::now()->month;
                $year  = $filterYear  ? (int)$filterYear  : Carbon::now()->year;
                $query->whereYear('created_at', $year)
                      ->whereMonth('created_at', $month);
                $filterMonth = $month;
                $filterYear  = $year;
                $monthNames  = ['Januari','Februari','Maret','April','Mei','Juni',
                                'Juli','Agustus','September','Oktober','November','Desember'];
                $dateLabel   = 'Bulanan — ' . $monthNames[$month - 1] . ' ' . $year;
                break;

            case 'yearly':
                $year  = $filterYear ? (int)$filterYear : Carbon::now()->year;
                $query->whereYear('created_at', $year);
                $filterYear = $year;
                $dateLabel  = 'Tahunan — ' . $year;
                break;
        }

        $transactions = $query->get();
        $products     = Product::orderBy('name')->get();

        // Buat daftar tahun tersedia (dari tahun pertama transaksi sampai tahun ini)
        $firstYear   = StockTransaction::min(DB::raw('YEAR(created_at)')) ?? Carbon::now()->year;
        $currentYear = Carbon::now()->year;
        $yearOptions = range($currentYear, (int)$firstYear);

        return view('stock.index', compact(
            'transactions', 'products', 'period', 'dateLabel',
            'filterDate', 'filterMonth', 'filterYear', 'yearOptions'
        ));
    }

    // Input barang masuk
    public function storeIn(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'notes'      => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request) {
            StockTransaction::create([
                'product_id' => $request->product_id,
                'user_id'    => Auth::id(),
                'type'       => 'in',
                'quantity'   => $request->quantity,
                'notes'      => $request->notes,
            ]);
            Product::findOrFail($request->product_id)->increment('stock', $request->quantity);
        });

        return redirect()->route('stock.index')
                         ->with('success', 'Barang masuk berhasil dicatat.');
    }

    // Input barang keluar
    public function storeOut(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'required|integer|min:1',
            'notes'      => 'nullable|string|max:255',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock < $request->quantity) {
            return back()->withErrors([
                'quantity' => "Stok tidak cukup. Stok tersedia: {$product->stock} pcs.",
            ])->withInput();
        }

        DB::transaction(function () use ($request, $product) {
            StockTransaction::create([
                'product_id' => $request->product_id,
                'user_id'    => Auth::id(),
                'type'       => 'out',
                'quantity'   => $request->quantity,
                'notes'      => $request->notes,
            ]);
            $product->decrement('stock', $request->quantity);
        });

        return redirect()->route('stock.index')
                         ->with('success', 'Barang keluar berhasil dicatat.');
    }

    // Cetak histori (admin only)
    public function print(Request $request)
    {
        $period      = $request->get('period', 'all');
        $filterDate  = $request->get('filter_date');
        $filterMonth = $request->get('filter_month');
        $filterYear  = $request->get('filter_year');
        $query       = StockTransaction::with(['product', 'user'])->latest();
        $dateLabel   = 'Semua Waktu';

        switch ($period) {
            case 'daily':
                $date = $filterDate ? Carbon::parse($filterDate) : Carbon::today();
                $query->whereDate('created_at', $date);
                $dateLabel = 'Harian — ' . $date->format('d/m/Y');
                break;
            case 'monthly':
                $month     = $filterMonth ? (int)$filterMonth : Carbon::now()->month;
                $year      = $filterYear  ? (int)$filterYear  : Carbon::now()->year;
                $query->whereYear('created_at', $year)->whereMonth('created_at', $month);
                $monthNames = ['Januari','Februari','Maret','April','Mei','Juni',
                               'Juli','Agustus','September','Oktober','November','Desember'];
                $dateLabel  = 'Bulanan — ' . $monthNames[$month - 1] . ' ' . $year;
                break;
            case 'yearly':
                $year = $filterYear ? (int)$filterYear : Carbon::now()->year;
                $query->whereYear('created_at', $year);
                $dateLabel = 'Tahunan — ' . $year;
                break;
        }

        $transactions = $query->get();

        return view('stock.print', compact('transactions', 'dateLabel', 'period'));
    }
}
