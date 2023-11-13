<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'tax_id', 'phone', 'api_token', 'address_id'];

    protected $hidden = ['id', 'api_token', 'address_id', 'created_at', 'updated_at'];
    
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function stock()
    {
        return $this->hasMany(Stock::class);
    }
}
