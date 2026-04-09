<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = ['seient_id', 'customer_name', 'customer_email', 'amount'];

    public function seient()
    {
        return $this->belongsTo(Seient::class);
    }
}
