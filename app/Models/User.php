<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\Filters\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
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

    public function formatPermissions()
    {
        $rights = ['read', 'write', 'create', 'delete'];
        $objects = ['customers', 'operators', 'equipment', 'locations', 'repairs', 'repair_details', 'uvv', 'repair_list', 'invoices', 'rentals', 'rental_invoices', 'offers'];
        $extra = ['access_um', 'access_internal_notes', 'access_prices_offer', 'access_time_tracking', 'access_manual_invoice'];
        $api_permissions = [];
        $permissions = [];

        $model_has_roles = DB::table('model_has_roles')->where('model_id', auth()->id())
            ->pluck('role_id')->toArray();
        $role_has_permissions = DB::table('role_has_permissions')
            ->join('permissions', 'permissions.id', '=', 'role_has_permissions.permission_id')
            ->whereIn('role_has_permissions.role_id', $model_has_roles)
            ->pluck('permissions.name')->toArray();

        foreach($objects as $object) {
            $api_permissions[$object] = [];
            foreach($rights as $right) {
                $text = $right.' '.$object;
                if (in_array($text, $role_has_permissions)) {
                    $permissions[] = [
                        'user_id' => auth()->id(),
                        'permission' => $text
                    ];
                }
                $api_permissions[$object][$right] = in_array($text, $role_has_permissions);
            }
        }

        foreach ($extra as $extraPermissions) {
            $api_permissions[$extraPermissions] = in_array($extraPermissions, $role_has_permissions);
            if (in_array($extraPermissions, $role_has_permissions)) {
                $permissions[] = [
                    'user_id' => auth()->id(),
                    'permission' => $extraPermissions
                ];
            }
        }

        DB::table('user_permissions')->where('user_id', auth()->id())->delete();
        DB::table('user_permissions')->insert($permissions);
        session()->put('user_permissions', $permissions);

        $this->api_permissions = (object)$api_permissions;
        return $this->api_permissions;
    }

    public function CP($permission): bool
    {
        $permissions = DB::table('user_permissions')->where('user_id', auth()->id())->pluck('permission')->toArray();
        return in_array($permission, $permissions);
    }

    public function CPA($permissions): bool
    {
        $permissionsFromDB = DB::table('user_permissions')->where('user_id', auth()->id())->pluck('permission')->toArray();
        $check = false;
        foreach ($permissions as $permission) {
            if (in_array($permission, $permissionsFromDB)) {
                $check = true;
            }
        }
        return $check;
    }

//    public function formatPermissions() {
//        $rights = ['read', 'write', 'create', 'delete'];
//        $objects = ['customers', 'operators', 'equipment', 'locations', 'repairs', 'repair_details', 'uvv', 'repair_list', 'invoices', 'rentals', 'rental_invoices', 'offers'];
//        $extra = ['access_um','access_internal_notes','access_prices_offer','access_time_tracking','access_manual_invoice'];
//        $api_permissions = [];
//
//        foreach($objects as $object)
//        {
//            $api_permissions[$object] = [];
//            foreach($rights as $right)
//            {
//                $api_permissions[$object][$right] = $this->hasPermissionTo($right.' '.$object);
//            }
//        }
//
//        foreach($extra as $extraPermissions) {
//            $api_permissions[$extraPermissions] = $this->hasPermissionTo($extraPermissions);
//        }
//
//        $this->api_permissions = (object)$api_permissions;
//        // return as object as that's what the UI sends and expects to receive back
//        return $this->api_permissions;
//    }
}
