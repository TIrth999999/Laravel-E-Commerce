<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::whereNull('parent_id')
            ->with(['children' => function($query) {
                $query->with(['children' => function($q) {
                    $q->with(['children' => function($q2) {
                        $q2->with('children');
                    }]);
                }]);
            }])
            ->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        $categoriesTree = Category::whereNull('parent_id')
            ->with([
                'children' => function ($q) {
                    $q->with([
                        'children' => function ($q2) {
                            $q2->with([
                                'children' => function ($q3) {
                                    $q3->with('children')->withCount('products');
                                },
                            ])->withCount('products');
                        },
                    ])->withCount('products');
                },
            ])
            ->withCount('products')
            ->get();

        return view('admin.categories.create', [
            'categoriesTree' => $categoriesTree,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'image' => 'required|image',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        if ($request->parent_id) {
            $parent = Category::withCount('products')->findOrFail($request->parent_id);
            if ($parent->products_count > 0) {
                return back()->withErrors([
                    'parent_id' => 'This category already has products. You cannot create a subcategory under it. Create a new category or remove products from this category first.',
                ])->withInput();
            }
        }

        $imagePath = $request->file('image')->store('categories', 'public');

        Category::create([
            'name' => $request->name,
            'image' => $imagePath,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Category created');
    }

    public function show($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.show', compact('category'));
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $categoriesTree = Category::whereNull('parent_id')
            ->where('id', '!=', $id)
            ->with([
                'children' => function ($q) use ($id) {
                    $q->where('id', '!=', $id)->with([
                        'children' => function ($q2) use ($id) {
                            $q2->where('id', '!=', $id)->with([
                                'children' => function ($q3) use ($id) {
                                    $q3->where('id', '!=', $id)->with('children')->withCount('products');
                                },
                            ])->withCount('products');
                        },
                    ])->withCount('products');
                },
            ])
            ->withCount('products')
            ->get();

        return view('admin.categories.edit', [
            'category' => $category,
            'categoriesTree' => $categoriesTree,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'image' => 'nullable|image',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $category = Category::findOrFail($id);
        $data = ['name' => $request->name, 'parent_id' => $request->parent_id];

        if ($request->parent_id) {
            $parent = Category::withCount('products')->findOrFail($request->parent_id);
            if ($parent->products_count > 0) {
                return back()->withErrors([
                    'parent_id' => 'This category already has products. You cannot move/create a subcategory under it. Create a new category or remove products from this category first.',
                ])->withInput();
            }
        }

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($category->image);
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);
        return redirect()->route('admin.categories.index')->with('success', 'Category updated');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        Storage::disk('public')->delete($category->image);
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Category deleted');
    }
}
