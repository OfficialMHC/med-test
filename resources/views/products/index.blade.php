@extends('layouts.app')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>




    
    <div class="card">
        <form action="" method="get" class="card-header">
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="title" placeholder="Product Title" class="form-control" value="{{ request('title') }}">
                </div>
                <div class="col-md-2">
                    <select name="variant" id="" class="form-control">
                        <option value="">-- Select A Variant --</option>
                        @foreach ($variants as $variant)
                            <optgroup label="{{ $variant['title'] }}">
                                @foreach ($variant['productVariants'] as $productVariant)
                                    <option value="{{ $productVariant['variant'] }}" {{ request('variant') == $productVariant['variant'] ? 'selected' : '' }}>
                                        {{ $productVariant['variant'] }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" name="price_from" aria-label="First name" placeholder="From" value="{{ request('price_from') }}" class="form-control">
                        <input type="text" name="price_to" aria-label="Last name" placeholder="To" value="{{ request('price_to') }}" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" placeholder="Date" class="form-control" value="{{ request('date') }}">
                </div>
                <div class="col-md-1">
                    <a href="{{ request()->url() }}" class="btn btn-dark ml-1 float-right"><i class="fa fa-sync"></i></a>
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>





        <div class="card-body">
            <div class="table-response">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="2%">#</th>
                            <th width="18%">Title</th>
                            <th width="20%">Description</th>
                            <th width="50%">Variant</th>
                            <th width="10%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    {{ $product->title }}
                                    <br>
                                    {{ date('d-M-Y', strtotime($product->created_at)) }}
                                </td>
                                <td>{{ Str::limit($product->description, 50, '...') }}</td>
                                <td>
                                    <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant{{ $product->id }}">
                                        @foreach ($product->productVariantPrices as $productVariantPrice)
                                            <dt class="col-sm-3 pb-0">
                                                @php
                                                    $variation = "";

                                                    $variant_1 = optional(\App\Models\ProductVariant::where('id', $productVariantPrice->product_variant_one)->first())->variant;
                                                    $variant_2 = optional(\App\Models\ProductVariant::where('id', $productVariantPrice->product_variant_two)->first())->variant;
                                                    $variant_3 = optional(\App\Models\ProductVariant::where('id', $productVariantPrice->product_variant_three)->first())->variant;
                                
                                                    $variation .= $variant_1 . ($variant_1 && $variant_2 ? '/' : '') . $variant_2 . ($variant_2 && $variant_3 ? '/' : '') . $variant_3;
                                                @endphp
                                                {{ $variation }}
                                            </dt>
                                            <dd class="col-sm-9">
                                                <dl class="row mb-0">
                                                    <dt class="col-sm-4 pb-0">Price : {{ number_format($productVariantPrice->price, 2) }}</dt>
                                                    <dd class="col-sm-8 pb-0">InStock : {{ number_format($productVariantPrice->stock, 2) }}</dd>
                                                </dl>
                                            </dd>
                                        @endforeach 
                                    </dl>
                                    <button onclick="$('#variant{{ $product->id }}').toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button>
                                </td> 
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('product.edit', $product->id) }}" class="btn btn-success">Edit</a>
                                    </div>    
                                </td> 
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>





        <div class="card-footer">
            <div class="row justify-content-between">
                <div class="col-md-6">
                    <p>Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} out of {{ $products->total() }}</p>
                </div>
                <div class="col-md-2">
                    {{ $products->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
