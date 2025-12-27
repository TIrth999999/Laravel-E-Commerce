<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tax;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            DB::table('product_tax')->delete();
            Product::query()->delete();
            Category::query()->delete();
            Tax::query()->delete();

            $taxGst = Tax::create([
                'name' => 'GST',
                'type' => 'percentage',
                'value' => 18,
            ]);

            $taxShipping = Tax::create([
                'name' => 'Shipping Fee',
                'type' => 'flat',
                'value' => 49,
            ]);

            $electronics = Category::create([
                'name' => 'Electronics',
                'image' => 'https://picsum.photos/seed/electronics/600/400',
                'parent_id' => null,
            ]);
            $mobiles = Category::create([
                'name' => 'Mobiles',
                'image' => 'https://picsum.photos/seed/mobiles/600/400',
                'parent_id' => $electronics->id,
            ]);
            $android = Category::create([
                'name' => 'Android Phones',
                'image' => 'https://picsum.photos/seed/android/600/400',
                'parent_id' => $mobiles->id,
            ]);
            $ios = Category::create([
                'name' => 'iOS Phones',
                'image' => 'https://picsum.photos/seed/ios/600/400',
                'parent_id' => $mobiles->id,
            ]);

            $fashion = Category::create([
                'name' => 'Fashion',
                'image' => 'https://picsum.photos/seed/fashion/600/400',
                'parent_id' => null,
            ]);
            $men = Category::create([
                'name' => 'Men',
                'image' => 'https://picsum.photos/seed/men/600/400',
                'parent_id' => $fashion->id,
            ]);
            $shirts = Category::create([
                'name' => 'Shirts',
                'image' => 'https://picsum.photos/seed/shirts/600/400',
                'parent_id' => $men->id,
            ]);
            $women = Category::create([
                'name' => 'Women',
                'image' => 'https://picsum.photos/seed/women/600/400',
                'parent_id' => $fashion->id,
            ]);
            $dresses = Category::create([
                'name' => 'Dresses',
                'image' => 'https://picsum.photos/seed/dresses/600/400',
                'parent_id' => $women->id,
            ]);

            $p1 = Product::create([
                'name' => 'NeoPhone A1 (8GB / 128GB)',
                'images' => ['https://picsum.photos/seed/phone1/800/800'],
                'actual_price' => 19999,
                'discount_price' => 15999,
                'category_id' => $android->id,
            ]);
            $p1->taxes()->sync([$taxGst->id, $taxShipping->id]);

            $p2 = Product::create([
                'name' => 'iFruit Mini (64GB)',
                'images' => ['https://picsum.photos/seed/phone2/800/800'],
                'actual_price' => 49999,
                'discount_price' => 44999,
                'category_id' => $ios->id,
            ]);
            $p2->taxes()->sync([$taxGst->id]);

            $p3 = Product::create([
                'name' => 'Oxford Cotton Shirt',
                'images' => ['https://picsum.photos/seed/shirt1/800/800'],
                'actual_price' => 1499,
                'discount_price' => 999,
                'category_id' => $shirts->id,
            ]);
            $p3->taxes()->sync([$taxGst->id]);

            $p4 = Product::create([
                'name' => 'Floral Summer Dress',
                'images' => ['https://picsum.photos/seed/dress1/800/800'],
                'actual_price' => 2499,
                'discount_price' => 1999,
                'category_id' => $dresses->id,
            ]);
            $p4->taxes()->sync([$taxGst->id, $taxShipping->id]);
        });
    }
}
