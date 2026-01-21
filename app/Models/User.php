<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


/**
 * Class User
 * 
 * @property int $id
 * @property string $name
 * @property string $email
 * @property int $role_id
 * @property string $password
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Role $role
 * @property Collection|Sale[] $sales
 *
 * @package App\Models
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'role_id',
        'location_id',
        'isle_id',
        'employee_id',
        'password',
        'deleted'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function isle()
    {
        return $this->belongsTo(Isle::class);
    }

    /*     public function hasRole($roles)
    {
        // Obtiene el nombre del rol del usuario
        $userRole = $this->role ? $this->role->name : null;

        // Permite pasar un solo rol o un array de roles
        $roles = is_array($roles) ? $roles : [$roles];

        return in_array($userRole, $roles);
    } */

    public function cash_closes()
    {
        return $this->hasMany(CashClose::class);
    }

    public function measurements()
    {
        return $this->hasMany(Measurement::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
