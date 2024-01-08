<?php

namespace App\Jobs;

use App\Models\Directory;
use App\Models\Thumbnail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DirectoryThumbprint implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach(Directory::all() as $directory){
            echo $directory->directory . "\n";
            if(!isset($directory->thumbnail)){

                foreach($directory->files as $file){
                    if(isset($file->thumbnail)){
                        $thumbnail = Thumbnail::findOrFail($file->thumbnail->id);

                        $thumbnail->dir_id = $directory->id;
                        $thumbnail->save();
                        break;

                    }
                }

            }

        }
    }
}
