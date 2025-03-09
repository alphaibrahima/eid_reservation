<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;



class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone', // Ajouter
        'contact_phone', 
        'address',
        'registration_number',
        'role',
        'association_id',
        'is_active', // Ajoute cette ligne
    ];

    // Optionnel : Valeur par défaut
    protected $attributes = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified' => 'boolean',
        'password' => 'hashed',
    ];

    // Vérifier si l'utilisateur a le rôle 'association'
    public function isAssociation()
    {
        return $this->role === 'association';  // Vérifie que le rôle est bien 'association'
    }

    public function association()
    {
        return $this->belongsTo(Association::class); // Au lieu de User
    }
    
    public function buyers()
    {
        return $this->hasMany(User::class, 'association_id');
    }

    // Ajoute cette méthode pour la relation
    public function quota()
    {
        return $this->hasOne(Quota::class);
    }

    // Relation inverse CORRIGÉE
    public function slots(): HasMany
    {
        return $this->hasMany(Slot::class, 'association_id');
    }
}
