<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\Common\Entities\Status;
use Modules\Common\Http\Controllers\ApiController;

class AuthController extends ApiController
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
            'passwordConfirm' => 'required|same:password',
            'address' => 'required|string',
            'cellphone' => 'required',
            'postal_code' => 'required',
            'province_id' => 'required',
            'city_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), 422);
        }
        $user = UserRepo::store($request->all());
        $token = $user->createToken('myApp')->plainTextToken;

        return $this->successResponse([
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->messages(), Status::STATUS_UNPROCESSABLE_ENTITY);
        }

        $user = UserRepo::findByEmail($request->email);

        if(!$user){
            return $this->errorResponse('User not found!', Status::STATUS_UNAUTHORIZED);
        }

        if(!Hash::check($request->password, $user->password)){
            return $this->errorResponse('Password is incorrect!', Status::STATUS_UNAUTHORIZED);
        }

        $token = $user->createToken('myApp')->plainTextToken;

        return $this->successResponse([
            'user' => $user,
            'token' => $token
        ], 200);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();
        return $this->successResponse(null, Status::STATUS_OK, 'Logged out successfully.');
    }
}
