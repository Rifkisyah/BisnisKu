<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%")->orWhere('supplier_code', 'like', "%{$s}%"))
            ->when($request->status, fn($q, $st) => $q->where('status', $st));

        if ($request->get('export') === 'pdf') {
            $suppliers = $query->latest()->get();
            $pdf = Pdf::loadView('suppliers.pdf', compact('suppliers'));
            return $pdf->download('laporan-supplier.pdf');
        }

        $suppliers = $query->latest()->paginate(15)->withQueryString();
        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|min:3|max:100',
            'phone_prefix' => 'nullable|string|max:10',
            'phone_number' => 'nullable|string|min:7|max:20',
            'email'        => 'nullable|email|max:150',
            'address'      => 'nullable|string|max:300',
            'notes'        => 'nullable|string|max:500',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('suppliers', 'public');
        }

        Supplier::create(array_merge($validated, [
            'supplier_code' => Supplier::generateCode(),
            'is_active' => true,
        ]));

        return redirect()->route('suppliers.index')
            ->with('success', __('messages.created', ['item' => __('messages.supplier')]));
    }

    public function show(Supplier $supplier)
    {
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name'         => 'required|string|min:3|max:100',
            'phone_prefix' => 'nullable|string|max:10',
            'phone_number' => 'nullable|string|min:7|max:20',
            'email'        => 'nullable|email|max:150',
            'address'      => 'nullable|string|max:300',
            'notes'        => 'nullable|string|max:500',
            'is_active'    => 'boolean',
            'image'        => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($supplier->image) {
                Storage::disk('public')->delete($supplier->image);
            }
            $validated['image'] = $request->file('image')->store('suppliers', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active');
        $supplier->update($validated);

        return redirect()->route('suppliers.index')
            ->with('success', __('messages.updated', ['item' => 'Supplier']));
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->products()->count() > 0) {
            return back()->with('error', __('messages.cannot_delete_has_relation', ['item' => 'Supplier']));
        }

        if ($supplier->image) {
            Storage::disk('public')->delete($supplier->image);
        }

        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', __('messages.deleted', ['item' => 'Supplier']));
    }
}
