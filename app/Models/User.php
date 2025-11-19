<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Role;
use App\Models\Permission;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_admin',
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
            'banned_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Get the roles for the user.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role')->withTimestamps();
    }

    /**
     * Get the permissions for the user (through roles and direct).
     */
    public function permissions()
    {
        $rolePermissions = $this->roles()->with('permissions')->get()->pluck('permissions')->flatten();
        $directPermissions = $this->belongsToMany(Permission::class, 'user_permission')->get();
        
        return $directPermissions->merge($rolePermissions)->unique('id');
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission($permissionSlug)
    {
        // Super admin (is_admin = true) has all permissions
        if ($this->is_admin) {
            return true;
        }

        // Check direct permissions
        $hasDirectPermission = $this->belongsToMany(Permission::class, 'user_permission')
            ->where('slug', $permissionSlug)
            ->exists();

        if ($hasDirectPermission) {
            return true;
        }

        // Check role permissions
        return $this->roles()
            ->whereHas('permissions', function($query) use ($permissionSlug) {
                $query->where('slug', $permissionSlug);
            })
            ->exists();
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissionSlugs)
    {
        foreach ($permissionSlugs as $slug) {
            if ($this->hasPermission($slug)) {
                return true;
            }
        }
        return false;
    }
}
