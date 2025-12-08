<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
//    protected $connection = 'atiqah';
    protected $table = 'category';
    protected $fillable = ['main', 'sub'];

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'categoryID');
    }
}
