<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Tax;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->get();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::doesntHave('children')
            ->with(['parent' => function ($q) {
                $q->with(['parent' => function ($q2) {
                    $q2->with('parent');
                }]);
            }])
            ->get();
        $taxes = Tax::all();
        return view('admin.products.create', compact('categories', 'taxes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'images.*' => 'required|image',
            'actual_price' => 'required|numeric',
            'discount_price' => 'nullable|numeric',
            'category_id' => 'required|exists:categories,id',
            'taxes' => 'nullable|array',
            'taxes.*' => 'exists:taxes,id',
        ]);

        $category = Category::findOrFail($request->category_id);
        if (!$category->isLeaf()) {
            return back()->withErrors(['category_id' => 'Products can only be added to leaf categories']);
        }

        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('products', 'public');
            }
        }

        $product = Product::create([
            'name' => $request->name,
            'images' => $images,
            'actual_price' => $request->actual_price,
            'discount_price' => $request->discount_price,
            'category_id' => $request->category_id,
        ]);

        if ($request->has('taxes')) {
            $product->taxes()->attach($request->taxes);
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created');
    }

    public function show($id)
    {
        $product = Product::with(['category', 'taxes'])->findOrFail($id);
        return view('admin.products.show', compact('product'));
    }

    public function edit($id)
    {
        $product = Product::with('taxes')->findOrFail($id);
        $categories = Category::doesntHave('children')
            ->with(['parent' => function ($q) {
                $q->with(['parent' => function ($q2) {
                    $q2->with('parent');
                }]);
            }])
            ->get();
        $taxes = Tax::all();
        return view('admin.products.edit', compact('product', 'categories', 'taxes'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'images.*' => 'nullable|image',
            'actual_price' => 'required|numeric',
            'discount_price' => 'nullable|numeric',
            'category_id' => 'required|exists:categories,id',
            'taxes' => 'nullable|array',
            'taxes.*' => 'exists:taxes,id',
        ]);

        $product = Product::findOrFail($id);
        $category = Category::findOrFail($request->category_id);
        
        if (!$category->isLeaf()) {
            return back()->withErrors(['category_id' => 'Products can only be added to leaf categories']);
        }

        $data = [
            'name' => $request->name,
            'actual_price' => $request->actual_price,
            'discount_price' => $request->discount_price,
            'category_id' => $request->category_id,
        ];

        if ($request->hasFile('images')) {
            foreach ($product->images ?? [] as $oldImage) {
                Storage::disk('public')->delete($oldImage);
            }
            $images = [];
            foreach ($request->file('images') as $image) {
                $images[] = $image->store('products', 'public');
            }
            $data['images'] = $images;
        }

        $product->update($data);
        $product->taxes()->sync($request->taxes ?? []);

        return redirect()->route('admin.products.index')->with('success', 'Product updated');
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        foreach ($product->images ?? [] as $image) {
            Storage::disk('public')->delete($image);
        }
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted');
    }
}
