<?php

namespace App\Http\Controllers;

use App\Models\Directory;
use App\Models\File;
use App\Models\Library;
use App\Models\LibraryFolder;
use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

class ShowLibraryController extends Controller
{

    public function Show(Request $request, $id){

        $itemsPerPage = UserSetting::where("user_id", "=", Auth::user()->id)->where("setting", "=", "items_per_page")->first()->value ?? 20;
        $thumbnailSize = UserSetting::where("user_id", "=", Auth::user()->id)->where("setting", "=", "thumbnail_size")->first()->value ?? 100;

        $view = UserSetting::where("user_id", "=", Auth::user()->id)->where("setting", "=", "default_view")->first()->value ?? "Table View";

        $showCounters =  UserSetting::where("user_id", "=", Auth::user()->id)->where("setting", "=", "show_counters")->first()->value ?? false;

        if($view == "Table View"){
            $useView = "library.comics.show_table";
        }else{
            $useView = "library.comics.show_grid";
        }
        $library = \App\Models\Library::findOrFail($id);

        return view($useView)->with([
            "library" => $library,
            "directories" => Directory::where("parent_directory_id", "=", 0)->whereIn("library_folder_id", $library->folderIDs())->where("directory_name", "!=", ".")->where("directory", "LIKE", "%" . $request->input("searchDir") . "%")->orderBy("directory_name")->paginate($itemsPerPage)->appends(request()->query()),
            "files" => File::where("id", "=", 0)->get(),
            "thumbnailSize" => $thumbnailSize,
            "showCounters" => $showCounters
        ]);

    }

    public function ShowDir(Request $request, $id, $dir){

        $view = UserSetting::where("user_id", "=", Auth::user()->id)->where("setting", "=", "default_view")->first()->value ?? "Table View";
        $itemsPerPage = UserSetting::where("user_id", "=", Auth::user()->id)->where("setting", "=", "items_per_page")->first()->value ?? 20;
        $thumbnailSize = UserSetting::where("user_id", "=", Auth::user()->id)->where("setting", "=", "thumbnail_size")->first()->value ?? 100;
        $showCounters =  UserSetting::where("user_id", "=", Auth::user()->id)->where("setting", "=", "show_counters")->first()->value ?? false;

        $directory = Directory::where("id", "=", $dir)->first();

        if($view == "Table View"){
            $useView = "library.comics.show_table";
        }else{
            $useView = "library.comics.show_grid";
        }

        $library = \App\Models\Library::findOrFail($id);

        return view($useView)->with([

            "library" => $library,
            "directories" => Directory::where("parent_directory_id", "=", $dir)->where("directory_name", "!=", ".")->whereIn("library_folder_id", $library->folderIDs())->where("directory", "LIKE", "%" . $request->input("searchDir") . "%")->orderBy("directory_name")->paginate($itemsPerPage)->appends(request()->query()),

            "files" => File::where("directory_id", "=", $dir)->where("filename", "LIKE", "%" . $request->input("searchFile") . "%")->orderBy("filename")->paginate($itemsPerPage, ['*'], "fpage")->appends(request()->query()),
            "thumbnailSize" => $thumbnailSize,
            "showCounters" => $showCounters
        ]);

    }

    public function ShowComic(Request $request, $id, $dir, File $comic){

        $directory = Directory::where("id", "=", $dir)->first();

        return view("library.comics.comic")->with([
            "library" => \App\Models\Library::findOrFail($id),
            "comic" => $comic

        ]);

    }

    public function redirectSingleFile(File $comic){

        $directory = Directory::where("id", "=", $comic->directory_id)->first();
        $libraryFolder = LibraryFolder::findOrFail($directory->library_folder_id);
        $library = Library::findOrFail($libraryFolder->library_id);


        return redirect("/library/$library->id/$directory->id/$comic->id");

    }
    public function redirectDirectory(Directory $directory){


        $libraryFolder = LibraryFolder::findOrFail($directory->library_folder_id);
        $library = Library::findOrFail($libraryFolder->library_id);


        return redirect("/library/$library->id/$directory->id/");

    }


    public function DownloadComic(Request $request, Library $id, Directory $dir, File $comic){

           return response()->download(
            $dir->directory . "/" .
            $comic->filename);

    }

    public function DownloadSingleComic(File $comic){

        $dir = Directory::where("id", "=", $comic["directory_id"])->first();
        return response()->download(
            $dir->directory . "/" .
            $comic->filename);

    }

}
