<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class setting_thumbnail extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(!Setting::where("setting", "=", "thumbnail_quality")->exists()){
            Setting::create([
                "setting" => "thumbnail_quality",
                "value" => "Low"
            ]);
        }
    }
}
