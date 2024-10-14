<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request){
        
        
        $validateUser = validator::make(
            $request->all(),
            [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:100|unique:users,email',
                'phone' => 'required|string|regex:/^\+?[0-9\s\-()]{7,}$/|unique:users,phone',
                'password' => 'required|string|min:8',
            ]
            );

            if($validateUser->fails()){
                return response()->json([
                    'status'=>false,
                    'message' => 'Validation failed',
                    'errors'=>$validateUser->errors()->all()
                ], 401);
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'status'=>true,
                'message' => 'user created successfully',
                'user'=>$user,
            ], 200);


    }

    public function login(Request $request){
        $validateUser = validator::make(
            $request->all(),
            [
                'email' => 'required|email',
                'password' => 'required',
            ]
            );
            if($validateUser->fails()){
                return response()->json([
                    'status'=>false,
                    'message' => 'Authentication failed',
                    'errors'=>$validateUser->errors()->all()
                ], 404);
            }

            if(Auth::attempt(['email'=> $request->email, 'password' => $request->password])){
                $authUser = Auth::user();
                return response()->json([
                    'status'=>true,
                    'message' => 'user Loged In successfully',
                    'token'=>$authUser->createToken("API Token")->plainTextToken,
                    'token_type'=> 'bearer'
                ], 200);
            }else{
                return response()->json([
                    'status'=>false,
                    'message' => 'Email or password does not exists!',
                ], 401);
            }
    }

    public function logout(Request $request){
        $user = $request->user();
        $user->tokens()->delete();
        return response()->json([
            'status'=>true,
            'message' => 'You Loged Out successfully',
        ], 200);
    }


    

    public function update(Request $request, string $id){
          
        $validateUser = validator::make(
            $request->all(),
            [
                'profile' => 'required|mimes:png,jpg,jpeg,gif',
            ]
            );

            if($validateUser->fails()){
                return response()->json([
                    'status'=>false,
                    'message' => 'Validation failed',
                    'errors'=>$validateUser->errors()->all()
                ], 401);
            }


            $userImage = User::select('id' , 'profile')
            ->where(['id'=> $id])->get();
            if($request->profile != '' ){
                $path = public_path(). '/uploads/main_user_profile';
                if($userImage[0]->profile != '' && $userImage[0]->profile != null){
                    $old_file = $path. $userImage[0]->profile;
                    if(file_exists($old_file)){
                        unlink($old_file);
                    }  
                }
                $img = $request->profile;
                $ext = $img->getClientOriginalExtension();
                $userImageName = 'userProfile_'.time().'.'.$ext;
                $img->move(public_path(). '/uploads/main_user_profile', $userImageName);
            }else{
                $userImageName = $userImage->profile;
            }

            $user = User::where(['id' => $id])->update([
                'profile' => $userImageName,
            ]);

            return response()->json([
                'status'=>true,
                'message' => 'user image updated successfully',
                'user'=>$user,
            ], 200);
    }


    public function change_password(Request $request){
        $validateUser = validator::make(
            $request->all(),
            [
                'current_password' => 'required',
                'new_password' => 'required|min:8|different:current_password',
            ]
            );

            if($validateUser->fails()){
                return response()->json([
                    'status'=>false,
                    'message' => 'Validation failed',
                    'errors'=>$validateUser->errors()->all()
                ], 401);
            }

            $user = Auth::user();
            if (Hash::check($request->current_password, $user->password)) {
                $user->password = Hash::make($request->new_password);
                $user->save();
                return response()->json([
                   'status'=>true,
                   'message' => 'Password changed successfully',
                ], 200);
            } else {
                return response()->json([
                   'status'=>false,
                   'message' => 'User Authentication Failed',
                ]);
            
            }

        }


        public function get_logged_user_details(Request $request){
            $user = Auth::user();
            return response()->json([
               'status'=>true,
               'message' => 'User details fetched successfully',
                'user'=>$user,
            ], 200);
        }

    }

