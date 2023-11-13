<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Repository\API\StockRepository;

class StockController extends Controller
{
    private $stock;

    public function __construct(Request $request)
    {
        $this->stock = new StockRepository($request->header('Authorization'));
    }

    public function index()
    {
        $allStock = $this->stock->getAllStock();

        return response()->json([
            'status' => 'Success',
            'stock' => $allStock
        ], 200);
    }

    public function show($id)
    {
        //
    }
   
    public function update(Request $request, $id)
    {
        
    }
}
