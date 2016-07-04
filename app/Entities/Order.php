<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Order extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = ['client_id', 'product_id', 'total_price', 'currency'];

    protected $casts = ['created_at' => 'timestamp'];

    protected $visible = ['id', 'client_id', 'product_id', 'client', 'product', 'total_price', 'currency', 'created_at'];

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
