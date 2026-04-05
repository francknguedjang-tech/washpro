<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = ['libelle', 'description', 'prix_unitaire', 'unite'];

    public function linges()
    {
        return $this->hasMany(Linge::class);
    }
}
