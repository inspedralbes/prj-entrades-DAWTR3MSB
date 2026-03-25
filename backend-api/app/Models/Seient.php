<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seient extends Model
{
    protected $fillable = ['esdeveniment_id', 'fila', 'numero', 'estat', 'preu'];

    public function esdeveniment()
    {
        return $this->belongsTo(Esdeveniment::class);
    }
}
