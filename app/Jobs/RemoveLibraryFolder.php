<?php

namespace App\Jobs;

use App\Models\Directory;
use App\Models\File;
use App\Models\Library;
use App\Models\LibraryFolder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;

class RemoveLibraryFolder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($id)
    {
        $this->id = $id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $folder = LibraryFolder::findOrFail($this->id);

        foreach($folder->directories as $directory){

            foreach($directory->files as $file){
                try{
                    $file->delete();
                }catch (\ErrorException $e){
                    unset($e);
                }

            }
            $directory->delete();
        }
        $folder->delete();


        foreach(LibraryFolder::orderBy("updated_at")->get() as $folder){

            $fileCounterForFolder = 0;
            foreach(Directory::where("library_folder_id", "=", $folder->id)->get() as $item) {

                $item->total_files =  File::where("directory_id", "=", $item->id)->count();
                $item->save();
                $fileCounterForFolder = $fileCounterForFolder + $item->total_files;
            }


            $folder->total_files = $fileCounterForFolder;
            $folder->save();

        }

        foreach(Library::all() as $library){

            $fileCounterForLibrary = 0;
            foreach(LibraryFolder::where("library_id", "=", $library->id)->get() as $libdir){

                $fileCounterForLibrary = $fileCounterForLibrary + $libdir->total_files;

            }
            $library->total_files = $fileCounterForLibrary;
            $library->save();
        }
        Artisan::call("Scan:Library");
    }
}
