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
        echo "Starting import process\n";

        foreach ($finder as $file) {
            $metadata = new SetMetaDataClass();

            echo "Processing file: " . $file->getRealPath() . "\n";
            $meta = $metadata->getEpubFileMetaData($file->getRealPath());

            if(!is_array($meta)){
                continue;
            }

            if(str_ends_with($file->getRealPath(), ".epub")) {

                $meta = $metadata->getEpubFileMetaData($file->getRealPath());
                if(!is_array($meta)){
                    continue;
                }

                //Check if it has a "creator" key
                if(array_key_exists("language", $meta)) {


                    if(!array_key_exists($meta['language'], $summaryData["language"])) {
                        $summaryData["language"][$meta['language']] = 0;
                    }

                    $summaryData["language"][$meta['language']]++;
                    $summaryData["totalBooks"]++;

                    if($this->option('summaryOnly') == "true"){
                        continue;
                    }

                    if(!empty($this->option('language')) && strtolower($this->option('language')) == strtolower($meta['language'])){
                        File::delete($file->getRealPath());
                        echo "Removed file: " . $file->getRealPath() . "\n";
                    }





                } else {
                    //Skip for now
                    continue;
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
