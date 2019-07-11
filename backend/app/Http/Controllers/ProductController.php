<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\Product;

class ProductController extends Controller
{
    public function create(Request $request)
    {
        $v = Validator::make($request->all(), [
            'sku' => 'required',
            'name' => 'required|min:3',
            'price' => 'required',
            'box_volume'  => 'required',
            'materials_volume' => 'required',
            'width' => 'required',
            'height' => 'required',
            'length' => 'required',
            'stock' => 'required',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }
        $product = new Product();
        $product->sku = $request->sku;
        $product->name = $request->name;
        $product->description = $request->description;
        $product->price = $request->price;
        // dd(bcrypt($request->password));
        $product->box_volume = $request->box_volume;
        $product->materials_volume = $request->materials_volume;
        $product->width = $request->width;
        $product->height = $request->height;
        $product->length = $request->length;
        $product->stock = $request->stock;
        $product->save();

        $admin_user = auth()->user();

        $admin_user->registerLog('creates product '. $product->id);
        return response()->json(['status' => 'success'], 200);
    }

    public function products_list(){
        $product = Product::all();
        return $product;
    }

    /**
     * Update a Product
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function product_update(Request $request)
    {
        $v = Validator::make($request->all(), [
            'id' => 'required',
            'name' => 'min:3',
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }

        $product_id = $request->input('id');
        $product = Product::find($product_id);
        if($product){
            $product->fill($request->all());
            $product->save();

            $admin_user = auth()->user();
            $admin_user->registerLog('updates product '. $product->id);
            return $product;
        }
        
        return $this->respondWithError('no products found');
        
    }

    /**
     * Delete a Product
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function product_delete(Request $request)
    {
        $v = Validator::make($request->all(), [
            'id' => 'required'
        ]);
        if ($v->fails())
        {
            return response()->json([
                'status' => 'error',
                'errors' => $v->errors()
            ], 422);
        }

        $product_id = $request->input('id');
        $deletion = Product::find($product_id)->delete();

        $admin_user = auth()->user();

        $admin_user->registerLog('deletes product '. $product_id);

        return response()->json([
            'status' => 'done'
        ], 200);
        
    }
}
