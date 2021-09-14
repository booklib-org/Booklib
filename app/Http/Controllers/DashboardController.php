<?php

namespace App\Http\Controllers;

use App\Models\Library;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function main(){

        return view("dashboard")->with([

            "libraries" => Library::orderBy("name")->get()

        ]);

    }
}
