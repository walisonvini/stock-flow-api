<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Repository\API\ClientRepository;

class ClientController extends Controller
{
    private $client;

    public function __construct(Request $request)
    {
        $this->client = new ClientRepository($request->header('Authorization'));
    }

    public function show()
    {
        $client = $this->client->getClientWithAddress();

        return response()->json([
            'status' => 'Success',
            'client' => $client
        ], 200);
       
    }
}
