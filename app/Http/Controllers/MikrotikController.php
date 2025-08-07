<?php

namespace App\Http\Controllers;

use App\Models\MikrotikConfig;
use App\Models\Customer;
use App\Services\MikrotikService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MikrotikController extends Controller
{
    /**
     * Display Mikrotik configuration.
     */
    public function index()
    {
        $configs = MikrotikConfig::latest()->get();
        
        return Inertia::render('mikrotik/config', [
            'configs' => $configs,
        ]);
    }

    /**
     * Update Mikrotik configuration.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'host' => 'required|ip',
            'port' => 'required|integer|min:1|max:65535',
            'username' => 'required|string|max:255',
            'password' => 'required|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        // If setting as active, deactivate others
        if ($request->is_active) {
            MikrotikConfig::where('is_active', true)->update(['is_active' => false]);
        }

        $config = MikrotikConfig::updateOrCreate(
            ['name' => $request->name],
            $request->only(['host', 'port', 'username', 'password', 'description', 'is_active'])
        );

        // Test connection
        try {
            $service = new MikrotikService($config);
            $test = $service->testConnection();
            
            if (!$test['success']) {
                return redirect()->back()
                    ->with('warning', 'Konfigurasi disimpan tapi koneksi gagal: ' . $test['message']);
            }
            
            $config->update(['last_sync' => now()]);
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('warning', 'Konfigurasi disimpan tapi koneksi gagal: ' . $e->getMessage());
        }

        return redirect()->route('mikrotik.index')
            ->with('success', 'Konfigurasi Mikrotik berhasil disimpan dan ditest.');
    }

    /**
     * Show Mikrotik status and active connections.
     */
    public function show()
    {
        try {
            $service = new MikrotikService();
            $activeConnections = $service->getActiveConnections();
            
            // Match with customers
            $customers = Customer::with('paket')->get();
            $onlineCustomers = [];
            $offlineCustomers = [];
            
            foreach ($customers as $customer) {
                $isOnline = false;
                foreach ($activeConnections as $connection) {
                    if (isset($connection['name']) && $connection['name'] === $customer->username_pppoe) {
                        $onlineCustomers[] = [
                            'customer' => $customer,
                            'connection' => $connection,
                        ];
                        $isOnline = true;
                        break;
                    }
                }
                
                if (!$isOnline) {
                    $offlineCustomers[] = $customer;
                }
            }
            
            return Inertia::render('mikrotik/status', [
                'onlineCustomers' => $onlineCustomers,
                'offlineCustomers' => $offlineCustomers,
                'totalOnline' => count($onlineCustomers),
                'totalOffline' => count($offlineCustomers),
            ]);
            
        } catch (\Exception $e) {
            return Inertia::render('mikrotik/status', [
                'error' => $e->getMessage(),
                'onlineCustomers' => [],
                'offlineCustomers' => [],
                'totalOnline' => 0,
                'totalOffline' => 0,
            ]);
        }
    }

    /**
     * Update synchronization with Mikrotik.
     */
    public function update()
    {
        try {
            $service = new MikrotikService();
            $customers = Customer::aktif()->get();
            $success = 0;
            $failed = 0;
            
            foreach ($customers as $customer) {
                $result = $service->addPPPoESecret([
                    'username' => $customer->username_pppoe,
                    'password' => $customer->password_pppoe,
                    'profile' => $customer->paket->nama_paket ?? 'default',
                    'remote_address' => $customer->ip_pool,
                    'comment' => 'Customer: ' . $customer->nama,
                ]);
                
                if ($result) {
                    $success++;
                } else {
                    $failed++;
                }
            }
            
            // Update last sync time
            MikrotikConfig::active()->update(['last_sync' => now()]);
            
            return redirect()->back()
                ->with('success', "Sinkronisasi selesai. Berhasil: {$success}, Gagal: {$failed}");
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Sinkronisasi gagal: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        try {
            $service = new MikrotikService();
            $result = $service->addPPPoESecret([
                'username' => $customer->username_pppoe,
                'password' => $customer->password_pppoe,
                'profile' => $customer->paket->nama_paket ?? 'default',
                'remote_address' => $customer->ip_pool,
                'comment' => 'Customer: ' . $customer->nama,
            ]);
            
            if ($result) {
                return redirect()->back()
                    ->with('success', 'Pelanggan berhasil ditambahkan ke Mikrotik.');
            } else {
                return redirect()->back()
                    ->with('error', 'Gagal menambahkan pelanggan ke Mikrotik.');
            }
            
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error: ' . $e->getMessage());
        }
    }
}