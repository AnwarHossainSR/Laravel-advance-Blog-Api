<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:8',
        ]);
        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }

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

    public function registration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'about' => 'required',
        ]);

        if ($validator->fails())
        {
            return response(['errors'=>$validator->errors()->all()], 422);
        }
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
                'message' => 'Something is wrong',
            ], status: 400);
        }
    }
}
