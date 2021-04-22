<?php

namespace App\Http\Controllers;

use App\Http\Requests\ForgotPassword;
use App\Http\Requests\ResetRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Exception;
use Illuminate\Support\Facades\DB;

class ForgotController extends Controller
{
    public function forgotPassword(Request $request)
    {
        if (User::where('email','=',$request->email)->doesntExist()) {
            return \response([
                'message' => 'User does not exists'
            ], status: 404);
        }

        $token = Str::random(10);
        try {
            DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => $token
            ]);
            //Send email

            $data = [
                'subject' => 'Password reset',
                'email' => $request->email,
                'token' => $token
              ];
            Mail::send('emailTemplate', $data, function($message) use ($data) {
                $message->to($data['email'])
                ->subject($data['subject']);
            });

            return \response([
                'message' => 'Check your email'
            ]);
        } catch (Exception $th) {
            return \response([
                'message' => $th->getMessage()
            ]);
        }
    }

    public function resetReq(ResetRequest $req)
    {
        if (!$passwordReset = DB::table('password_resets')->where(
            'token','=',$req->token)->first()) {
            return \response([
                'message'=>'Invalid token'
            ],status:400);
        }

        /* @var User $user */
        if (!$user = User::where('email','=',$passwordReset->email)->first()) {
            return \response([
                'message' => 'User does not exists'
            ], status: 404);
        }

        $user->password = Hash::make($req->password);
        $user->save();
        return \response([
            'message'=>'success'
        ]);
    }
}
