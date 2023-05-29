<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Variant;
use App\Services\ProductService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public $productService;





    public function __construct()
    {
        $this->productService = new ProductService;
    }





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
                            ->latest('id')
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
        $request->validate([
            'product_name'  => 'required|min:2|max:2',
            'product_sku'   => 'required|min:2|max:2|unique:products,sku',
        ]);

        try {

            DB::transaction(function () use ($request) {
                $this->productService->updateOrCreateProduct($request);
                $this->productService->storeProductVariants($request);
                $this->productService->storeProductVariantPrices($request);
            });

            return redirect()->route('product.index')->withMessage('Product has been created successfully');

        } catch (\Exception $ex) {
            return redirect()->back()->withError($ex->getMessage());
        }
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
