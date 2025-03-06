<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quota extends Model
{
    use HasFactory;

    protected $fillable = [
        'association_id', // REMPLACER user_id
        'grand',
        'moyen',
        'petit'
    ];

    // Relation vers l'utilisateur (association)
    public function association()
    {
        return $this->belongsTo(Association::class);
    }
}
