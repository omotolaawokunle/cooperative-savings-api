<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Requests\RegisterAuthRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UserController extends Controller
{
    public function register(RegisterAuthRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email'    => $request->email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'password' => $request->password,
        ]);
        return $this->login($request);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $token = null;
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['success' => false, 'message' => 'Invalid Username or Password'], 401);
        }

        return response()->json(['success' => true, 'token' => $token]);
    }

    public function logout(Request $request)
    {
        $this->validate($request, ['token' => 'required']);
        try {
            JWTAuth::invalidate($request->token);
            return response()->json(['success' => true, 'message' => 'User logged out succeessfully']);
        } catch (JWTException $e) {
            return response()->json(['success' => false, 'message' => 'User could not be logged out'], 500);
        }
    }
    public function getAuthUser(Request $request)
    {
        $this->validate($request, ['token' => 'required']);
        $user = JWTAuth::authenticate($request->token);
        return response()->json(['user' => $user]);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => auth()->factory()->getTTL() * 60
        ]);
    }
}
