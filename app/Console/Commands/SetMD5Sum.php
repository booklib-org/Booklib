<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetMD5Sum extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:set-m-d5-sum';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        //For each File where md5sum is null, set the md5sum
        $files = \App\Models\File::where('md5sum', null)->get();

        echo "Found " . $files->count() . " files with a null md5sum\n";
        $processed = 0;
        foreach($files as $file){
            if(file_exists($file->directory->directory . "/" . $file->filename) == false){
                continue;
            }
            $file->md5sum = md5_file($file->directory->directory . "/" . $file->filename);
            $file->save();
            $processed++;
            echo "Processed " . $processed . " / " . $files->count() . " files\n";
        }

    }
}
