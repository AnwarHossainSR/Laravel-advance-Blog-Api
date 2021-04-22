<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            try {
                /* @var User user */
                $user = Auth::user();
                $token = $user->createToken('app')->accessToken;

                return \response([
                    'message' => 'success',
                    'token' => $token,
                    'user' => $user
                ]);
            } catch (\Exception $th) {
                return \response(['message' => $th->getMessage()], status: 400);
            }
        } else {
            return \response(['message' => 'Invalid username or password'], status: 401);
        }
    }

    public function authenticatedUser()
    {
        return  Auth::user();
    }

    public function registration(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'about' => $request->about,
            ]);
            return \response([
                'message' => 'success',
                'user'=>$user,
            ], status: 201);
        } catch (Exception $th) {
            return \response([
                'message' => 'error',
            ], status: 400);
        }
    }
}
