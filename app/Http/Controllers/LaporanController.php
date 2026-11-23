<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $period      = $request->get('period', 'monthly');
        $filterDate  = $request->get('filter_date');
        $filterMonth = $request->get('filter_month');
        $filterYear  = $request->get('filter_year');
        $dateLabel   = '';

        // Query dasar: hanya transaksi keluar (pendapatan)
        $query = StockTransaction::with(['product.category', 'user'])
            ->where('type', 'out')
            ->latest();

        switch ($period) {
            case 'daily':
                $date = $filterDate ? Carbon::parse($filterDate) : Carbon::today();
                $filterDate = $date->format('Y-m-d');
                $query->whereDate('created_at', $date);
                $dateLabel = 'Harian — ' . $date->format('d/m/Y');
                break;

            case 'monthly':
                $month = $filterMonth ? (int)$filterMonth : Carbon::now()->month;
                $year  = $filterYear  ? (int)$filterYear  : Carbon::now()->year;
                $filterMonth = $month;
                $filterYear  = $year;
                $query->whereYear('created_at', $year)->whereMonth('created_at', $month);
                $monthNames = ['Januari','Februari','Maret','April','Mei','Juni',
                               'Juli','Agustus','September','Oktober','November','Desember'];
                $dateLabel = 'Bulanan — ' . $monthNames[$month - 1] . ' ' . $year;
                break;

            case 'yearly':
                $year = $filterYear ? (int)$filterYear : Carbon::now()->year;
                $filterYear = $year;
                $query->whereYear('created_at', $year);
                $dateLabel = 'Tahunan — ' . $year;
                break;

            default:
                $dateLabel = 'Semua Waktu';
                break;
        }

        $transactions = $query->get();

        // ── Hitung ringkasan ──
        $totalPendapatan  = 0;
        $totalItemTerjual = 0;
        foreach ($transactions as $t) {
            $harga             = $t->product->price ?? 0;
            $totalPendapatan  += $t->quantity * $harga;
            $totalItemTerjual += $t->quantity;
        }
        $totalTransaksi   = $transactions->count();
        $rataRata         = $totalTransaksi > 0 ? $totalPendapatan / $totalTransaksi : 0;

        // ── Grafik pendapatan per hari/bulan ──
        $chartLabels = [];
        $chartData   = [];

        if ($period === 'daily') {
            for ($h = 0; $h < 24; $h++) {
                $chartLabels[] = sprintf('%02d:00', $h);
                $rev = 0;
                foreach ($transactions as $t) {
                    if ((int)$t->created_at->format('G') === $h) {
                        $rev += $t->quantity * ($t->product->price ?? 0);
                    }
                }
                $chartData[] = $rev;
            }
        } elseif ($period === 'monthly') {
            $daysInMonth = Carbon::createFromDate($filterYear, $filterMonth, 1)->daysInMonth;
            for ($d = 1; $d <= $daysInMonth; $d++) {
                $chartLabels[] = $d;
                $rev = 0;
                foreach ($transactions as $t) {
                    if ((int)$t->created_at->format('j') === $d) {
                        $rev += $t->quantity * ($t->product->price ?? 0);
                    }
                }
                $chartData[] = $rev;
            }
        } else {
            $yr = $filterYear ?? Carbon::now()->year;
            $monthNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            for ($m = 1; $m <= 12; $m++) {
                $chartLabels[] = $monthNames[$m - 1];
                $rev = 0;
                foreach ($transactions as $t) {
                    if ((int)$t->created_at->format('n') === $m) {
                        $rev += $t->quantity * ($t->product->price ?? 0);
                    }
                }
                $chartData[] = $rev;
            }
        }

        // ── Top produk terlaris (per pendapatan) ──
        $produkMap = [];
        foreach ($transactions as $t) {
            $id = $t->product_id;
            if (!isset($produkMap[$id])) {
                $produkMap[$id] = [
                    'nama'       => $t->product->name ?? 'N/A',
                    'kategori'   => $t->product->category->name ?? '-',
                    'qty'        => 0,
                    'pendapatan' => 0,
                ];
            }
            $produkMap[$id]['qty']        += $t->quantity;
            $produkMap[$id]['pendapatan'] += $t->quantity * ($t->product->price ?? 0);
        }
        usort($produkMap, fn($a, $b) => $b['pendapatan'] <=> $a['pendapatan']);
        $topProduk = array_slice($produkMap, 0, 5);

        // ── Tahun tersedia ──
        $firstYear   = StockTransaction::min(DB::raw('YEAR(created_at)')) ?? Carbon::now()->year;
        $currentYear = Carbon::now()->year;
        $yearOptions = range($currentYear, (int)$firstYear);

        return view('laporan.index', compact(
            'period', 'dateLabel', 'filterDate', 'filterMonth', 'filterYear', 'yearOptions',
            'transactions', 'totalPendapatan', 'totalItemTerjual', 'totalTransaksi', 'rataRata',
            'chartLabels', 'chartData', 'topProduk'
        ));
    }

    public function print(Request $request)
    {
        $period      = $request->get('period', 'monthly');
        $filterDate  = $request->get('filter_date');
        $filterMonth = $request->get('filter_month');
        $filterYear  = $request->get('filter_year');
        $dateLabel   = '';

        $query = StockTransaction::with(['product.category', 'user'])
            ->where('type', 'out')
            ->latest();

        switch ($period) {
            case 'daily':
                $date = $filterDate ? Carbon::parse($filterDate) : Carbon::today();
                $query->whereDate('created_at', $date);
                $dateLabel = 'Harian — ' . $date->format('d/m/Y');
                break;
            case 'monthly':
                $month = $filterMonth ? (int)$filterMonth : Carbon::now()->month;
                $year  = $filterYear  ? (int)$filterYear  : Carbon::now()->year;
                $query->whereYear('created_at', $year)->whereMonth('created_at', $month);
                $monthNames = ['Januari','Februari','Maret','April','Mei','Juni',
                               'Juli','Agustus','September','Oktober','November','Desember'];
                $dateLabel = 'Bulanan — ' . $monthNames[$month - 1] . ' ' . $year;
                break;
            case 'yearly':
                $year = $filterYear ? (int)$filterYear : Carbon::now()->year;
                $query->whereYear('created_at', $year);
                $dateLabel = 'Tahunan — ' . $year;
                break;
            default:
                $dateLabel = 'Semua Waktu';
        }

        $transactions = $query->get();

        $totalPendapatan  = 0;
        $totalItemTerjual = 0;
        foreach ($transactions as $t) {
            $totalPendapatan  += $t->quantity * ($t->product->price ?? 0);
            $totalItemTerjual += $t->quantity;
        }
        $totalTransaksi = $transactions->count();

        return view('laporan.print', compact(
            'transactions', 'dateLabel', 'totalPendapatan', 'totalItemTerjual', 'totalTransaksi'
        ));
    }
}
