<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'all');
        $user   = Auth::user();

        // Bangun query berdasarkan periode
        $query = StockTransaction::query();

        switch ($period) {
            case 'daily':
                $query->whereDate('created_at', Carbon::today());
                $dateLabel = 'Hari Ini (' . Carbon::today()->format('d M Y') . ')';
                break;
            case 'monthly':
                $query->whereYear('created_at', Carbon::now()->year)
                      ->whereMonth('created_at', Carbon::now()->month);
                $dateLabel = Carbon::now()->format('F Y');
                break;
            case 'yearly':
                $query->whereYear('created_at', Carbon::now()->year);
                $dateLabel = 'Tahun ' . Carbon::now()->year;
                break;
            default:
                $dateLabel = 'Semua Waktu';
                break;
        }

        // Hitung total transaksi masuk dan keluar
        $transactionsIn  = (clone $query)->where('type', 'in')->sum('quantity');
        $transactionsOut = (clone $query)->where('type', 'out')->sum('quantity');

        // Total produk & stok
        $totalProducts = Product::count();
        $totalStock    = Product::sum('stock');

        // Total pendapatan (hanya admin)
        $totalRevenue = 0;
        if ($user->isAdmin()) {
            $outTransactions = (clone $query)->where('type', 'out')->with('product')->get();
            foreach ($outTransactions as $t) {
                $totalRevenue += $t->quantity * ($t->product->price ?? 0);
            }
        }

        // Data grafik
        $revenueChartData = $this->getRevenueChartData($period);
        $topProductsData  = $this->getTopProductsData($period);

        return view('dashboard', compact(
            'period',
            'dateLabel',
            'transactionsIn',
            'transactionsOut',
            'totalProducts',
            'totalStock',
            'totalRevenue',
            'revenueChartData',
            'topProductsData',
        ));
    }

    private function getRevenueChartData(string $period): array
    {
        $labels = [];
        $data   = [];

        switch ($period) {
            case 'daily':
                // Per jam hari ini — gunakan whereRaw agar HOUR() berjalan benar di MySQL
                for ($h = 0; $h < 24; $h++) {
                    $labels[] = sprintf('%02d:00', $h);
                    $transactions = StockTransaction::where('type', 'out')
                        ->whereDate('created_at', Carbon::today())
                        ->whereRaw('HOUR(created_at) = ?', [$h])
                        ->with('product')
                        ->get();
                    $revenue = 0;
                    foreach ($transactions as $t) {
                        $revenue += $t->quantity * ($t->product->price ?? 0);
                    }
                    $data[] = $revenue;
                }
                break;

            case 'monthly':
                // Per hari bulan ini
                $daysInMonth = Carbon::now()->daysInMonth;
                for ($d = 1; $d <= $daysInMonth; $d++) {
                    $labels[] = $d;
                    $transactions = StockTransaction::where('type', 'out')
                        ->whereYear('created_at', Carbon::now()->year)
                        ->whereMonth('created_at', Carbon::now()->month)
                        ->whereDay('created_at', $d)
                        ->with('product')
                        ->get();
                    $revenue = 0;
                    foreach ($transactions as $t) {
                        $revenue += $t->quantity * ($t->product->price ?? 0);
                    }
                    $data[] = $revenue;
                }
                break;

            case 'yearly':
                // Per bulan tahun ini
                $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                for ($m = 1; $m <= 12; $m++) {
                    $labels[] = $months[$m - 1];
                    $transactions = StockTransaction::where('type', 'out')
                        ->whereYear('created_at', Carbon::now()->year)
                        ->whereMonth('created_at', $m)
                        ->with('product')
                        ->get();
                    $revenue = 0;
                    foreach ($transactions as $t) {
                        $revenue += $t->quantity * ($t->product->price ?? 0);
                    }
                    $data[] = $revenue;
                }
                break;

            default:
                // Semua waktu — per bulan tahun ini
                $months = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                for ($m = 1; $m <= 12; $m++) {
                    $labels[] = $months[$m - 1];
                    $transactions = StockTransaction::where('type', 'out')
                        ->whereYear('created_at', Carbon::now()->year)
                        ->whereMonth('created_at', $m)
                        ->with('product')
                        ->get();
                    $revenue = 0;
                    foreach ($transactions as $t) {
                        $revenue += $t->quantity * ($t->product->price ?? 0);
                    }
                    $data[] = $revenue;
                }
                break;
        }

        return ['labels' => $labels, 'data' => $data];
    }

    private function getTopProductsData(string $period): array
    {
        $query = StockTransaction::where('type', 'out');

        switch ($period) {
            case 'daily':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'monthly':
                $query->whereYear('created_at', Carbon::now()->year)
                      ->whereMonth('created_at', Carbon::now()->month);
                break;
            case 'yearly':
                $query->whereYear('created_at', Carbon::now()->year);
                break;
        }

        $topProducts = $query
            ->selectRaw('product_id, SUM(quantity) as total_sold')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->with('product')
            ->get();

        $labels = [];
        $data   = [];
        foreach ($topProducts as $t) {
            $labels[] = $t->product->name ?? 'N/A';
            $data[]   = (int) $t->total_sold;
        }

        return ['labels' => $labels, 'data' => $data];
    }
}
