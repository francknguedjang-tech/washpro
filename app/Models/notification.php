<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class notification extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'message', 'date_envoi', 'lu'];
    protected $casts = ['lu' => 'boolean'];
    public function user()
    {
        return $this->belongsTo(user::class);
    }
}
