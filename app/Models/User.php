<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'store_id',
        'name',
        'email',
        'password',
        'role_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn(string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    // Relation vers le modèle Role
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function language()
    {
        return $this->hasOne(Language::class);
    }

    public function getLocaleAttribute()
    {
        return $this->language?->locale ?? config('app.locale');
    }

    /**
     * Détecte si l'utilisateur est de type Boulangerie
     */
    public function isBakeryUser(): bool
    {
        // Les Super Admins ne sont jamais considérés comme des utilisateurs boulangerie standards
        if ($this->hasRoleString('Super Admin')) {
            return false;
        }

        $bakeryRoles = ['admin', 'geran_depot_magasin', 'geran_depot_usine', 'geran_depot_boulangerie'];

        // L'attribut 'role' (string) est utilisé par la boulangerie
        $roleValue = $this->getAttribute('role');

        if (is_string($roleValue) && in_array($roleValue, $bakeryRoles)) {
            return true;
        }

        return false;
    }

    /**
     * Méthode unifiée pour vérification de rôle
     */
    public function hasRoleString(string $role): bool
    {
        // On récupère le nom du rôle via la relation role_id
        $roleName = $this->role?->name;

        if ($roleName === 'Super Admin') {
            return true;
        }

        if ($roleName === $role) {
            return true;
        }

        // On vérifie aussi l'ancienne colonne 'role' (string) pour la compatibilité
        if ($this->getAttribute('role') === $role) {
            return true;
        }

        return false;
    }

    /**
     * Accessor pour obtenir le nom du rôle de façon unifiée
     */
    public function getRoleNameAttribute(): string
    {
        return $this->role?->name ?? $this->getAttribute('role') ?? 'user';
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function inventaires()
    {
        return $this->hasMany(Cloture::class);
    }

    public function syntheses()
    {
        return $this->hasMany(Synthese::class);
    }
}
