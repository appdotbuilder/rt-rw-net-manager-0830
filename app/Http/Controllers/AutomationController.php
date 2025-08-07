<?php

namespace App\Http\Controllers;

use App\Services\BillingService;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * Handle billing automation tasks.
 */
class AutomationController extends Controller
{
    /**
     * Billing service instance.
     *
     * @var BillingService
     */
    protected BillingService $billingService;

    /**
     * Create a new controller instance.
     *
     * @param BillingService $billingService
     */
    public function __construct(BillingService $billingService)
    {
        $this->billingService = $billingService;
    }

    /**
     * Display the billing automation dashboard.
     */
    public function index()
    {
        $stats = $this->billingService->getBillingStats();
        
        return Inertia::render('automation/index', [
            'stats' => $stats,
        ]);
    }

    /**
     * Store automation tasks.
     */
    public function store(Request $request)
    {
        $action = $request->get('action');
        
        switch ($action) {
            case 'generate_monthly':
                $result = $this->billingService->generateMonthlyBills();
                $message = "Berhasil membuat {$result['created']} tagihan bulanan untuk periode {$result['periode']}.";
                if ($result['skipped'] > 0) {
                    $message .= " {$result['skipped']} tagihan dilewati karena sudah ada.";
                }
                return redirect()->back()->with('success', $message);
                
            case 'process_overdue':
                $result = $this->billingService->processOverdueBills();
                $message = "Pemrosesan tunggakan selesai: ";
                $message .= "{$result['marked_overdue']} tagihan ditandai terlambat, ";
                $message .= "{$result['suspended']} pelanggan di-suspend, ";
                $message .= "{$result['reminders']} reminder dikirim.";
                return redirect()->back()->with('success', $message);
                
            case 'reactivate_customer':
                $request->validate(['customer_id' => 'required|exists:customers,id']);
                $customer = \App\Models\Customer::findOrFail($request->customer_id);
                $result = $this->billingService->reactivateCustomer($customer);
                
                if ($result) {
                    return redirect()->back()->with('success', "Pelanggan {$customer->nama} berhasil diaktifkan kembali.");
                } else {
                    return redirect()->back()->with('error', 'Gagal mengaktifkan pelanggan. Pastikan semua tagihan sudah lunas.');
                }
                
            default:
                return redirect()->back()->with('error', 'Aksi tidak valid.');
        }
    }
}