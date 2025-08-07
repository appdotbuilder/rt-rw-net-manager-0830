<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Tagihan;
use App\Models\PaketInternet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BillingService
{
    /**
     * Generate monthly bills for all active customers.
     *
     * @return array
     */
    public function generateMonthlyBills(): array
    {
        $periode = now()->format('Y-m');
        $customers = Customer::aktif()->with('paket')->get();
        $created = 0;
        $skipped = 0;
        
        foreach ($customers as $customer) {
            // Skip if bill already exists
            if (Tagihan::where('customer_id', $customer->id)->where('periode', $periode)->exists()) {
                $skipped++;
                continue;
            }
            
            Tagihan::create([
                'customer_id' => $customer->id,
                'periode' => $periode,
                'jumlah' => $customer->paket->harga,
                'jatuh_tempo' => now()->addDays(7), // 7 days from now
                'keterangan' => 'Tagihan bulanan otomatis',
            ]);
            
            $created++;
        }
        
        Log::info("Monthly billing generated: {$created} created, {$skipped} skipped");
        
        return [
            'created' => $created,
            'skipped' => $skipped,
            'periode' => $periode,
        ];
    }

    /**
     * Process overdue bills and suspend customers.
     *
     * @return array
     */
    public function processOverdueBills(): array
    {
        $suspendCount = 0;
        $markOverdueCount = 0;
        $reminderCount = 0;
        
        // Find bills that are overdue by more than 5 days
        $overdueBills = Tagihan::with('customer')
            ->where('status', 'belum_lunas')
            ->where('jatuh_tempo', '<', now()->subDays(5))
            ->get();
        
        foreach ($overdueBills as $bill) {
            $customer = $bill->customer;
            
            // Mark bill as overdue
            if ($bill->status !== 'terlambat') {
                $bill->update(['status' => 'terlambat']);
                $markOverdueCount++;
                Log::info("Marked bill #{$bill->id} as overdue for customer: {$customer->nama}");
            }
            
            // Suspend customer if active
            if ($customer->status === 'aktif') {
                $customer->update([
                    'status' => 'suspended',
                    'keterangan' => 'Suspended due to overdue payment on ' . now()->toDateString()
                ]);
                $suspendCount++;
                Log::info("Suspended customer: {$customer->nama} (Bill #{$bill->id})");
                
                // TODO: Disable in Mikrotik
                try {
                    $mikrotikService = new MikrotikService();
                    $mikrotikService->togglePPPoESecret($customer->username_pppoe, false);
                } catch (\Exception $e) {
                    Log::error("Failed to disable customer {$customer->username_pppoe} in Mikrotik: " . $e->getMessage());
                }
            }
        }
        
        // Find bills due in 3 days for reminder
        $reminderBills = Tagihan::with('customer')
            ->where('status', 'belum_lunas')
            ->whereDate('jatuh_tempo', now()->addDays(3))
            ->get();
        
        foreach ($reminderBills as $bill) {
            $reminderCount++;
            Log::info("Reminder needed for customer: {$bill->customer->nama} (Due: {$bill->jatuh_tempo->format('d/m/Y')})");
            
            // TODO: Send reminder email
            $this->sendPaymentReminder($bill);
        }
        
        Log::info("Overdue processing completed: {$markOverdueCount} marked overdue, {$suspendCount} suspended, {$reminderCount} reminders");
        
        return [
            'marked_overdue' => $markOverdueCount,
            'suspended' => $suspendCount,
            'reminders' => $reminderCount,
        ];
    }

    /**
     * Send payment reminder (placeholder for email/WhatsApp).
     *
     * @param Tagihan $tagihan
     * @return bool
     */
    public function sendPaymentReminder(Tagihan $tagihan): bool
    {
        // TODO: Implement email reminder
        // Mail::to($tagihan->customer->email)->send(new PaymentReminderMail($tagihan));
        
        // TODO: Implement WhatsApp reminder via gateway
        // $whatsappGateway = new WhatsAppGateway();
        // $whatsappGateway->sendReminder($tagihan->customer->kontak, $tagihan);
        
        Log::info("Payment reminder sent to customer: {$tagihan->customer->nama}");
        
        return true;
    }

    /**
     * Get billing statistics.
     *
     * @return array
     */
    public function getBillingStats(): array
    {
        $currentMonth = now()->format('Y-m');
        
        return [
            'total_bills' => Tagihan::count(),
            'current_month_bills' => Tagihan::where('periode', $currentMonth)->count(),
            'unpaid_bills' => Tagihan::where('status', 'belum_lunas')->count(),
            'overdue_bills' => Tagihan::where('status', 'terlambat')->count(),
            'total_unpaid_amount' => Tagihan::whereIn('status', ['belum_lunas', 'terlambat'])->sum('jumlah'),
            'current_month_revenue' => Tagihan::where('periode', $currentMonth)
                ->where('status', 'lunas')
                ->sum('jumlah'),
        ];
    }

    /**
     * Reactivate customer after payment.
     *
     * @param Customer $customer
     * @return bool
     */
    public function reactivateCustomer(Customer $customer): bool
    {
        if ($customer->status !== 'suspended') {
            return false;
        }
        
        // Check if all bills are paid
        $unpaidBills = $customer->tagihan()
            ->whereIn('status', ['belum_lunas', 'terlambat'])
            ->count();
        
        if ($unpaidBills > 0) {
            return false;
        }
        
        // Reactivate customer
        $customer->update([
            'status' => 'aktif',
            'keterangan' => 'Reactivated after payment on ' . now()->toDateString()
        ]);
        
        // TODO: Enable in Mikrotik
        try {
            $mikrotikService = new MikrotikService();
            $mikrotikService->togglePPPoESecret($customer->username_pppoe, true);
        } catch (\Exception $e) {
            Log::error("Failed to enable customer {$customer->username_pppoe} in Mikrotik: " . $e->getMessage());
            return false;
        }
        
        Log::info("Customer reactivated: {$customer->nama}");
        
        return true;
    }
}