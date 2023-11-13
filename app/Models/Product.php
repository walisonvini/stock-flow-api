<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['sku', 'name', 'price', 'description', 'category', 'client_id'];

    protected $hidden = ['client_id'];


    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function stock()
    {
        return $this->hasOne(Stock::class);
    }
}
