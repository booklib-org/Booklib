<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Jobs\RemoveLibraryFolder;
use App\Jobs\RescanLibrary;
use App\Models\Directory;
use App\Models\Library;
use App\Models\LibraryFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Input\Input;

class LibraryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $r)
    {
        if(key_exists("search", $r->input())){

            return view("manage.libraries.index")->with([
                "libraries" => Library::where("name", "LIKE", "%" . $r->input("search") . "%")->orderBy("name")->paginate(20)
            ]);

        }else{
            return view("manage.libraries.index")->with([
                "libraries" => Library::orderBy("name")->paginate(20)
            ]);
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("manage.libraries.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

            $x = new Library();

            $x->name = $request->input("name");
            $x->type = $request->input("type");
            $x->total_files = 0;

            $x->save();

            foreach($request->input("folder") as $directory){

                $d = new LibraryFolder();
                $d->path = $directory;
                $d->library_id = $x->id;
                $d->total_files = 0;
                $d->save();

            }
            RescanLibrary::dispatch();
            return redirect("/manage/libraries")->with(["success" => true, "message" => "The library was added. Indexing will begin shortly."]);



    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view("manage.libraries.show")->with([
            "library" => Library::findOrFail($id)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return view("manage.libraries.edit")->with([
            "library" => Library::findOrFail($id)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $x = Library::findOrFail($id);

        $x->name = $request->input("name");
        $x->type = $request->input("type");

        foreach($x->folders as $folder){
            if(!in_array($folder->path, $request->folder)){
                RemoveLibraryFolder::dispatch($folder->id);
            }
        }

        foreach($request->input("folder") as $directory){

            if(!LibraryFolder::where("library_id", "=", $id)->where("path", "=", $directory)->exists()){

                $d = new LibraryFolder();
                $d->path = $directory;
                $d->library_id = $x->id;
                $d->total_files = 0;
                $d->save();

            }

        }

        $filecount = 0;
        foreach($x->folders as $dir){
            $filecount = $filecount + $dir->total_files;
        }
        $x->total_files = $filecount;

        $x->save();
        RescanLibrary::dispatch();
        return redirect("/manage/libraries")->with(["success" => true, "message" => "The library was updated. A re-scan will be done shortly."]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $x = Library::findOrFail($id);

        foreach($x->folders as $folder) {
            RemoveLibraryFolder::dispatch($folder->id);
        }

        $x->delete();

        return redirect()->back()->with(["success" => true, "message" => "The library will be removed shortly."]);
    }
}
