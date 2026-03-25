<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Esdeveniment extends Model
{
    protected $fillable = ['nom', 'data_hora', 'recinte', 'descripcio', 'aforament'];

    public function seients()
    {
        return $this->hasMany(Seient::class);
    }
}
