<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class create_admin_user extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(User::count() == 0){

            $user = new User();

            $user->username = "admin";
            $user->email = "";
            $user->password = Hash::make("password");
            $user->role = "Administrator";

            $user->save();

        }
    }
}
