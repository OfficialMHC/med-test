@extends('layouts.app')

@section('content')
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Create Product</h1>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session()->get('error'))
        <div class="alert alert-danger">
            <button type="button" class="close" data-dismiss="alert">
                <i class="ace-icon fa fa-times"></i>
            </button>
            <strong>
                <i class="ace-icon fa fa-times"></i>
                Error !
            </strong>
            {{ session()->get('error') }}
        </div>
    @endif

    <form action="{{ route('product.store') }}" method="post" autocomplete="off" spellcheck="false" enctype="multipart/form-data">
        @csrf
        <section>
            <div class="row">
                <div class="col-md-6">
                    <!-- Product-->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">Product</h6>
                        </div>
                        <div class="card-body border">
                            <div class="form-group">
                                <label for="product_name">Product Name</label>
                                <input 
                                    type="text"
                                    name="product_name"
                                    id="product_name"
                                    required
                                    placeholder="Product Name"
                                    class="form-control"
                                    value="{{ old('product_name') }}"
                                >
                            </div>
                            <div class="form-group">
                                <label for="product_sku">Product SKU</label>
                                <input 
                                    type="text" name="product_sku"
                                    id="product_sku"
                                    required
                                    placeholder="Product SKU"
                                    class="form-control"
                                    value="{{ old('product_sku') }}"
                                >
                            </div>
                            <div class="form-group mb-0">
                                <label for="product_description">Description</label>
                                <textarea 
                                    name="product_description"
                                    id="product_description"
                                    required
                                    rows="4"
                                    class="form-control"
                                >{{ old('product_description') }}
                                </textarea>
                            </div>
                        </div>
                    </div>
                    <!-- Media-->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between"><h6 class="m-0 font-weight-bold text-primary">Media</h6></div>
                        <div class="card-body border">
                            <input type="file" name="images[]" multiple>
                            {{-- <div id="file-upload" class="dropzone dz-clickable">
                                <div class="dz-default dz-message"><span>Drop files here to upload</span></div>
                            </div> --}}
                        </div>
                    </div>
                </div>
                <!--                Variants-->
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Variants</h6>
                        </div>
                        <div class="card-body pb-0" id="variant-sections">
                            @if (old('product_variant'))
                                @foreach (old('product_variant') as $key => $productVariant)
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Option</label>
                                                <select id="select2-option-{{ $key }}" data-index="{{ $key }}" name="product_variant[{{ $key }}][option]" class="form-control custom-select select2 select2-option">
                                                    <option value="1" {{ $productVariant['option'] == 1 ? 'selected' : '' }}>
                                                        Color
                                                    </option>
                                                    <option value="2" {{ $productVariant['option'] == 2 ? 'selected' : '' }}>
                                                        Size
                                                    </option>
                                                    <option value="6" {{ $productVariant['option'] == 6 ? 'selected' : '' }}>
                                                        Style
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label class="d-flex justify-content-between">
                                                    <span>Value</span>
                                                    <a href="#" class="remove-btn" data-index="{{ $key }}" onclick="removeVariant(event, this);">Remove</a>
                                                </label>
                                                <select id="select2-value-{{ $key }}" data-index="{{ $key }}" name="product_variant[{{ $key }}][value][]" class="select2 select2-value form-control custom-select" multiple="multiple">
                                                    @foreach($productVariant['value'] ?? [] as $value)
                                                            <option value="{{ $value }}" selected>{{ $value }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        <div class="card-footer bg-white border-top-0" id="add-btn">
                            <div class="row d-flex justify-content-center">
                                <button class="btn btn-primary add-btn" onclick="addVariant(event);">
                                    Add another option
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow">
                        <div class="card-header text-uppercase">Preview</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                    <tr class="text-center">
                                        <th width="33%">Variant</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                    </tr>
                                    </thead>
                                    <tbody id="variant-previews">
                                        @if (old('product_preview'))
                                            @foreach (old('product_preview') as $key => $productPreview)
                                                <tr>
                                                    <th>
                                                        <input type="hidden" name="product_preview[{{ $key }}][variant]" value="{{ $productPreview['variant'] }}">
                                                        <span class="font-weight-bold">{{ $productPreview['variant'] }}</span>
                                                    </th>
                                                    <td>
                                                        <input type="text" class="form-control" value="{{ $productPreview['price'] }}" name="product_preview[{{ $key }}][price]" required>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control" value="{{ $productPreview['stock'] }}" name="product_preview[{{ $key }}][stock]">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="btn btn-lg btn-primary">Save</button>
            <button type="button" class="btn btn-secondary btn-lg">Cancel</button>
        </section>
    </form>
@endsection

@push('page_js')
    {{-- <script type="text/javascript" src="{{ asset('js/product.js') }}"></script> --}}
    @include('products.script')
@endpush
