<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        $data = array(
            'products' => Product::orderBy('id', 'desc')->paginate(2),
            'variants' => Variant::all(), 
        );
        return view('products.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {        

        $basic = array(
            'title' => $request->title, 
            'sku' => $request->sku, 
            'description' => $request->description, 
        );
        // insert basic information of product and get id
        $product_id = Product::insertGetId($basic);
        if ($product_id>0) {
            // data insert in product variants
            foreach ($request->product_variant as $product_variant) {
                foreach ($product_variant['tags'] as $tag) {
                    $variants = array(
                        'variant' => $tag, 
                        'variant_id' => $product_variant['option'],
                        'product_id' => $product_id,
                    );
                    ProductVariant::insert($variants);
                }
            }

            // data insert in prices table
            foreach ($request->product_variant_prices as $value) {
                $data['title'] = explode('/',$value['title']);
                $product_variant_one = ProductVariant::where('variant',$data['title'][0])->orderBy('id','desc')->first();
                $product_variant_two = ProductVariant::where('variant',$data['title'][1])->orderBy('id','desc')->first();
                $product_variant_three = ProductVariant::where('variant',$data['title'][2])->orderBy('id','desc')->first();
                $prices = array(
                    'price' =>  $value['price'], 
                    'stock' =>  $value['stock'], 
                    'product_id' =>  $product_id, 
                    'product_variant_one' =>  $product_variant_one->id, 
                    'product_variant_two' =>  $product_variant_two->id, 
                    'product_variant_three' =>  $product_variant_three->id, 
                );
                ProductVariantPrice::insert($prices);
            }

            return response()->json(['status'=>true, 'message'=>'Product saved successfully!']);

        } else {
            # code...
        }
        

    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $variants = Variant::all();
        return view('products.edit', compact('variants'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }

    /**
     * Display the searched resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        // dd($request->all());
        $title = $request->title;
        $variant = $request->variant;
        $price_from = $request->price_from;
        $price_to = $request->price_to;
        $date = $request->date;
        $products = Product::join('product_variants', 'product_variants.product_id','products.id')
                        ->join('product_variant_prices', 'product_variant_prices.product_id','products.id')
                        ->where('products.title','LIKE', '%'.$title.'%')
                        ->orWhere('product_variants.variant','LIKE', '%'.$variant.'%')
                        ->orWhereBetween('product_variant_prices.price',[$price_from, $price_to])
                        ->groupBy('product_variants.variant')
                        ->paginate(2);
        $data = array(
            'title' => $title,
            'variant_name' => $variant,
            'price_from' => $price_from,
            'price_to' => $price_to,
            'date' => $date,
            'products' => $products,
            'variants' => Variant::all(), 
        );
        return view('products.index',$data);
    }
}
