<?php

namespace App\Http\Controllers\Manage;

use App\Exceptions\Handler;
use App\Http\Controllers\Controller;
use App\Jobs\RemoveLibraryFolder;
use App\Models\Library;
use App\Models\LibraryFolder;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $r)
    {
        if(key_exists("search", $r->input())){

            return view("manage.users.index")->with([
                "users" => User::where("username", "LIKE", "%" . $r->input("search") . "%")->orderBy("username")->paginate(20)
            ]);

        }else{
            return view("manage.users.index")->with([
                "users" => User::orderBy("username")->paginate(20)
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
        return view("manage.users.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            "username" => ["required", Rule::unique("users")]
        ]);

        $password = $this->randomPassword();
        $x = new User();

        $x->username = $request->input("username");
        $x->email = $request->input("email");
        $x->role = $request->input("role");
        $x->password = Hash::make($password);

        $x->save();

        return redirect("/manage/users")->with(["success" => true, "message" => "The user was added. The password was set to: " . $password]);

    }

    function randomPassword() {
        $pass = "";
        $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
        for ($i = 0; $i < 12; $i++) {
            $n = rand(0, strlen($alphabet)-1);
            $pass  = $pass . substr($alphabet, $n, 1);
        }
        return $pass;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view("manage.users.edit")->with([
        "user" => $user
    ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {

        $request->validate([
            "username" => ["required", Rule::unique("users")->ignore($user->id)]
        ]);

        $user->username = $request->input("username");
        $user->role = $request->input("role");

        $user->save();

        if($request->exists("resetpassword")){
            $password = $this->randomPassword();
            $user->password = Hash::make($password);
            $user->save();
            return redirect("/manage/users")->with(["success" => true, "message" => "The user has been updated. The password was set to: " . $password]);
        }else{
            return redirect("/manage/users")->with(["success" => true, "message" => "The user has been updated."]);
        }


    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        //

        $user->delete();
        return redirect("/manage/users");
    }
}
