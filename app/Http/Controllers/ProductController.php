<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\Product;
use App\Http\Resources\Product\ProductResource;
use App\Http\Resources\Product\ProductCollection;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pagination = 12;
        if(request()->category) {
            $category = Category::where('slug', request()->category)->get()->first();
            $products = Product::where('category_id', $category->id);
            $categoryName = $category->name;
        } else if(request()->tag) {
            $tag = Tag::where('slug', request()->tag)->get()->first();
            $products = $tag->products();
            $tagName = $tag->name;
        } else {
            $products = Product::where('featured', true);
            $categoryName = 'Featured';
        }
        if(request()->sort == 'low_high') {
            $products = $products->orderBy('price')->paginate($pagination);
        } else if(request()->sort == 'high_low') {
            $products = $products->orderBy('price', 'desc')->paginate($pagination);
        } else {
            $products = $products->inRandomOrder()->paginate($pagination);
        }
        $categories = Category::all();
        $tags = Tag::all();
        return view('shop')->with([
            'products' => $products,
            'categories'=> $categories,
            'tags'=> $tags,
        'categoryName' => $categoryName ?? null,
            'tagName' => $tagName ?? null
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreProductRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreProductRequest $request)
    {

        $request->validate([

        ])
        $product = new Product;
        $product->name = $request->name;
        $product->slug  = $request->slug;
        $product->detail = $request->description;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->discount = $request->discount;
        $product->user_id = Auth::id();
        $product->save();
        return response([
            'data' => new ProductResource($product)
        ],201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        $images = json_decode($product->images);
        $mightLike = Product::where('slug', '!=', $product->slug)->mightAlsoLike()->get();
        $stockLevel = getStockLevel($product->quantity);
        return view('product')->with([
            'product' => $product,
            'mightLike' => $mightLike,
            'images' => $images,
            'stockLevel' => $stockLevel
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateProductRequest  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        $this->ProductUserCheck($product);
        $request['detail'] = $request->description;
        unset($request['description']);
        $product->update($request->all());
        return response([
            'data' => new ProductResource($product)
        ],Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $this->ProductUserCheck($product);
        $product->delete();
        return response(null,204);
    }
    public function ProductUserCheck($product){
        if (Auth::id() !== $product->user_id) {
           throw new ProductNotBelongsToUser;
           
        }
    }
    public function search($query) {
        if(strlen($query) < 3) 
            return back()->with('error', 'minimum query length is 3');
        $products = Product::search($query)->paginate(10);
        return view('search')->with(['products' => $products, 'query' => $query]);
    }
    
}
