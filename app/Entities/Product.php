<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class Product extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = ['name', 'price', 'currency'];

    protected $visible = ['id', 'name', 'price', 'currency'];

    protected $casts = ['created_at' => 'timestamp', 'updated_at' => 'timestamp'];
}
