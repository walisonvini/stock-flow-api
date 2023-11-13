<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = ['street', 'number', 'neighborhood', 'city', 'state', 'country', 'postal_code'];

    protected $hidden = ['id', 'created_at', 'updated_at'];

    public function client()
    {
        return $this->hasOne(Client::class);
    }
}
