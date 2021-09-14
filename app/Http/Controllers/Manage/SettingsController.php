<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function Show(){
        return view('manage.settings')->with([
            "thumbnail_quality" => Setting::where("setting", "=", "thumbnail_quality")->first(),
            "scanning_frequency" => Setting::where("setting", "=", "scanning_frequency")->first(),
        ]);
    }

    public function store(Request $request){

        $x = Setting::where("setting", "=", "thumbnail_quality")->first();
        $x->value = $request->thumbnail_quality;
        $x->save();

        $x = Setting::where("setting", "=", "scanning_frequency")->first();
        $x->value = $request->scanning_frequency;
        $x->save();

        return redirect("/manage/settings")->with(["success" => true, "message" => "Settings have been saved."]);

    }

}
