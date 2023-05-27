<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = ['userIdSeller', 'name', 'desc', 'price', 'stock'];


    public function user()
    {
        return $this->belongsTo(User::class, 'userIdSeller');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'inventoriesId');
    }
}
