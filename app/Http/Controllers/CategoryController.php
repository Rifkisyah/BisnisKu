<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{

    public function index(Request $request)
    {
        if (auth()->user()->isKasir()) abort(403, 'Akses Ditolak: Kasir tidak dapat mengelola kategori.');
        $categories = Category::when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->type, fn($q, $t) => $q->where('type', $t))
            ->applySort($request->sort)
            ->paginate(15)
            ->withQueryString();

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        if (auth()->user()->isKasir()) abort(403, 'Akses Ditolak: Kasir tidak dapat mengelola kategori.');
        return view('categories.create');
    }

    public function store(Request $request)
    {
        if (auth()->user()->isKasir()) abort(403, 'Akses Ditolak: Kasir tidak dapat mengelola kategori.');
        $validated = $request->validate([
            'name'        => 'required|string|min:2|max:60|unique:categories,name',
            'description' => 'nullable|string|max:300',
            'type'        => 'required|in:product,service',
        ]);

        $validated['category_code'] = Category::generateCode();
        $validated['slug']          = Str::slug($validated['name']);
        $validated['is_active']     = true;

        Category::create($validated);

        return redirect()->route('categories.index')
            ->with('success', __('messages.created', ['item' => __('messages.category')]));
    }

    public function show(Category $category)
    {
        return view('categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        if (auth()->user()->isKasir()) abort(403, 'Akses Ditolak: Kasir tidak dapat mengelola kategori.');
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        if (auth()->user()->isKasir()) abort(403, 'Akses Ditolak: Kasir tidak dapat mengelola kategori.');
        $validated = $request->validate([
            'name'        => 'required|string|min:2|max:60|unique:categories,name,' . $category->category_code . ',category_code',
            'description' => 'nullable|string|max:300',
            'type'        => 'required|in:product,service',
            'is_active'   => 'boolean',
        ]);

        $validated['slug']      = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        $category->update($validated);

        return redirect()->route('categories.index')
            ->with('success', __('messages.updated', ['item' => __('messages.category')]));
    }

    public function destroy(Category $category)
    {
        if (auth()->user()->isKasir()) abort(403, 'Akses Ditolak: Kasir tidak dapat mengelola kategori.');
        if ($category->products()->exists()) {
            return back()->with('error', __('messages.cannot_delete_has_relation', ['item' => __('messages.category')]));
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', __('messages.deleted', ['item' => __('messages.category')]));
    }
}
