<?php

namespace App\Http\Controllers;

use App\Models\Directory;
use App\Models\File;
use App\Models\Meta;
use App\Models\MetaType;
use App\Models\MetaValue;
use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SearchController extends Controller
{
    public function show(){

        Session::remove('type');
        Session::remove('searchString');

        return view("search")->with([
           "fields" => MetaType::where("type", "!=", "title")->where("type", "!=", "creator")->orderBy("type")->get()
        ]);

    }

    public function search(Request $request){

        if( $request->method() == "POST"){
            $type = $request->input("field");
            Session::put("type", $type);
            $searchString = $request->input("search");
            Session::put("search", $searchString);
        }elseif($request->method() == "GET"){
            $type = Session::get("type");
            $searchString = Session::get("search");
        }


        $itemsPerPage = UserSetting::where("user_id", "=", Auth::user()->id)->where("setting", "=", "items_per_page")->first()->value ?? 20;

        if($type == "Filename"){

            $results = File::where("filename", "LIKE", "%$searchString%")->orderBy("filename")->paginate($itemsPerPage, ['*'], "fpage")->appends(request()->query());
            return view("search.filename")->with([
                "results" => $results
            ]);

        }elseif($type == "Directory"){

            $results = Directory::where("directory_name", "LIKE", "%$searchString%")->orderBy("directory_name")->paginate($itemsPerPage, ['*'], "fpage")->appends(request()->query());
            return view("search.directory")->with([
                "results" => $results
            ]);

        }elseif($type == "Title"){

            $metatype_id = MetaType::where("type", "=", "title")->first()->id;


            $results = MetaValue::where("metadata_type", "=", $metatype_id)->where("value", "LIKE", "%$searchString%")->paginate($itemsPerPage, ['*'], "fpage")->appends(request()->query());
            return view("search.metatype")->with([
                "results" => $results
            ]);

        }elseif($type == "Author"){

            $metatype_id = MetaType::where("type", "=", "creator")->first()->id;


            $results = MetaValue::where("metadata_type", "=", $metatype_id)->where("value", "LIKE", "%$searchString%")->paginate($itemsPerPage, ['*'], "fpage")->appends(request()->query());
            return view("search.metatype")->with([
                "results" => $results
            ]);

        }else{

            $metatype_id = MetaType::where("type", "=", $type)->first()->id;


            $results = MetaValue::where("metadata_type", "=", $metatype_id)->where("value", "LIKE", "%$searchString%")->paginate($itemsPerPage, ['*'], "fpage")->appends(request()->query());
            return view("search.metatype")->with([
                "results" => $results
            ]);
        }

    }
}
