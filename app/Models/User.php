<?php

namespace App\Models;

use App\Models\Permission\Group;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject, CanResetPasswordContract
{
    use Authenticatable, Authorizable, HasFactory, Notifiable, CanResetPassword;

    const STATUS_PENDING = "waiting_approval";
    const STATUS_APPROVE = "approve";
    const STATUS_REJECT = "reject";

    const ROLE_ADMINISTRATOR = "AD";
    const ROLE_DISTRIBUTOR = "DI";
    const ROLE_GENERAL_ADMIN = "GA";
    const ROLE_HEAD_OFFICE = "HO";
    const ROLE_SALES = "SA";

    protected $table = "user";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "created_at",
        "user_id",
        "full_name",
        "email",
        "username",
        "kode_group",
        "kode_area",
        "kode_distributor",
        "status",
    ];

    /**
     * The attributes excluded from the model"s JSON form.
     *
     * @var array
     */
    protected $hidden = [
        "password",
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function userGroup()
    {
        return $this->belongsTo(Group::class, 'kode_group', 'kode_group');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'kode_area', 'kode_area');
    }

    public function distributor()
    {
        return $this->belongsTo(Distributor::class, 'kode_distributor', 'kode_distributor');
    }

    public function hasRole($role = null)
    {
        return $this->userGroup()->first()->kode_group == $role || empty($role);
    }

    public function hasPermission($permission = null)
    {
        return $this->userGroup()->first()->permissions()->where('permission.kode_permission', $permission)->count() > 0 || empty($permission);
    }
}
