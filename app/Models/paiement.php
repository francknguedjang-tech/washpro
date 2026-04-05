<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class paiement extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        'depot_id',
        'montant',
        'mode_paiement',
        'date_paiement'
    ];

    public function depot()
    {
        return $this->belongsTo(depot::class);
    }
}
