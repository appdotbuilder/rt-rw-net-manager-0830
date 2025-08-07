<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\PaketInternet;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display reports dashboard.
     */
    public function index()
    {
        $currentMonth = now()->format('Y-m');
        $lastMonth = now()->subMonth()->format('Y-m');
        
        // Current month stats
        $currentMonthRevenue = Pembayaran::whereMonth('tanggal_bayar', now()->month)
            ->whereYear('tanggal_bayar', now()->year)
            ->sum('jumlah');
        
        $lastMonthRevenue = Pembayaran::whereMonth('tanggal_bayar', now()->subMonth()->month)
            ->whereYear('tanggal_bayar', now()->subMonth()->year)
            ->sum('jumlah');
        
        // Customer stats
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::aktif()->count();
        $newCustomersThisMonth = Customer::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // Billing stats
        $unpaidBills = Tagihan::belumLunas()->count();
        $overdueBills = Tagihan::jatuhTempo()->count();
        $totalUnpaidAmount = Tagihan::belumLunas()->sum('jumlah');
        
        // Monthly revenue for chart (last 12 months)
        $monthlyRevenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenue = Pembayaran::whereMonth('tanggal_bayar', $date->month)
                ->whereYear('tanggal_bayar', $date->year)
                ->sum('jumlah');
            
            $monthlyRevenue[] = [
                'month' => $date->format('M Y'),
                'revenue' => $revenue,
            ];
        }
        
        // Package popularity
        $packageStats = PaketInternet::withCount('customers')
            ->orderBy('customers_count', 'desc')
            ->get(['id', 'nama_paket', 'harga', 'customers_count']);
        
        return Inertia::render('reports/index', [
            'stats' => [
                'currentMonthRevenue' => $currentMonthRevenue,
                'lastMonthRevenue' => $lastMonthRevenue,
                'revenueGrowth' => $lastMonthRevenue > 0 
                    ? (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 
                    : 0,
                'totalCustomers' => $totalCustomers,
                'activeCustomers' => $activeCustomers,
                'newCustomersThisMonth' => $newCustomersThisMonth,
                'unpaidBills' => $unpaidBills,
                'overdueBills' => $overdueBills,
                'totalUnpaidAmount' => $totalUnpaidAmount,
            ],
            'monthlyRevenue' => $monthlyRevenue,
            'packageStats' => $packageStats,
        ]);
    }

    /**
     * Show the specified resource.
     */
    public function show(Request $request)
    {
        $type = $request->get('type', 'overview');
        
        if ($type === 'revenue') {
            return $this->showRevenue($request);
        } elseif ($type === 'customers') {
            return $this->showCustomers($request);
        }
        
        return $this->index();
    }
    
    /**
     * Revenue report with filters.
     */
    protected function showRevenue(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        $payments = Pembayaran::with(['tagihan.customer'])
            ->whereBetween('tanggal_bayar', [$startDate, $endDate])
            ->latest()
            ->paginate(20)
            ->withQueryString();
        
        $totalRevenue = Pembayaran::whereBetween('tanggal_bayar', [$startDate, $endDate])
            ->sum('jumlah');
        
        // Daily revenue for chart
        $dailyRevenue = Pembayaran::whereBetween('tanggal_bayar', [$startDate, $endDate])
            ->selectRaw('DATE(tanggal_bayar) as date, SUM(jumlah) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Payment method breakdown
        $paymentMethods = Pembayaran::whereBetween('tanggal_bayar', [$startDate, $endDate])
            ->selectRaw('metode, SUM(jumlah) as total, COUNT(*) as count')
            ->groupBy('metode')
            ->get();
        
        return Inertia::render('reports/revenue', [
            'payments' => $payments,
            'totalRevenue' => $totalRevenue,
            'dailyRevenue' => $dailyRevenue,
            'paymentMethods' => $paymentMethods,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }

    /**
     * Customer report with analytics.
     */
    protected function showCustomers(Request $request)
    {
        $query = Customer::with(['paket', 'tagihan']);
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('paket_id')) {
            $query->where('paket_id', $request->paket_id);
        }
        
        $customers = $query->latest()
            ->paginate(20)
            ->withQueryString();
        
        // Customer status distribution
        $statusDistribution = Customer::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();
        
        // Customers by package
        $packageDistribution = Customer::with('paket')
            ->selectRaw('paket_id, COUNT(*) as count')
            ->groupBy('paket_id')
            ->get();
        
        // New customers per month (last 12 months)
        $monthlyNewCustomers = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Customer::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
            
            $monthlyNewCustomers[] = [
                'month' => $date->format('M Y'),
                'count' => $count,
            ];
        }
        
        $packages = PaketInternet::aktif()->get(['id', 'nama_paket']);
        
        return Inertia::render('reports/customers', [
            'customers' => $customers,
            'statusDistribution' => $statusDistribution,
            'packageDistribution' => $packageDistribution,
            'monthlyNewCustomers' => $monthlyNewCustomers,
            'packages' => $packages,
            'filters' => $request->only(['status', 'paket_id']),
        ]);
    }
}