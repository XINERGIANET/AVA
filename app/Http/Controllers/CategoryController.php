<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of active product categories.
     */
    public function index(Request $request)
    {
        $query = Category::where('deleted', 0);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $categories = $query->orderBy('id', 'asc')->paginate(15);

        return view('categories.index', compact('categories'));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'El nombre de la categoría es obligatorio.',
            'name.max' => 'El nombre no debe exceder 255 caracteres.',
            'description.max' => 'La descripción no debe exceder 500 caracteres.',
        ]);

        $category = Category::create([
            'name' => trim($request->name),
            'description' => $request->filled('description') ? trim($request->description) : null,
            'deleted' => 0,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'category' => $category,
                'message' => 'Categoría registrada correctamente.',
            ]);
        }

        return redirect()->route('categories.index')->with('success', 'Categoría registrada correctamente.');
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'El nombre de la categoría es obligatorio.',
            'name.max' => 'El nombre no debe exceder 255 caracteres.',
            'description.max' => 'La descripción no debe exceder 500 caracteres.',
        ]);

        $category->update([
            'name' => trim($request->name),
            'description' => $request->filled('description') ? trim($request->description) : null,
        ]);

        return redirect()->route('categories.index')->with('success', 'Categoría actualizada correctamente.');
    }

    /**
     * Soft delete the specified category.
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->deleted = 1;
        $category->save();

        return redirect()->route('categories.index')->with('success', 'Categoría eliminada correctamente.');
    }
}
