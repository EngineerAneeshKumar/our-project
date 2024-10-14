<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Mail\Message;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class PasswordResetController extends Controller
{
    public function send_reset_email(Request $request){
      $validateUser = validator::make(
        $request->all(),
        [
            'email' => 'required|email',
        ]
        );
        if($validateUser->fails()){
            return response()->json([
                'status'=>false,
                'message' => 'Request failed',
                'errors'=>$validateUser->errors()->all()
            ], 404);
        }

      $user = User::where('email', $request->email)->first();

      if(!$user){
        return response([
          'message' => 'Email does not exist',
          'error' => 'failed',
        ]);
      }

        $token = Str::random(60);

        PasswordReset::create([
          'email' => $request->email,
          'token' => $token,
          'created_at' => Carbon::now()
        ]);
      
          Mail::send('reset', ['token' => $token], function (Message $message) use ($user) {
            $message->to($user->email)->subject('Reset Password');
          });


        return response([
          'message' => 'Password Reset Email sent... Check your Email',
          'status' => 'success',
        ], 200);
    }

    public function reset_password(Request $request, $token){

      $formatted = Carbon::now()->subMinute(15)->toDateTimeString();
      PasswordReset::where('created_at','<=', $formatted)->delete();


      $request->validate(([
        'password' => 'required|min:8|confirmed'
      ]));
      $passwordreset = PasswordReset::where('token', $token)->first();
      if(!$passwordreset){
        return response([
         'message' => 'Password reset token expired or invalid',
          'error' => 'failed',
        ], 404);
      }

      $user = User::where('email', $passwordreset->email)->first();
      $user->password = Hash::make($passwordreset->password);
      $user->save();
      PasswordReset::where('token', $token)->delete();
      return response([
       'message' => 'Password reset successful',
       'status' =>'success',
      ], 200);

    }

}
