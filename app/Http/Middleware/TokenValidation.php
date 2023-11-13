<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Client;

class TokenValidation
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function handle(Request $request, Closure $next)
    {
        $api_token =  $request->header('Authorization');

        if(!$api_token) return response()->json(['status' => 'Error', 'message' => 'Token not found'], 404);
    
        $client = $this->client->where('api_token', '=', $api_token)->first();

        if(!$client) return response()->json(['status' => 'Error', 'message' => 'Invalid token'], 422);

        return $next($request);
    }
}
