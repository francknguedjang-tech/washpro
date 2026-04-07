<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Code_acces extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code',
        'is_used'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
