<?php

namespace App\Http\Controllers;

use App\Mail\ForgotPassword;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }
    public function register()
    {
        if(session()->has('loggedInUser')) {
            return redirect('/profile');
        } else {
            return view('auth.register');
        }
    }
    public function forgotPass()
    {
        if(session()->has('loggedInUser')) {
            return redirect('/profile');
        } else {
            return view('auth.forgot');
        }
    }
    public function resetPass(Request $request)
    {
        $email = $request->email;
        $token = $request->token;
        return view('auth.reset', ['email' => $email, 'token' => $token]);
    }

    // Handle registerUser ajax request
    public function registerUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:100',
            'password' => 'required|min:6|max:50',
            'cpassword' => 'required|same:password'
        ],[
            'cpassword.same' => 'Password did not match!',
            'cpassword.required' => 'Confirm password is required!'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'messages' => $validator->getMessageBag(),
            ]);
        }else {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                'status' => 200,
                'messages' => 'Registered Successfully!',
            ]);

        }
    }
    // Handle loginUser ajax request
    public function loginUser(Request $request)
    {
        // print_r($_POST);
        $validator = Validator::make($request->all(),[
            'email' => 'required|email|max:100',
            'password' => 'required|min:6|max:50',
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => 400,
                'messages' => $validator->getMessageBag(),
            ]);
        } else {
            $user = User::where('email', $request->email)->first();
            if($user){
                if(Hash::check($request->password, $user->password)) {
                   $request->session()->put('loggedInUser', $user->id);
                   return response()->json([
                        'status' => 200,
                        'messages' => 'success',
                   ]); 
                } else {
                    return response()->json([
                       'status' => 401,
                       'messages' => 'E-mail or Password is incorrect!',
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 401,
                    'messages' => 'User not found!',
                ]);
            }
        }
    }
    // profile page
    public function profile()
    {
        $data = ['userInfo' => DB::table('users')->where('id', session('loggedInUser'))->first()];

        return view('profile', $data);
    }

    // logout
    public function logout()
    {
        if(session()->has('loggedInUser')){
            session()->pull('loggedInUser');
            
            return redirect('/');
            
        }
    }

    // handle update user profile image ajax request
    public function profileImageUpdate(Request $request)
    {
        // print_r($_POST);
        // print_r($_FILES);
        $user_id = $request->user_id;
        $user = User::find($user_id);
        

        if($request->hasFile('picture')){

            $file = $request->file('picture');
            $fileName = time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('public/images/', $fileName);
            if($user->avatar){
                Storage::delete('public/images/'.$user->avatar);
            }


        }
        User::where('id', $user->id)->update([
            'avatar' => $fileName,
        ]);

        return response()->json([
            'status' => 200,
            'messages' => 'Profile image updated successfully!'
        ]);
    }

    // handle profile update ajax request
    public function profileUpdate(Request $request)
    {
        // print_r($_POST);
        User::where('id', $request->id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'phone' => $request->phone,
        ]);

        return response()->json([
            'status' => 200,
            'messages' => 'Profile updated successfully!',
        ]);
    }

    // handle forgot password ajax request
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:100',
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => 400,
                'messages' => $validator->getMessageBag(),
            ]);
        } else {
            $token = Str::uuid();
            $user = DB::table('users')->where('email', $request->email)->first();
            $details = [
                'body' => route('reset', ['email' => $request->email, 'token' => $token])
            ];

            if($user){
                User::where('email', $request->email)->update([
                    'token' => $token,
                    'token_expire' => Carbon::now()->addMinutes(10)->toDateTimeString(),
                ]);

                Mail::to($request->email)->send(new ForgotPassword($details));
                return response()->json([
                    'status' => 200,
                    'messages' => 'Reset password link has been sent to your E-mail!',
                ]);
            }
            else{
                return response()->json([
                    'status' => 401,
                    'messages' => 'This is not a registered E-mail!',
                ]);
            }
        }
    }

    //handle reset password ajax request
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'npass' => 'required|min:6|max:50',
            'cnpass' => 'required|min:6|max:50|same:npass'
        ], [
            'cnpass.same' => 'Password did not match'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'messages' => $validator->getMessageBag(),
            ]);
        } else {
            $user = DB::table('users')->where('email', $request->email)
            ->whereNotNull('token')
            ->where('token', $request->token)
            ->where('token_expire', '>', Carbon::now())
            ->exists();

            if($user){
                User::where('email', $request->email)->update([
                    'password' => Hash::make($request->npass),
                    'token' => null,
                    'token_expire' => null
                ]);

                return response()->json([
                    'status' => 200,
                    'messages' => 'New password updated!&nbsp;&nbsp;<a href="/">Login Now</a>'
                ]);
            } else {
                return response()->json([
                    'status' => 401,
                    'messages' => 'Reset link expired! Request for a new reset password link'
                ]);
            }
        }
    } 
}
