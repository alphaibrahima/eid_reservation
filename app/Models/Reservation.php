<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'slot_id', 
        'size', 
        'code', 
        'status'
    ];

    public function slot()
    {
        return $this->belongsTo(Slot::class);
    }


    public function association()
    {
        return $this->belongsTo(User::class, 'association_id');
    }
}
