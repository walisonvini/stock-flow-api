<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ItemStoreRequest;
use App\Http\Requests\ItemUpdateRequest;

use App\Http\Repository\API\ItemRepository;

class ItemController extends Controller
{
    protected $item;
    
    public function __construct(Request $request)
    {
        $this->item = new ItemRepository($request->header('Authorization'));
    }

    public function index($product_id)
    {
        $items = $this->item->getAllItems($product_id);

        if($items) {
            return response()->json([
                'status' => 'Success',
                'items' => $items
            ], 200);
        } else {
            return response()->json([
                'status' => 'Error',
                'message' => 'Items not found'
            ], 404);
        }
    }

    public function store(ItemStoreRequest $request)
    {
        $newItem = $this->item->createItem($request);

        if($newItem) {
            return response()->json([
                'status' => 'Success',
                'message' => 'Item created successfully',
                'item' => $newItem
            ], 201);
        } else {
            return response()->json([
                'status' => 'Error',
                'message' => 'Product does not exist.'
            ], 500);
        }
    }

    public function show($identifier)
    {
        $item = $this->item->getItem($identifier);

        if($item) {
            return response()->json([
                'status' => 'Success',
                'item' => $item
            ], 200);
        } else {
            return response()->json([
                'status' => 'Error',
                'message' => 'Item not found'
            ], 404);
        }
    }

    public function update(ItemUpdateRequest $request, $identifier)
    {
        $item = $this->item->updateItem($identifier, $request);

        if($item) {
            return response()->json([
                'status' => 'Success',
                'item' => 'Item updated successfully'
            ], 200);
        } else {
            return response()->json([
                'status' => 'Error',
                'message' => 'Item not found'
            ], 404);
        }
    }

    public function destroy($identifier)
    {
        $item = $this->item->deleteItem($identifier);

        if($item) {
            return response()->json([
                'status' => 'Success',
                'message' => 'Item deleted successfully'
            ], 200);
        }
        else {
            return response()->json([
                'status' => 'Error',
                'message' => 'Item not found'
            ], 404);
        }
    }
}
