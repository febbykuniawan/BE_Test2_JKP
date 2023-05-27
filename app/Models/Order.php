<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'userIdSeller',
        'userIdCustomer',
        'inventoriesId',
        'date',
        'status',
        'quantity',
        'totalPrice',
    ];

    public function userSeller()
    {
        return $this->belongsTo(User::class, 'userIdSeller');
    }

    public function userCustomer()
    {
        return $this->belongsTo(User::class, 'userIdCustomer');
    }

    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventoriesId');
    }
}
