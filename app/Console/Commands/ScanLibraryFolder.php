<?php

namespace App\Console\Commands;

use App\Models\Directory;
use App\Models\File;
use App\Models\Library;
use App\Models\LibraryFolder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ScanLibraryFolder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Scan:Library';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */

    function getDirContents($dir, $libraryFolderId, $parentDirectory, $libraryId) {
        $files = scandir($dir);

        $parentdir = Directory::firstOrCreate([
            "directory" => $dir,
            "library_folder_id" => $libraryFolderId,
            "parent_directory_id" => $parentDirectory,
            "directory_name" => basename($dir)
        ]);

echo $parentdir->directory . PHP_EOL;
        foreach ($files as $key => $value) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
            if (!is_dir($path)) {


                if(in_array(strtoupper(pathinfo($path, PATHINFO_EXTENSION)), ["CBR", "CBZ", "PDF", "MOBI", "EPUB"])){
                    File::firstOrCreate([
                        "filename" => basename($path),
                        "filesize" => filesize($path),
                        "directory_id" => $parentdir->id,
                        "library_id" => $libraryId
                    ]);

                }

            } else if ($value != "." && $value != "..") {
                $this->getDirContents($path, $libraryFolderId, $parentdir->id, $libraryId);

            }
        }




    }

    public function handle()
    {

        echo "Running Scan Library\n";
        foreach(LibraryFolder::orderBy("updated_at","DESC" )->get() as $folder) {
            try {
                $files = $this->getDirContents($folder->path, $folder->id, 0, $folder->library_id);

            }catch (\ErrorException $e){
                print_r($e->getMessage());
                unset($e);

            }

        }

        foreach(Library::all() as $library){
            $library->touch();
        }
        Artisan::call("Generate:Thumbnails");
        Artisan::call("Set:MetaData");
    }

    private function CalculateDirectoryFiles(Directory $directory)
    {

        $counter = count($directory->files);

        if(Directory::where("parent_directory_id", "=", $directory->id)->exists()){

            foreach(Directory::where("parent_directory_id", "=", $directory->id)->get() as $subdirectory){

                $counter = $counter + $this->CalculateDirectoryFiles($subdirectory);

            }

        }

        return $counter;




    }

    private function ProcessFiles($directory, $directory_id, $library_id){

        echo $directory . "\n";
        $files = scandir($directory);

        $existingFiles = File::where("directory_id", "=", $directory_id)->get(["filename"])->toArray();

        foreach($files as $file){

            if(is_file($directory . "/" . $file)){
                $exists = false;
                foreach($existingFiles as $ef){

                    if($ef["filename"] == $file){
                        $exists = true;
                    }

                }

                if($exists == false){

                    if(in_array(strtoupper(pathinfo($file, PATHINFO_EXTENSION)), ["CBR", "CBZ", "PDF", "MOBI", "EPUB"])){
                        $f = new File();
                        $f->filename = $file;
                        $f->filesize = filesize($directory . "/" . $file);
                        $f->directory_id = $directory_id;
                        $f->library_id = $library_id;
                        $f->save();

                    }

                }


            }

        }


        //Now remove files no longer existing in the drive
        $existingFiles = File::where("directory_id", "=", $directory_id)->get();

        foreach($existingFiles as $f){

            if(!file_exists($f->directory->directory . "/" . $f->filename)){

                $f->delete();

            }

        }

    }

    private function ProcessDir($folder, $library_folder_id, $parent_directory_id){

        echo $folder . "\n";

        //Process base directories
        $directories = new \RecursiveDirectoryIterator($folder, \RecursiveDirectoryIterator::SKIP_DOTS);
        foreach($directories as $directory) {

            if($directory->isDir()){
                if(!Directory::where("library_folder_id", "=", $library_folder_id)->where("directory", "=", $directory->getPathname())->where("parent_directory_id", "=", $parent_directory_id)->exists()){

                    $x = new Directory();

                    $x->library_folder_id = $library_folder_id;
                    $x->directory = $directory->getPathname();
                    $x->directory_name = $directory->getBasename();
                    $x->parent_directory_id = $parent_directory_id;
                    $x->total_files = 0;

                    $x->save();

                }
            }
        }

        foreach(Directory::where("library_folder_id", "=", $library_folder_id)->where("parent_directory_id", "=", $parent_directory_id)->get() as $item){

            $this->ProcessDir($item->directory, $item->library_folder_id, $item->id);

        }
    }


}
