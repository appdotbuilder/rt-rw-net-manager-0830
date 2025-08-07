<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\MikrotikConfig;
use App\Models\PaketInternet;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RTRWSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin RT/RW',
            'email' => 'admin@rtrw.local',
            'password' => Hash::make('password'),
        ]);

        // Create Mikrotik configuration
        MikrotikConfig::factory()->create([
            'name' => 'RT/RW Main Router',
            'host' => '192.168.1.1',
            'port' => 8728,
            'username' => 'admin',
            'password' => 'password123',
            'is_active' => true,
            'description' => 'Router utama RT/RW Net',
        ]);

        // Create internet packages
        $packages = [
            [
                'nama_paket' => 'Paket Hemat 5 Mbps',
                'harga' => 75000,
                'bandwidth' => '5 Mbps',
                'deskripsi' => 'Paket ekonomis untuk browsing dan streaming ringan',
                'status' => 'aktif',
            ],
            [
                'nama_paket' => 'Paket Standar 10 Mbps',
                'harga' => 125000,
                'bandwidth' => '10 Mbps',
                'deskripsi' => 'Paket untuk keluarga dengan streaming HD',
                'status' => 'aktif',
            ],
            [
                'nama_paket' => 'Paket Premium 20 Mbps',
                'harga' => 200000,
                'bandwidth' => '20 Mbps',
                'deskripsi' => 'Paket untuk gaming dan streaming 4K',
                'status' => 'aktif',
            ],
            [
                'nama_paket' => 'Paket Bisnis 50 Mbps',
                'harga' => 400000,
                'bandwidth' => '50 Mbps',
                'deskripsi' => 'Paket untuk warnet dan kantor',
                'status' => 'aktif',
            ],
        ];

        foreach ($packages as $package) {
            PaketInternet::create($package);
        }

        // Create customers
        $paketIds = PaketInternet::pluck('id')->toArray();
        
        for ($i = 1; $i <= 25; $i++) {
            $customer = Customer::create([
                'nama' => "Pelanggan {$i}",
                'alamat' => "Jl. Contoh No. {$i}, RT/RW 00{$i}/001",
                'kontak' => '08' . str_pad((string)$i, 10, '0', STR_PAD_LEFT),
                'username_pppoe' => "user{$i}",
                'password_pppoe' => Customer::generatePassword(),
                'paket_id' => $paketIds[array_rand($paketIds)],
                'ip_pool' => "192.168.100." . (100 + $i),
                'status' => $i <= 20 ? 'aktif' : 'nonaktif',
                'tanggal_daftar' => now()->subDays(random_int(1, 365)),
                'keterangan' => $i % 5 === 0 ? 'Pelanggan VIP' : null,
            ]);

            // Create bills for active customers
            if ($customer->status === 'aktif') {
                // Create bills for last 3 months
                for ($month = 2; $month >= 0; $month--) {
                    $periode = now()->subMonths($month)->format('Y-m');
                    $jatuhTempo = now()->subMonths($month)->addDays(7);
                    
                    $tagihan = Tagihan::create([
                        'customer_id' => $customer->id,
                        'periode' => $periode,
                        'jumlah' => $customer->paket->harga,
                        'jatuh_tempo' => $jatuhTempo,
                        'status' => $month === 0 ? 'belum_lunas' : 'lunas',
                        'keterangan' => 'Tagihan bulanan otomatis',
                    ]);

                    // Create payment for paid bills
                    if ($tagihan->status === 'lunas') {
                        Pembayaran::create([
                            'tagihan_id' => $tagihan->id,
                            'tanggal_bayar' => $jatuhTempo->subDays(random_int(1, 5)),
                            'jumlah' => $tagihan->jumlah,
                            'metode' => collect(['tunai', 'transfer', 'e_wallet'])->random(),
                            'keterangan' => 'Pembayaran via admin',
                        ]);
                    }
                }
            }
        }

        $this->command->info('RT/RW Net sample data created successfully!');
        $this->command->info('Admin login: admin@rtrw.local / password');
        $this->command->info('Total packages: ' . PaketInternet::count());
        $this->command->info('Total customers: ' . Customer::count());
        $this->command->info('Active customers: ' . Customer::aktif()->count());
        $this->command->info('Total bills: ' . Tagihan::count());
        $this->command->info('Total payments: ' . Pembayaran::count());
    }
}