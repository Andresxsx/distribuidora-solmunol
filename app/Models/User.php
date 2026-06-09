<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'rol'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

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

    public function esAdministrador(): bool
{
    return $this->rol === 'Administrador';
}

public function esOperativo(): bool
{
    return $this->rol === 'Operativo';
}

public function esDirectivo(): bool
{
    return $this->rol === 'Directivo';
}

public function puedeGestionarRegistros(): bool
{
    return in_array($this->rol, ['Administrador', 'Operativo'], true);
}

public function puedeConsultarDirectivo(): bool
{
    return in_array($this->rol, ['Administrador', 'Directivo'], true);
}
}
