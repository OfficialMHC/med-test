<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Variant;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function getProductVariants($variant_id)
    {
        return ProductVariant::where('variant_id', $variant_id)->select('variant')->groupBy('variant_id', 'variant')->get();
    }


    


    public function index()
    {
        $data['products']   = Product::query()
                            ->when(request()->filled('title'), fn ($q) => $q->where('title', 'LIKE', '%' . request('title') . '%'))
                            ->whereHas('productVariantPrices', function ($q) {
                                if (request()->filled('price_from') || request()->filled('price_to')) {
                                    $q->whereBetween('price', [request('price_from'), request('price_to')]);
                                }

                                if (request()->filled('variant')) {
                                    $q->whereHas('productVariantOne', fn ($q) => $q->where('variant', request('variant')))
                                    ->orWhereHas('productVariantTwo', fn ($q) => $q->where('variant', request('variant')))
                                    ->orWhereHas('productVariantThree', fn ($q) => $q->where('variant', request('variant')));
                                }
                            })
                            ->with(['productVariantPrices' => function ($q) {
                                if (request()->filled('price_from') || request()->filled('price_to')) {
                                    $q->whereBetween('price', [request('price_from'), request('price_to')]);
                                }

                                if (request()->filled('variant')) {
                                    $q->whereHas('productVariantOne', fn ($q) => $q->where('variant', request('variant')))
                                    ->orWhereHas('productVariantTwo', fn ($q) => $q->where('variant', request('variant')))
                                    ->orWhereHas('productVariantThree', fn ($q) => $q->where('variant', request('variant')));
                                }
                            }])
                            ->when(request()->filled('date'), fn ($q) => $q->whereDate('created_at', request('date')))
                            ->paginate(10);

        $data['variants']   = Variant::query()
                            ->get()
                            ->map(function ($item) {
                                return [
                                    'title'             => $item->title,
                                    'productVariants'   => $this->getProductVariants($item->id),
                                ];
                            });

        return view('products.index', $data);
    }
    




    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }
    




    public function store(Request $request)
    {
        // 
    }
    




    public function show($product)
    {

    }
    




    public function edit(Product $product)
    {
        $variants = Variant::all();
        return view('products.edit', compact('variants'));
    }
    





    public function update(Request $request, Product $product)
    {
        //
    }
    




    public function destroy(Product $product)
    {
        //
    }
}
