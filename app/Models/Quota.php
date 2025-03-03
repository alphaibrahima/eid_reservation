<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quota extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'grand',
        'moyen',
        'petit',
    ];

    // Relation vers l'utilisateur (association)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
