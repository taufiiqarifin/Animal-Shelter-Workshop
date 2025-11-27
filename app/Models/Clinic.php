<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clinic extends Model
{
    //  protected $connection = 'pgsql_remote';

    protected $table = 'clinic';
    protected $fillable = ['name', 'address', 'contactNum', 'latitude', 'longitude'];

    public function vets()
    {
        return $this->hasMany(Vet::class, 'clinicID');
    }
}
