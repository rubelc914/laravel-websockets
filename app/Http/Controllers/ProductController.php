<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        if($products->isEmpty()){
            return response()->json(['message' =>'data not found'],404);
        }
        $response = [
            'status'=>'true',
            'message' => 'all products',
            'data'  => $products
        ];
        return response()->json($response,200);
    }

    public function store(Request $request)
    {
        try{
            $validData = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'slug' => 'required|string|unique:products,slug|max:255|',
                'description' => 'string|max:300',
                'price' =>'required',
                'status' => 'numeric'
            ]);
            if
            ($validData->fails()){
                return response()->json(['message'=> $validData->errors()],400);
            }
            $product = Product::create([
                'name'  =>  $request->name,
                'slug'  => $request->slug,
                'description'=> $request->description,
                'price'=> $request->price,
                'status'=> $request->status
            ]);
            $response =[
                'status' => 'true',
                'message' =>'Product Created successfully',
                'product' =>$product
            ];
            return response()->json($response,200);
        }catch(Exception $e){
            Log::error($e->getMessage());
            return response()->json(['message'=> $e->getMessage()],500);
        }

    }
}
