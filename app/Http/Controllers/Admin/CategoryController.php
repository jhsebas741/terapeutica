<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = QueryBuilder::for(Category::class)
            ->allowedFilters([
                AllowedFilter::callback('search', function ($query, $value) {
                    $query->where(function ($q) use ($value) {
                        $q->where('name', 'like', "%{$value}%")
                            ->orWhere('description', 'like', "%{$value}%");
                    });
                }),
            ])
            ->allowedSorts(['name', 'created_at'])
            ->defaultSort('name')
            ->withCount('pictograms')
            ->paginate($request->input('per_page', 15))
            ->withQueryString();

        return Inertia::render('admin/catalog/categories/Index', [
            'paginatedCategories' => $categories,
        ]);
    }

    public function create()
    {
        return Inertia::render('admin/catalog/categories/Create');
    }

    public function store(CategoryRequest $request)
    {
        Category::create($request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Categoría creada exitosamente',
        ]);

        return redirect()->route('admin.categories.index');
    }

    public function edit(Category $category)
    {
        return Inertia::render('admin/catalog/categories/Edit', [
            'category' => $category,
        ]);
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Categoría actualizada exitosamente',
        ]);

        return redirect()->route('admin.categories.index');
    }

    public function destroy(Category $category)
    {
        if ($category->pictograms()->exists()) {
            Inertia::flash('toast', [
                'type' => 'error',
                'message' => 'No se puede eliminar una categoría con pictogramas asociados',
            ]);

            return redirect()->route('admin.categories.index');
        }

        $category->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Categoría eliminada exitosamente',
        ]);

        return redirect()->route('admin.categories.index');
    }
}
