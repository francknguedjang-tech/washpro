<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class linge extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'description',
        'quantite',
        'depot_id',
        'service_id',
        'prix_unitaire'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
