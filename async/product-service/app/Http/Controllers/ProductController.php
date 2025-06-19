<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function index()
    {
        return response()->json(Product::all(), 200);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);


        $product = Product::create($request->only(['name', 'description', 'price', 'stock']));

        return response()->json($product, 201);
    }


    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product, 200);
    }

   
    public function update(Request $request, $id)
    {
       
        $product = Product::findOrFail($id);

        
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'stock' => 'sometimes|required|integer|min:0', 
        ]);

        
        $product->update($validated);

        return response()->json($product, 200);  
    }

    
    public function destroy($id)
    {
       
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully'], 200);
    }
}
