@extends('layouts.app')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>


    <div class="card">
        <form action="{{ route('search') }}" method="get"  class="card-header">
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="title" placeholder="Product Title" value="{{ !empty($title) ? $title:'' }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <select name="variant" id="" class="form-control" data-placeholder="Choose one variant">
                        @foreach ($variants as $variant)
                        <optgroup label="{{ $variant->title }}">
                            @foreach ($variant->variants->unique('variant') as $element)
                                <option @if (!empty($variant_name)) {{ $variant_name == $element->variant ? 'selected':'' }} @endif>
                                {{ $element->variant }}</option>
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
                        <input type="number" name="price_from" aria-label="price from"  value="{{ !empty($price_from) ? $price_from:'' }}" placeholder="From" class="form-control">
                        <input type="number" name="price_to" aria-label="price to"  value="{{ !empty($price_to) ? $price_to:'' }}" placeholder="To" class="form-control" >
                    </div>
                </div>
                <div class="col-md-2">
                    <input value="{{ !empty($date) ? $date:'' }}" type="date" name="date" placeholder="Date" class="form-control">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-response">
                <table class="table">
                    <thead>
                        <tr>
                            <th width="3%">#</th>
                            <th width="15%">Title</th>
                            <th width="44%">Description</th>
                            <th width="32%">Variant</th>
                            <th width="5%">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $key=> $product)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $product->title }} <br> Created at : {{ Carbon\Carbon::parse($product->created_at)->format('d-F-Y') }}</td>
                                <td>{{ $product->description }}</td>
                                <td>
                                    <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant-{{ $product->id }}">
                                @foreach ($product->variants as $variant)

                                        <dt class="col-sm-3 pb-0">
                                            {{ $variant->variant_one != null ? $variant->variant_one->variant:'' }} / {{ $variant->variant_two != null ? $variant->variant_two->variant:'' }} / {{ $variant->variant_three != null ? $variant->variant_three->variant:'' }}
                                        </dt>
                                        <dd class="col-sm-9">
                                            <dl class="row mb-0">
                                                <dt class="col-sm-4 pb-0">Price : {{ number_format($variant->price,2) }}</dt>
                                                <dd class="col-sm-8 pb-0">InStock : {{ number_format($variant->stock,2) }}</dd>
                                            </dl>
                                        </dd>
                                @endforeach
                                    </dl>
                                <button onclick="$('#variant-'+{{ $product->id }}).toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm text-center">
                                        <a href="{{ route('product.edit', 1) }}" class="btn btn-success">Edit</a>
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
                    <p>Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} out of {{$products->total()}}</p>
                </div>
                <div class="col-md-2">
                {{ $products->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>

@endsection
