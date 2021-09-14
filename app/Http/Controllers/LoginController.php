<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class LoginController extends Controller
{
    public function Show(){

        Auth::viaRemember();
        if(Auth::check()){
            return redirect("/");
        }
        return view("login.login");

    }

    public function Logout(){

        Auth::logout();
        return redirect("/login")->with(["success" => "You have been logged out."]);

    }

    public function Login(Request $r){

        $r->validate([
            "username" => "required",
            "password" => "required"
        ]);

        $credentials = $r->only('username', 'password');

        if (Auth::attempt($credentials)) {

            if($r->input("rememberMe")){
                Auth::attempt($credentials, true);
            }
            return redirect('/');
        }else{
            return redirect()->back()->withErrors(["error" => "The provided login details are incorrect."]);
        }

    }

    public function changePassword(){

        return view("login.changePassword");

    }

    public function doChangePassword(Request $request){

        $request->validate([
            "password" => "same:confirmPassword"
        ]);

        $user = User::findOrFail(\auth()->user()->id);

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect("/changePassword")->with(["success" => true, "message" => "The password has been changed."]);

    }
}
