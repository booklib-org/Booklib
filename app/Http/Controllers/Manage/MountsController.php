<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use App\Jobs\RescanLibrary;
use App\Models\Mount;
use App\Models\User;
use Illuminate\Http\Request;

class MountsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view("manage.mounts.index")->with([
            "mounts" => Mount::orderBy("name")->paginate(20)
        ]);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("manage.mounts.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $destination = base_path("mounts/" . trim($request->input("destination")));

        //Check if dir exists and is empty
        if(is_dir($destination)){
            if(count(glob($destination . "/*")) === 0){
                //Done
            }else{
                return redirect()->back()->withInput()->with(["error" => true, "message" => "Unable to use destination directory. Reason: Directory not empty"]);
            }
        }else{
            //Create Directory
            if(mkdir($destination, 0755, true)){
                //Done
            }else{
                //Directory Creation Failed
                return redirect()->back()->withInput()->with(["error" => true, "message" => "Unable to create destination directory. Reason: Unknown"]);
            }
        }

        if($request->input("type") == "Samba"){

            $output = shell_exec("sudo mount -t cifs \"" . trim($request->input("source")) . "\" \"" . $destination . "\" -o username=" . $request->input("username") . ",password=" . $request->input("password") . " 2>&1");

            if(empty($output)){
                //Success. Add mount to /etc/rc.local and DB

                $x = new Mount();

                $x->name = $request->input("name");
                $x->type = $request->input("type");
                $x->mount_from = $request->input("source");
                $x->mount_to = $destination;
                $x->username = $request->input("username");
                $x->password = $request->input("password");

                $x->save();

            }else{
                return redirect()->back()->withInput()->with(["error" => true, "message" => "Unable to mount. Reason: " . $output]);
            }


        }

        if($request->input("type") == "NFS"){

            $output = shell_exec("sudo mount -t nfs \"" . trim($request->input("source")) . "\" \"" . $destination . "\" 2>&1");

            if(empty($output)){
                //Success. Add mount to /etc/rc.local and DB
                shell_exec("sudo chmod 777 \"" . $destination . "\"");
                $x = new Mount();

                $x->name = trim($request->input("name"));
                $x->type = $request->input("type");
                $x->mount_from = trim($request->input("source"));
                $x->mount_to = trim($destination);
                $x->username = trim($request->input("username"));
                $x->password = trim($request->input("password"));

                $x->save();

            }else{
                return redirect()->back()->withInput()->with(["error" => true, "message" => "Unable to mount. Reason: " . $output]);
            }


        }

        return redirect("/manage/mounts")->with(["success" => true, "message" => "The mount was added."]);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Mount  $mount
     * @return \Illuminate\Http\Response
     */
    public function destroy(Mount $mount)
    {

        $mountpoint = $mount->mount_to;

        $mount->delete();

        shell_exec("sudo umount \"" . $mountpoint . "\"");
        RescanLibrary::dispatch();

        return redirect("/manage/mounts")->with(["success" => true, "message" => "The mount was removed. Any remaining items will be removed shortly."]);

    }
}
