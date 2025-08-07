<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class TagihanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Tagihan::with(['customer', 'pembayaran']);
        
        if ($request->filled('search')) {
            $query->whereHas('customer', function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('periode')) {
            $query->where('periode', $request->periode);
        }
        
        $bills = $query->latest()
            ->paginate(15)
            ->withQueryString();
        
        return Inertia::render('tagihan/index', [
            'bills' => $bills,
            'filters' => $request->only(['search', 'status', 'periode']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = Customer::aktif()->get(['id', 'nama']);
        
        return Inertia::render('tagihan/create', [
            'customers' => $customers,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'periode' => 'required|string|size:7',
            'jumlah' => 'required|numeric|min:0',
            'jatuh_tempo' => 'required|date|after:today',
            'keterangan' => 'nullable|string',
        ]);
        
        // Check if bill already exists for this customer and period
        $exists = Tagihan::where('customer_id', $request->customer_id)
            ->where('periode', $request->periode)
            ->exists();
            
        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['periode' => 'Tagihan untuk periode ini sudah ada.']);
        }
        
        $tagihan = Tagihan::create($request->all());

        return redirect()->route('tagihan.show', $tagihan)
            ->with('success', 'Tagihan berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tagihan $tagihan)
    {
        $tagihan->load(['customer', 'pembayaran']);
        
        return Inertia::render('tagihan/show', [
            'bill' => $tagihan,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tagihan $tagihan)
    {
        $customers = Customer::aktif()->get(['id', 'nama']);
        
        return Inertia::render('tagihan/edit', [
            'bill' => $tagihan,
            'customers' => $customers,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tagihan $tagihan)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'periode' => 'required|string|size:7',
            'jumlah' => 'required|numeric|min:0',
            'jatuh_tempo' => 'required|date',
            'status' => 'required|in:belum_lunas,lunas,terlambat',
            'keterangan' => 'nullable|string',
        ]);
        
        // Check uniqueness excluding current record
        $exists = Tagihan::where('customer_id', $request->customer_id)
            ->where('periode', $request->periode)
            ->where('id', '!=', $tagihan->id)
            ->exists();
            
        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['periode' => 'Tagihan untuk periode ini sudah ada.']);
        }
        
        $tagihan->update($request->all());

        return redirect()->route('tagihan.show', $tagihan)
            ->with('success', 'Tagihan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tagihan $tagihan)
    {
        if ($tagihan->pembayaran()->count() > 0) {
            return redirect()->route('tagihan.index')
                ->with('error', 'Tagihan tidak dapat dihapus karena sudah ada pembayaran.');
        }
        
        $tagihan->delete();

        return redirect()->route('tagihan.index')
            ->with('success', 'Tagihan berhasil dihapus.');
    }


}