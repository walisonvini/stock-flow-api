<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;

use App\Http\Repository\API\ProductRepository;

class ProductController extends Controller
{
    protected $product;

    public function __construct(Request $request)
    {
        $this->product = new ProductRepository($request->header('Authorization'));
    }

    public function index()
    {
        $products = $this->product->getAllProducts();

        if($products) {
            $response = [
                'status' => 'Success',
                'products' => $products,
            ];
            
            return response()->json($response, 200);
        }
        else {
            return response()->json([
                'status' => 'Error',
                'message' => 'Products not found'
            ], 404);
        }
    }

    public function store(ProductStoreRequest $request)
    {
        $newProduct = $this->product->createProduct($request);

        if($newProduct) {
            $response = [
                'status' => 'Success',
                'message' => 'Product created successfully',
                'product' => $newProduct,
            ];
            
            return response()->json($response, 201);
        }
        else {
            return response()->json([
                'status' => 'Error',
                'message' => 'The product cannot be created'
            ], 500);
        }
    }

    public function show($id)
    {
        $product = $this->product->getProduct($id);

        if($product) {
            $response = [
                'status' => 'Success',
                'product' => $product,
            ];
            
            return response()->json($response, 200);
        }
        else {
            return response()->json([
                'status' => 'Error',
                'message' => 'Product not found'
            ], 404);
        }
    }

    public function update(ProductUpdateRequest $request, $id)
    {
        $product = $this->product->updateProduct($request, $id);

        if($product) {
            $response = [
                'status' => 'Success',
                'message' => 'Product updated successfully'
            ];
            
            return response()->json($response, 200);
        }
        else {
            return response()->json([
                'status' => 'Error',
                'message' => 'Product not found'
            ], 404);
        }
    }

    public function destroy($id)
    {
        $product = $this->product->deleteProduct($id);

        if($product) {
            $response = [
                'status' => 'Success',
                'message' => 'Product deleted successfully'
            ];
            
            return response()->json($response, 200);
        }
        else {
            return response()->json([
                'status' => 'Error',
                'message' => 'Product not found'
            ], 404);
        }
    }
}
