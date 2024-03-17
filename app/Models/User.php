<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasApiTokens, Filterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username', 'email', 'password', 'is_super_admin', 'api_token'
    ];

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

    public function userDetails()
    {
        return $this->hasOne(UserDetails::class);
    }

    public function formatPermissions() {
        $rights = ['read', 'write', 'create', 'delete'];
        $objects = ['customers', 'operators', 'equipment', 'locations', 'repairs', 'repair_details', 'uvv', 'repair_list', 'invoices', 'rentals', 'rental_invoices', 'offers'];
        $extra = ['access_um','access_internal_notes','access_prices_offer','access_time_tracking','access_manual_invoice'];
        $api_permissions = [];

        foreach($objects as $object)
        {
            $api_permissions[$object] = [];
            foreach($rights as $right)
            {
                $api_permissions[$object][$right] = $this->hasPermissionTo($right.' '.$object);
            }
        }

        foreach($extra as $extraPermissions) {
            $api_permissions[$extraPermissions] = $this->hasPermissionTo($extraPermissions);
        }

        $this->api_permissions = (object)$api_permissions;
        // return as object as that's what the UI sends and expects to receive back
        return $this->api_permissions;
    }
}
