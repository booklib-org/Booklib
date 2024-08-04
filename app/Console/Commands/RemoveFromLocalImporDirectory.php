<?php

namespace App\Console\Commands;

use App\Handlers\AddFileClass;
use App\Handlers\SetMetaDataClass;
use App\Models\Directory;
use App\Models\MetaType;
use App\Models\MetaValue;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;

class RemoveFromLocalImporDirectory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remove-from-local-import-directory {--language=} {--summaryOnly=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Use this command to remove files from a the local import directory, if the language code is a match';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $summaryData = [];
        $summaryData["language"] = [];
        $summaryData["totalBooks"] = 0;

        echo "Getting a complete list of all files in the /import directory\n";

        $directory = '/import';

        $finder = new Finder();
        $finder->files()
            ->in($directory)
            ->name('/\.epub$/i')
            ->name('/\.mobi$/i');

        echo "Found " . $finder->count() . " files in the /import directory\n";
        echo "Starting removal process\n";

        $removeLanguage = explode(",", $this->option('language'));

        foreach ($finder as $file) {
            $metadata = new SetMetaDataClass();

            echo "Processing file: " . $file->getRealPath() . "\n";


            if(str_ends_with($file->getRealPath(), ".epub")) {

                if(!file_exists($file->getRealPath() . ".bmf")){
                    $meta = $metadata->getEpubFileMetaData($file->getRealPath());
                    file_put_contents($file->getRealPath() . ".bmf", json_encode($meta));
                }else{
                    $meta = json_decode(file_get_contents($file->getRealPath() . ".bmf"), true);
                }
                if(!is_array($meta)){
                    continue;
                }

                //Check if it has a "creator" key
                if(array_key_exists("language", $meta)) {

                    if(is_array($meta['language'])){
                        $meta['language'] = $meta['language'][0];
                    }
                    if(!array_key_exists($meta['language'], $summaryData["language"])) {
                        $summaryData["language"][$meta['language']] = 0;
                    }

                    $summaryData["language"][$meta['language']]++;
                    $summaryData["totalBooks"]++;

                    if($this->option('summaryOnly') == "true"){
                        continue;
                    }

                    if(!empty($this->option('language')) && in_array(strtolower($meta['language']),$removeLanguage)){
                        File::delete($file->getRealPath());
                        echo "Removed file.\n";
                    }





                } else {
                    //Skip for now
                    continue;
                }

            }

        }

        echo "Getting a complete list of all CBR and CBZ files in the /import directory\n";

        $directory = '/import';

        $finder = new Finder();
        $finder->files()
            ->in($directory)
            ->name('/\.cbr/i')
            ->name('/\.cbz/i');

        echo "Found " . $finder->count() . " files in the /import directory\n";
        $summaryData["CBR/CBZ Files Removed"] =0;
        foreach ($finder as $file) {
            $summaryData["totalBooks"]++;

            if($this->option('summaryOnly') == "true"){
                continue;
            }

            if(File::where('filename', '=', $file->getFilename())->exists()){

                $md5sum = md5_file($file->getRealPath());
                foreach(File::where('filename', '=', $file->getFilename())->get() as $dbFile){
                    if($dbFile->md5sum == $md5sum){
                        echo "File already exists in the database based on and hash, removing.\n";
                        unlink($file->getRealPath());
                        $summaryData["CBR/CBZ Files Removed"]++;
                    }
                }


            }

        }

        print_r($summaryData);

        return 0;

    }

    private function createTargetDirectory($targetDirectory){


        if (!File::exists($targetDirectory)) {
            File::makeDirectory($targetDirectory, 0755, true);
        }

    }
}
