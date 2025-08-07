<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePaketRequest;
use App\Http\Requests\UpdatePaketRequest;
use App\Models\PaketInternet;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PaketInternet::query();
        
        if ($request->filled('search')) {
            $query->where('nama_paket', 'like', '%' . $request->search . '%')
                  ->orWhere('bandwidth', 'like', '%' . $request->search . '%');
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $packages = $query->withCount('customers')
            ->latest()
            ->paginate(10)
            ->withQueryString();
        
        return Inertia::render('paket/index', [
            'packages' => $packages,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('paket/create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaketRequest $request)
    {
        PaketInternet::create($request->validated());

        return redirect()->route('paket.index')
            ->with('success', 'Paket internet berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PaketInternet $paket)
    {
        $paket->load(['customers' => function($query) {
            $query->latest()->take(10);
        }]);
        
        return Inertia::render('paket/show', [
            'package' => $paket,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaketInternet $paket)
    {
        return Inertia::render('paket/edit', [
            'package' => $paket,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaketRequest $request, PaketInternet $paket)
    {
        $paket->update($request->validated());

        return redirect()->route('paket.show', $paket)
            ->with('success', 'Paket internet berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaketInternet $paket)
    {
        if ($paket->customers()->count() > 0) {
            return redirect()->route('paket.index')
                ->with('error', 'Paket tidak dapat dihapus karena masih digunakan oleh pelanggan.');
        }
        
        $paket->delete();

        return redirect()->route('paket.index')
            ->with('success', 'Paket internet berhasil dihapus.');
    }
}