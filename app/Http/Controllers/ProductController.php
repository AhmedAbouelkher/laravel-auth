<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Product::query()
            ->with(['user', 'media'])
            ->latest()
            ->paginate();
    }

    /**
     * Fetch User's products.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function userProducts(Request $request)
    {
        return Product::query()
            ->with(['media'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $productFields = $request->validate([
            'name' => 'required',
            'description' => 'min:10',
            'price' => 'required:numeric',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $productFields['user_id'] = auth()->id();
        $productFields['slug'] = Str::slug($request->name);

        $product = Product::create($productFields);

        $product->addMediaFromRequest('image')->toMediaCollection('products');

        return response()->json($product, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $productFields = $request->validate([
            'name' => 'required',
            'description' => 'min:10',
            'price' => 'required:numeric',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Product::destroy($id);
        return response()->json(['message' => 'Product deleted']);
    }
}
