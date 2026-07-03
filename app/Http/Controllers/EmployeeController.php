<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        // Scope employees to the current store only
        $query = User::with('role')
            ->where('store_id', auth()->user()->store_id)
            ->when($request->search, fn($q, $s) => $q->where('username', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"))
            ->when($request->role, fn($q, $r) => $q->whereHas('role', fn($rq) => $rq->where('name', $r)));

        if ($request->get('export') === 'pdf') {
            $employees = $query->applySort($request->sort)->get();
            $pdf = Pdf::loadView('employees.pdf', compact('employees'));
            return $pdf->download('laporan-karyawan.pdf');
        }

        $employees = $query->applySort($request->sort)->paginate(15)->withQueryString();
        $roles = Role::all();

        return view('employees.index', compact('employees', 'roles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('employees.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|min:3|max:60',
            'email'    => 'required|email|max:150|unique:users,email',
            'password' => ['required', 'confirmed', Password::defaults()],
            'role_id'  => 'required|exists:roles,id',
            'contact'  => 'nullable|string|min:7|max:20',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = 'active';
        $validated['store_id'] = auth()->user()->store_id; // auto-assign to owner's store

        User::create($validated);

        return redirect()->route('employees.index')
            ->with('success', __('messages.created', ['item' => __('messages.employee')]));
    }

    public function show(\App\Models\User $employee)
    {
        return view('employees.show', compact('employee'));
    }

    public function edit(User $employee)
    {
        $roles = Role::all();
        return view('employees.edit', compact('employee', 'roles'));
    }

    public function update(Request $request, User $employee)
    {
        $validated = $request->validate([
            'username' => 'required|string|min:3|max:60',
            'email'    => 'required|email|max:150|unique:users,email,' . $employee->id,
            'role_id'  => 'required|exists:roles,id',
            'contact'  => 'nullable|string|min:7|max:20',
            'status'   => 'required|in:active,inactive',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => ['confirmed', Password::defaults()]]);
            $validated['password'] = Hash::make($request->password);
        }

        $employee->update($validated);

        return redirect()->route('employees.index')
            ->with('success', __('messages.updated', ['item' => __('messages.employee')]));
    }

    public function destroy(User $employee)
    {
        if ($employee->id === auth()->id()) {
            return back()->with('error', __('messages.cannot_delete_self'));
        }

        $employee->delete();

        return redirect()->route('employees.index')
            ->with('success', __('messages.deleted', ['item' => __('messages.employee')]));
    }
}
