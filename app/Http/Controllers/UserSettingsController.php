<?php

namespace App\Http\Controllers;

use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserSettingsController extends Controller
{
    public function show(){

        return view("settings")->with([
           "settings" => UserSetting::where("user_id", "=", Auth::user()->id)->get()->keyBy('setting')
        ]);

    }

    public function store(Request $request){

        $settings = [
            "items_per_page",
            "thumbnail_size",
            "default_view",
            "show_counters"
        ];


        foreach($request->input() as $key => $value){

            if(in_array($key, $settings)){
                if(UserSetting::where("user_id", "=", Auth::user()->id)->where("setting", "=", $key)->exists()){

                    $x = UserSetting::where("user_id", "=", Auth::user()->id)->where("setting", "=", $key)->first();

                    $x->setting = $key;
                    $x->value = $value;
                    $x->save();

                    unset($x);

                }else{

                    $x = new UserSetting();

                    $x->setting = $key;
                    $x->value = $value;
                    $x->user_id = Auth::user()->id;

                    $x->save();

                    unset($x);

                }
            }


        }

        return view("settings")->with([
            "settings" => UserSetting::where("user_id", "=", Auth::user()->id)->get()->keyBy('setting')
        ]);

    }
}
