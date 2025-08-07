<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\PaketInternet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Customer::with('paket');
        
        if ($request->filled('search')) {
            $query->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('alamat', 'like', '%' . $request->search . '%')
                  ->orWhere('kontak', 'like', '%' . $request->search . '%')
                  ->orWhere('username_pppoe', 'like', '%' . $request->search . '%');
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('paket_id')) {
            $query->where('paket_id', $request->paket_id);
        }
        
        $customers = $query->latest()
            ->paginate(15)
            ->withQueryString();
        
        $packages = PaketInternet::aktif()->get(['id', 'nama_paket']);
        
        return Inertia::render('customers/index', [
            'customers' => $customers,
            'packages' => $packages,
            'filters' => $request->only(['search', 'status', 'paket_id']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $packages = PaketInternet::aktif()->get(['id', 'nama_paket', 'harga', 'bandwidth']);
        
        return Inertia::render('customers/create', [
            'packages' => $packages,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request)
    {
        $data = $request->validated();
        
        // Generate username and password
        $data['username_pppoe'] = Customer::generateUsername($data['nama']);
        $data['password_pppoe'] = Customer::generatePassword();
        $data['tanggal_daftar'] = now();
        
        // Handle KTP photo upload
        if ($request->hasFile('foto_ktp')) {
            $data['foto_ktp'] = $request->file('foto_ktp')->store('ktp', 'public');
        }
        
        $customer = Customer::create($data);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
        $customer->load(['paket', 'tagihan' => function($query) {
            $query->latest()->take(5);
        }]);
        
        return Inertia::render('customers/show', [
            'customer' => $customer,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        $packages = PaketInternet::aktif()->get(['id', 'nama_paket', 'harga', 'bandwidth']);
        
        return Inertia::render('customers/edit', [
            'customer' => $customer,
            'packages' => $packages,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $data = $request->validated();
        
        // Handle password update
        if (empty($data['password_pppoe'])) {
            unset($data['password_pppoe']);
        }
        
        // Handle KTP photo upload
        if ($request->hasFile('foto_ktp')) {
            // Delete old photo if exists
            if ($customer->foto_ktp) {
                Storage::disk('public')->delete($customer->foto_ktp);
            }
            $data['foto_ktp'] = $request->file('foto_ktp')->store('ktp', 'public');
        }
        
        $customer->update($data);

        return redirect()->route('customers.show', $customer)
            ->with('success', 'Data pelanggan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        // Delete KTP photo if exists
        if ($customer->foto_ktp) {
            Storage::disk('public')->delete($customer->foto_ktp);
        }
        
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'Pelanggan berhasil dihapus.');
    }


}