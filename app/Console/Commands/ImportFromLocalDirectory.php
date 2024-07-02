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

class ImportFromLocalDirectory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-from-local-directory {--libraryId=} {--removeDuplicates=} {--language=} {--summaryOnly=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Use this command to import files from a local directory into the specified library. This command will not import files that are already in the library. Files will be imported from the /import directory and placed into a AuthorName/BookTitle directory structure. Currently epub and mobi books are supported when metadata is found.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
//        echo $this->argument('RemoveDuplicates') . PHP_EOL;


        if($this->option('removeDuplicates') == "true") {
            $removeDuplicates = true;
        } else {
            $removeDuplicates = false;
        }

        if(empty($this->option('libraryId'))){
            $this->error("You must specify a libraryId");
            return 1;
        }
        $libraryId = $this->option('libraryId');
        $library = \App\Models\Library::find($libraryId);

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
                if(array_key_exists("creator", $meta) && array_key_exists("title", $meta) && array_key_exists("language", $meta)) {

                    if(is_array($meta['creator'])){
                        $meta['creator'] = $meta['creator'][0];
                    }
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

                    $targetDirectory = $library->folders->first()->path . '/' . $meta['creator'];
                    $targetDirectory = str_replace("/", "_", $targetDirectory);
                    $this->createTargetDirectory($targetDirectory);

                    $targetPath = $targetDirectory . '/' . $file->getFilename();

                    //Check if a file with this creator, title and language already exists
                    $creatorMetaType = MetaType::where("type", "=", "creator")->first()->id ?? 0;
                    $titleMetaType = MetaType::where("type", "=", "title")->first()->id ?? 0;
                    $languageMetaType = MetaType::where("type", "=", "language")->first()->id ?? 0;

                    if(!empty($this->option('language')) && strtolower($this->option('language')) != strtolower($meta['language'])){
                        //Skip this book
                        continue;
                    }
                    $creatorBooks = MetaValue::where("metadata_type", "=", $creatorMetaType)->where("value", "=", $meta['creator'])->get("file_id");
                    $titleBooks = MetaValue::where("metadata_type", "=", $titleMetaType)->where("value", "=", $meta['title'])->whereIn("file_id", $creatorBooks)->get("file_id");
                    if(MetaValue::where("metadata_type", "=", $languageMetaType)->where("value", "=", $meta['language'])->whereIn("file_id", $titleBooks)->exists()) {

                            $this->info("File already exists: " . $file->getRealPath());

                            if($removeDuplicates) {
                                $this->info("Removing duplicate file: " . $file->getRealPath());
                                File::delete($file->getRealPath());

                            }
                        continue;
                    }

                    $addFile = new AddFileClass();

                    if($removeDuplicates) {
                        if (File::move($file->getRealPath(), $targetPath)) {
                            $this->info("Moved: " . $file->getRealPath() . " to $targetPath");
                        } else {
                            $this->error("Failed to move: " . $file->getRealPath());
                            continue;
                        }
                    }else{
                        if (File::copy($file->getRealPath(), $targetPath)) {
                            $this->info("Copied: " . $file->getRealPath() . " to $targetPath");
                        } else {
                            $this->error("Failed to copy: " . $file->getRealPath());
                            continue;
                        }

                    }

                    $parentdir = Directory::firstOrCreate([
                        "directory" => $targetDirectory,
                        "library_folder_id" => $library->id,
                        "parent_directory_id" => 0,
                        "directory_name" => $meta['creator']
                    ]);

                    $newFile = $addFile->addFile($file->getFilename(), $targetDirectory, $parentdir->id, $library->id);
                    try{
                        $metadata->setEpubFileMetaData($newFile);
                    }catch(\Exception $e){
                        $this->error("Failed to set metadata for file: " . $file->getRealPath());
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
            File::makeDirectory(substr($targetDirectory, 0, 128), 0755, true);
        }

    }
}
