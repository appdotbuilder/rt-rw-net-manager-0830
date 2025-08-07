<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PembayaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pembayaran::with(['tagihan.customer']);
        
        if ($request->filled('search')) {
            $query->whereHas('tagihan.customer', function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('metode')) {
            $query->where('metode', $request->metode);
        }
        
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_bayar', '>=', $request->tanggal_dari);
        }
        
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_bayar', '<=', $request->tanggal_sampai);
        }
        
        $payments = $query->latest()
            ->paginate(15)
            ->withQueryString();
        
        return Inertia::render('pembayaran/index', [
            'payments' => $payments,
            'filters' => $request->only(['search', 'metode', 'tanggal_dari', 'tanggal_sampai']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $query = Tagihan::with('customer')
            ->where('status', 'belum_lunas');
            
        if ($request->filled('tagihan_id')) {
            $selectedBill = Tagihan::with('customer')->findOrFail($request->tagihan_id);
        } else {
            $selectedBill = null;
        }
        
        $bills = $query->get();
        
        return Inertia::render('pembayaran/create', [
            'bills' => $bills,
            'selectedBill' => $selectedBill,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tagihan_id' => 'required|exists:tagihan,id',
            'tanggal_bayar' => 'required|date|before_or_equal:today',
            'jumlah' => 'required|numeric|min:0',
            'metode' => 'required|in:tunai,transfer,e_wallet,lainnya',
            'bukti' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'keterangan' => 'nullable|string',
        ]);
        
        $data = $request->all();
        
        // Handle proof upload
        if ($request->hasFile('bukti')) {
            $data['bukti'] = $request->file('bukti')->store('pembayaran', 'public');
        }
        
        $pembayaran = Pembayaran::create($data);
        
        // Update bill status to paid
        $tagihan = $pembayaran->tagihan;
        $totalPaid = $tagihan->pembayaran()->sum('jumlah');
        
        if ($totalPaid >= $tagihan->jumlah) {
            $tagihan->update(['status' => 'lunas']);
        }

        return redirect()->route('tagihan.show', $tagihan)
            ->with('success', 'Pembayaran berhasil dicatat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pembayaran $pembayaran)
    {
        $pembayaran->load(['tagihan.customer']);
        
        return Inertia::render('pembayaran/show', [
            'payment' => $pembayaran,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pembayaran $pembayaran)
    {
        $bills = Tagihan::with('customer')->get();
        
        return Inertia::render('pembayaran/edit', [
            'payment' => $pembayaran,
            'bills' => $bills,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pembayaran $pembayaran)
    {
        $request->validate([
            'tagihan_id' => 'required|exists:tagihan,id',
            'tanggal_bayar' => 'required|date',
            'jumlah' => 'required|numeric|min:0',
            'metode' => 'required|in:tunai,transfer,e_wallet,lainnya',
            'bukti' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'keterangan' => 'nullable|string',
        ]);
        
        $data = $request->all();
        
        // Handle proof upload
        if ($request->hasFile('bukti')) {
            // Delete old proof if exists
            if ($pembayaran->bukti) {
                Storage::disk('public')->delete($pembayaran->bukti);
            }
            $data['bukti'] = $request->file('bukti')->store('pembayaran', 'public');
        } else {
            unset($data['bukti']); // Keep existing proof
        }
        
        $oldTagihanId = $pembayaran->tagihan_id;
        $pembayaran->update($data);
        
        // Update old bill status
        if ($oldTagihanId !== $pembayaran->tagihan_id) {
            $oldTagihan = Tagihan::find($oldTagihanId);
            if ($oldTagihan) {
                $totalPaid = $oldTagihan->pembayaran()->sum('jumlah');
                $oldTagihan->update([
                    'status' => $totalPaid >= $oldTagihan->jumlah ? 'lunas' : 'belum_lunas'
                ]);
            }
        }
        
        // Update new bill status
        $tagihan = $pembayaran->tagihan;
        $totalPaid = $tagihan->pembayaran()->sum('jumlah');
        $tagihan->update([
            'status' => $totalPaid >= $tagihan->jumlah ? 'lunas' : 'belum_lunas'
        ]);

        return redirect()->route('pembayaran.show', $pembayaran)
            ->with('success', 'Data pembayaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pembayaran $pembayaran)
    {
        $tagihan = $pembayaran->tagihan;
        
        // Delete proof file if exists
        if ($pembayaran->bukti) {
            Storage::disk('public')->delete($pembayaran->bukti);
        }
        
        $pembayaran->delete();
        
        // Update bill status
        $totalPaid = $tagihan->pembayaran()->sum('jumlah');
        $tagihan->update([
            'status' => $totalPaid >= $tagihan->jumlah ? 'lunas' : 'belum_lunas'
        ]);

        return redirect()->route('tagihan.show', $tagihan)
            ->with('success', 'Pembayaran berhasil dihapus.');
    }
}