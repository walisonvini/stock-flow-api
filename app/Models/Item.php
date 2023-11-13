<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['identifier', 'expiration_date', 'shelf', 'aisle', 'level', 'condition', 'status', 'product_id'];

    public function product()
    {
        return $this->belongsTo(Produto::class);
    }
}
