<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depot extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_depot',
        'poid',
        'etat',
        'prix_total',
        'service_id',
        'client_id',
        'receptionniste_id',
        'date_retrait_prevue',
        'etat_paiement'
    ];

    public function getTotalPoidsAttribute()
    {
        return $this->linges->filter(function($linge) {
            return $linge->service && str_contains(strtolower($linge->service->unite), 'kg');
        })->sum('quantite');
    }

    public function getResteAPayerAttribute()
    {
        return floatval($this->prix_total) - floatval($this->paiements()->sum('montant'));
    }

    public function getReferenceAttribute()
    {
        return 'DP-' . date('Y', strtotime($this->date_depot)) . '-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }
    public function receptionniste()
    {
        return $this->belongsTo(User::class, 'receptionniste_id');
    }
    public function linges()
    {
        return $this->hasMany(Linge::class);
    }
    public function paiements()
    {
        return $this->hasMany(Paiement::class, 'depot_id');
    }

    public function service(){
        return $this->belongsTo(Service::class, 'service_id');
    }
}
