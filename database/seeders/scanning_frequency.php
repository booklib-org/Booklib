<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class scanning_frequency extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!Setting::where("setting", "=", "scanning_frequency")->exists()){
            Setting::create([
                "setting" => "scanning_frequency",
                "value" => "Every 12 Hours"
            ]);
        }
    }
}
