<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $selectedCategoryId = request()->query('category');

        $categories = Category::whereNull('parent_id')
            ->with(['children' => function ($q) {
                $q->with(['children' => function ($q2) {
                    $q2->with(['children' => function ($q3) {
                        $q3->with('children');
                    }]);
                }]);
            }])
            ->get();

        $productsQuery = Product::with(['category', 'taxes'])->latest();
        $selectedCategory = null;

        if ($selectedCategoryId) {
            $selectedCategory = Category::find($selectedCategoryId);
            if ($selectedCategory) {
                $productsQuery->where('category_id', $selectedCategory->id);
            }
        }

        $products = $productsQuery->get();

        return view('frontend.home', compact('categories', 'products', 'selectedCategory'));
    }

    public function category($id)
    {
        return redirect()->route('home', ['category' => $id]);
    }

    public function product($id)
    {
        $product = Product::with(['category', 'taxes'])->findOrFail($id);
        return view('frontend.product', compact('product'));
    }
}
