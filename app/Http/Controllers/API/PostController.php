<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Models\Thoughts;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseController as BaseController;

class PostController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['thoughts'] = Thoughts::all();

        return $this->sendResponse($data, 'Thoughts retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $validateUser = validator::make(
            $request->all(),
            [
                'user_name' => 'required',
                'thoughts_content' => 'required',
                'user_profile' => 'required|mimes:png,jpg,jpeg,gif',
                'bg_img' => 'required|mimes:png,jpg,jpeg,gif',
            ]
            );

            if($validateUser->fails()){
                return response()->json([
                    'status'=>false,
                    'message' => 'Validation failed',
                    'errors'=>$validateUser->errors()->all()
                ], 401);
            }

            $img = $request->user_profile;
            $ext = $img->getClientOriginalExtension();
            $userImageName = 'user_'.time().'.'.$ext;
            $img->move(public_path(). '/uploads/user_profile', $userImageName);

            
            $img1 = $request->bg_img;
            $ext1 = $img1->getClientOriginalExtension();
            $bgImageName = 'thoughts_'.time().'.'.$ext1;
            $img1->move(public_path(). '/uploads/thoughts', $bgImageName);

            $thoughts = Thoughts::create([
                'user_name' => $request->user_name,
                'thoughts_content' => $request->thoughts_content,
                'user_profile' => $userImageName,
                'bg_img' => $bgImageName,
            ]);
            return $this->sendResponse($thoughts, 'thoughts created succesfully successfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data['post'] = Thoughts::select(
            'id',
            'user_name',
            'thoughts_content',
            'user_profile',
            'bg_img'
        )->where(['id'=> $id])->get();

        

        return $this->sendResponse($data, 'Your signle thoughts');


    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validateUser = validator::make(
            $request->all(),
            [
                'user_name' => 'required',
                'thoughts_content' => 'required',
                'user_profile' => 'required|mimes:png,jpg,jpeg,gif',
                'bg_img' => 'required|mimes:png,jpg,jpeg,gif',
            ]
            );

            if($validateUser->fails()){
                return response()->json([
                    'status'=>false,
                    'message' => 'Validation failed',
                    'errors'=>$validateUser->errors()->all()
                ], 401);
                
            }

            $thoughts = Thoughts::select('id', 'user_profile')
            ->where(['id'=>$id])->get();

            if($request->userProfileImage != ''){
                $path = public_path(). '/uploads/user_profile';
                if($thoughts->userProfileImage != '' && $thoughts->userProfileImage != null){
                    $old_file = $path. $thoughts->userProfileImage;
                    if(file_exists($old_file)){
                        unlink($old_file);
                    }
                }
                $img = $request->userProfileImage;
                $ext = $img->getClientOriginalExtension();
                $userImageName = 'user_'.time().'.'.$ext;
                $img->move(public_path(). '/uploads/user_profile', $userImageName);
            }else{
                $userImageName = $thoughts->userProfileImage;
            }
 
            $img1 = $request->thoughtImage;
            $ext1 = $img1->getClientOriginalExtension();
            $bgImageName = 'thoughts_'.time().'.'.$ext1;
            $img1->move(public_path(). '/uploads/thoughts', $bgImageName);

            $thoughts = Thoughts::where(['id' => $id])->update()([
                'user_name' => $request->user_name,
                'thoughts_content' => $request->thoughts_content,
                'user_profile' => $userImageName,
                'bg_img' => $bgImageName,
            ]);

            return $this->sendResponse($thoughts, 'thoughts updated successfully');
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {

        $imagePath = Thoughts::select('user_profile')->where('id', $id)->get();
        
    

        $filepath = public_path(). '/uploads/user_profile' .$imagePath[0]['user_profile'];

        if(file_exists($filepath)){
            unlink($filepath);
        }

        $thoughts = Thoughts::where(['id' => $id])->delete();
        if($thoughts){

            return $this->sendResponse($thoughts, 'thoughts deleted successfully');

        }else{
            return $this->sendError($thoughts, 'Failed to delete thoughts');
        }
    }
}
