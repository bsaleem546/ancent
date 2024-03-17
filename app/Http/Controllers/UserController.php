<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserCollectionResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserDetails;
use App\Traits\Filters\UserFilters;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
{
    private function loginValidator(array $data)
    {
        $validator = Validator::make($data, [
            'username' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'max:50']
        ]);

        return $validator;
    }

    private function userValidator(array $data)
    {
        $validator = Validator::make($data, [
            'username' => ['required', 'string', 'max:100'],
            // 'email' => ['required',  'email'],
            // 'first_name' => ['present',  'string'],
            // 'last_name' => ['present',  'string'],
            // 'phone' => ['sometimes', new PhoneNumber],
            // 'fax' => ['sometimes', new PhoneNumber],
            // 'sms' => ['sometimes', new PhoneNumber],
            // 'notes' => ['sometimes', 'string'],
            // 'customer_id' => ['required',  'integer'],
            // 'operator_id' => ['required',  'integer']
        ]);

        return $validator;
    }

    public function login(Request $request)
    {
        $apiResponse = array(
            'success' => false
        );

        $validator = $this->loginValidator($request->all());
        if ($validator->fails()) {
            $apiResponse['errors'] = $validator->messages();

            return response()->json($apiResponse, 422);
        }

        if (Auth::attempt(
            [
                'username' => $request->input('username'),
                'password' => $request->input('password')
            ])) {
            $user = Auth::user();
            $token = $user->createToken('token-name')->plainTextToken;
            $user->api_token = $token;
            $user->update();

            $apiResponse['success'] = true;
            $apiResponse['user'] = $user;

            return response()->json($apiResponse);
        } else {
            $response['error'] = 'invalid_login';

            return response()->json($response, 401);
        }
    }

    public function show($id)
    {
        $loggedUser = Auth::user();
        if ($loggedUser->id == $id || $loggedUser->hasPermissionTo('access_um')) {
            return new UserResource(User::findOrFail($id));
        } else {
            $apiData = array(
                'success' => false,
                'message' => 'forbidden_no_permission'
            );
            return response()->json($apiData, 403);
        }
    }

    public function getAll(Request $request, UserFilters $filters)
    {
        return new UserCollectionResource(User::filter($filters)
            ->paginate($request->pagination["per_page"], ['*'], 'page',
                $request->pagination["current_page"]));
    }

    public function store(Request $request)
    {
        $apiResponse = array(
            'success' => false
        );

        $validator = $this->userValidator($request->all());
        if ($validator->fails()) {
            $apiResponse['errors'] = $validator->messages();
            $apiResponse['detailed_errors'] = $validator->failed();
            return response()->json($apiResponse, 422);
        }

        $user = null;
        $userDetails = null;
        if (isset($request->id)) {
            $user = User::findOrFail($request->id);

            // Before updating user, check if we are changing the username or the email
            // if so, then chack if the new username or the new email are not alredy taken
            // Throw exception if already used by other user
            if ($user->username != $request->username) $this->checkUsernameUnique($request->username);
            // Update - we are no longer checking if the email is unique
            // if(isset($request->email) && $request->email != '' && $user->email != $request->email) $this->checkEmailUnique($request->email);
            // if(isset($request->email) && $user->email != $request->email) $this->checkEmailUnique($request->email);

            // We already have the user and user details - update those entries
            $userDetails = $user->userDetails;
            // Make sure that we have a user details entry(imports done without adding the related user_details entry)
            if (!$userDetails) $userDetails = new UserDetails();
        } else {
            // Before we create new user, check if the username or email are not alredy taken
            // Throw exception if already used by other user
            $this->checkUsernameUnique($request->username);
            // Update - we are no longer checking if the email is unique
            // if(isset($request->email) && $request->email != '') $this->checkEmailUnique($request->email);
            // if(isset($request->email)) $this->checkEmailUnique($request->email);

            $user = new User();
            $user->api_token = Str::random(60);

            // We also need to create the user details entry for this new user
            $userDetails = new UserDetails();
        }

        $user->username = $request->username;
        if (isset($request->email)) $user->email = $request->email;
        if (isset($request->is_super_admin)) $user->is_super_admin = $request->is_super_admin;

        if (isset($request->password) && $request->password != '') {
            $user->password = \Hash::make($request->password);
        }

        // Give or remove super_user role
        if (isset($request->is_super_admin)) {
            $user->is_super_admin = $request->is_super_admin;
            if ($user->is_super_admin == true) {
                $user->assignRole('super_user');
            } else {
                $user->removeRole('super_user');
            }
        }

        // Finished the user model create/update - persist the data into the db
        $user->save();

        // Now persist the User details into the db
        if (isset($request->first_name)) $userDetails->first_name = $request->first_name;
        if (isset($request->last_name)) $userDetails->last_name = $request->last_name;
        if (isset($request->email)) $userDetails->email = $request->email;
        if (isset($request->phone)) $userDetails->phone = $request->phone;
        if (isset($request->fax)) $userDetails->fax = $request->fax;
        if (isset($request->sms)) $userDetails->sms = $request->sms;
        if (isset($request->notes)) $userDetails->notes = $request->notes;
        if (isset($request->customer_id) && Auth::user()->hasPermissionTo('read customers')) $userDetails->customer_id = $request->customer_id;
        if (isset($request->operator_id) && Auth::user()->hasPermissionTo('read operators')) $userDetails->operator_id = $request->operator_id;
        if (isset($request->customer_operator_filter) && Auth::user()->hasAllPermissions(['read customers', 'read operators'])) $userDetails->customer_operator_filter = $request->customer_operator_filter;

        $user->userDetails()->save($userDetails);

        // Assign permissions to this user (even if he is super_admin?)
        if (isset($request->permissions) && $request->permissions != '') {
            foreach ($request->permissions as $entity => $permissions) {
                // Handle the non standard permissions
                if ($entity == 'access_um' || $entity == 'access_internal_notes' || $entity == 'access_prices_offer' || $entity == 'access_time_tracking' || $entity == 'access_manual_invoice') {
                    if ($permissions) {
                        $user->givePermissionTo($entity);
                    } else {
                        $user->revokePermissionTo($entity);
                    }
                } else { // Handle the standard permissions of "action subject" form
                    foreach ($permissions as $permission => $value) {
                        if ($value) {
                            $user->givePermissionTo($permission . " " . $entity);
                        } else {
                            $user->revokePermissionTo($permission . " " . $entity);
                        }
                    }
                }
            }

            // Add the permissions to the user model to return them to the UI
            $user->api_permissions = $user->formatPermissions();
        }

        return new UserResource($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->userDetails()->delete();

        $user->delete();
        return new UserResource($user);
    }

    private function checkUsernameUnique($username)
    {
        $user = User::where('username', $username)->first();

        if ($user) throw new \Exception;
        return true;
    }
}
