<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Models\ImportOPDS;
use App\Models\Library;
use App\Models\LibraryFolder;
use Illuminate\Http\Request;

class ImportOPDSController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("manage.opds.index")->with([
            "opds" => ImportOPDS::orderBy("url")->paginate(20)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("manage.opds.create")->with([
            "libraries" => Library::orderBy("name")->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $x = new ImportOPDS();

        $x->url = $request->input("url");
        $x->username = $request->input("username");
        $x->password = $request->input("password");
        $x->library_folder_id = $request->input("library_folder_id");
        $x->save();

        return redirect("/manage/importopds");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ImportOPDS  $importOPDS
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $importOPDS = ImportOPDS::findOrFail($id);
        $importOPDS->delete();


        return redirect("/manage/importopds");
    }
}
