<?php


namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;

class ProductService
{
    public $product;





    public function updateOrCreateProduct($request)
    {
        $this->product      = Product::updateOrCreate(['id' => $request->product_id], [
            'title'         => $request->product_name,
            'sku'           => $request->product_sku,
            'description'   => $request->product_description,
        ]);
    }





    public function storeProductVariants($request)
    {
        $this->product->productVariants()->delete();

        foreach($request->product_variant as $productVariant) {
            foreach($productVariant['value'] as $variant) {
                ProductVariant::create([
                    'variant' => $variant,
                    'variant_id' => $productVariant['option'],
                    'product_id' => optional($this->product)->id,
                ]);
            }
        }
    }





    public function storeProductVariantPrices($request)
    {
        $this->product->productVariantPrices()->delete();

        foreach($request->product_preview as $productPreview) {

            $productVariants            = explode('/', $productPreview['variant']);

            $product_variant_one        = optional(ProductVariant::where(['product_id' => optional($this->product)->id, 'variant' => isset($productVariants[0]) && $productVariants[0] != "" ? $productVariants[0] : null])->first())->id;
            $product_variant_two        = optional(ProductVariant::where(['product_id' => optional($this->product)->id, 'variant' => isset($productVariants[1]) && $productVariants[1] != "" ? $productVariants[1] : null])->first())->id;
            $product_variant_three      = optional(ProductVariant::where(['product_id' => optional($this->product)->id, 'variant' => isset($productVariants[2]) && $productVariants[2] != "" ? $productVariants[2] : null])->first())->id;

            ProductVariantPrice::create([
                'product_variant_one'   => $product_variant_one,
                'product_variant_two'   => $product_variant_two,
                'product_variant_three' => $product_variant_three,
                'price'                 => $productPreview['price'],
                'stock'                 => $productPreview['stock'],
                'product_id'            => optional($this->product)->id,
            ]);
        }
    }
}
