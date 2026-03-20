<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PictogramRequest;
use App\Models\Category;
use App\Models\Pictogram;
use App\Models\RoutineStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PictogramController extends Controller
{
    public function index(Request $request)
    {
        $pictograms = QueryBuilder::for(Pictogram::class)
            ->allowedFilters([
                AllowedFilter::callback('search', function ($query, $value) {
                    $query->where(function ($q) use ($value) {
                        $q->where('name', 'like', "%{$value}%")
                            ->orWhere('description', 'like', "%{$value}%");
                    });
                }),
                AllowedFilter::callback('category_id', function ($query, $value) {
                    if ($value !== 'all') {
                        $query->where('category_id', $value);
                    }
                }),
                AllowedFilter::exact('is_active'),
            ])
            ->allowedSorts(['name', 'difficulty_level', 'created_at'])
            ->defaultSort('name')
            ->with('category')
            ->get();

        $grouped = $pictograms->groupBy(fn (Pictogram $p) => $p->category_id ?? 0);

        $categories = Category::query()->orderBy('name')->get(['id', 'name', 'icon_emoji', 'color_hex']);

        $groupedByCategory = $categories
            ->filter(fn (Category $cat) => $grouped->has($cat->id))
            ->map(fn (Category $cat) => [
                'category' => $cat,
                'pictograms' => $grouped->get($cat->id)->values(),
            ])
            ->values();

        $uncategorized = $grouped->get(0);
        if ($uncategorized) {
            $groupedByCategory->prepend([
                'category' => [
                    'id' => 0,
                    'name' => 'Sin categoría',
                    'icon_emoji' => '📁',
                    'color_hex' => '#94a3b8',
                ],
                'pictograms' => $uncategorized->values(),
            ]);
        }

        return Inertia::render('admin/catalog/pictograms/Index', [
            'groupedPictograms' => $groupedByCategory,
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        $categories = Category::query()->orderBy('name')->get(['id', 'name', 'icon_emoji']);

        return Inertia::render('admin/catalog/pictograms/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(PictogramRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image_url'] = $request->file('image')->store('pictograms/images', 'public');
        }

        if ($request->hasFile('audio')) {
            $data['audio_url'] = $request->file('audio')->store('pictograms/audio', 'public');
        }

        unset($data['image'], $data['audio']);

        Pictogram::create($data);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Pictograma creado exitosamente',
        ]);

        return redirect()->route('admin.pictograms.index');
    }

    public function edit(Pictogram $pictogram)
    {
        $categories = Category::query()->orderBy('name')->get(['id', 'name', 'icon_emoji']);

        return Inertia::render('admin/catalog/pictograms/Edit', [
            'pictogram' => $pictogram->load('category'),
            'categories' => $categories,
        ]);
    }

    public function update(PictogramRequest $request, Pictogram $pictogram)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($pictogram->image_url) {
                Storage::disk('public')->delete($pictogram->image_url);
            }
            $data['image_url'] = $request->file('image')->store('pictograms/images', 'public');
        }

        if ($request->hasFile('audio')) {
            if ($pictogram->audio_url) {
                Storage::disk('public')->delete($pictogram->audio_url);
            }
            $data['audio_url'] = $request->file('audio')->store('pictograms/audio', 'public');
        }

        unset($data['image'], $data['audio']);

        $pictogram->update($data);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Pictograma actualizado exitosamente',
        ]);

        return redirect()->route('admin.pictograms.index');
    }

    public function destroy(Pictogram $pictogram)
    {
        if (RoutineStep::where('pictogram_id', $pictogram->id)->exists()) {
            Inertia::flash('toast', [
                'type' => 'error',
                'message' => 'No se puede eliminar el pictograma porque está siendo usado en una o más rutinas',
            ]);

            return redirect()->route('admin.pictograms.index');
        }

        $pictogram->delete();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => 'Pictograma eliminado exitosamente',
        ]);

        return redirect()->route('admin.pictograms.index');
    }
}
