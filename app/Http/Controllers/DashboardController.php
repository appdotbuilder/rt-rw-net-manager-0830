<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\PaketInternet;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with statistics.
     */
    public function index()
    {
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::aktif()->count();
        $totalPackages = PaketInternet::aktif()->count();
        $unpaidBills = Tagihan::belumLunas()->count();
        $overdueBills = Tagihan::jatuhTempo()->count();
        
        // Monthly revenue
        $currentMonth = now()->format('Y-m');
        $monthlyRevenue = Pembayaran::whereMonth('tanggal_bayar', now()->month)
            ->whereYear('tanggal_bayar', now()->year)
            ->sum('jumlah');
        
        // Recent customers
        $recentCustomers = Customer::with('paket')
            ->latest()
            ->take(5)
            ->get();
        
        // Recent payments
        $recentPayments = Pembayaran::with(['tagihan.customer'])
            ->latest()
            ->take(5)
            ->get();
        
        return Inertia::render('dashboard', [
            'stats' => [
                'totalCustomers' => $totalCustomers,
                'activeCustomers' => $activeCustomers,
                'totalPackages' => $totalPackages,
                'unpaidBills' => $unpaidBills,
                'overdueBills' => $overdueBills,
                'monthlyRevenue' => $monthlyRevenue,
            ],
            'recentCustomers' => $recentCustomers,
            'recentPayments' => $recentPayments,
        ]);
    }
}