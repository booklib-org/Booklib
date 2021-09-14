<?php

namespace App\Console\Commands;

use App\Models\ImportedFile;
use App\Models\LibraryFolder;
use Illuminate\Console\Command;
use SimpleXmlReader\SimpleXmlReader;

class ImportOPDS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Import:OPDS';

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
    public function handle()
    {

        foreach(\App\Models\ImportOPDS::orderBy("updated_at", "ASC")->get() as $opds){

            $auth = base64_encode($opds->username . ":" . $opds->password);
            $context = stream_context_create([
                "http" => [
                    "header" => "Authorization: Basic $auth"
                ]
            ]);

            $dir = LibraryFolder::findOrFail($opds->library_folder_id)->path;
            $xml = simplexml_load_string(file_get_contents($opds->url, false, $context));

            foreach($xml->entry as $entry){

                $catalog = dirname($opds->url) . $entry->link["href"];

                //Getting the actuall list
                $books = simplexml_load_string(file_get_contents($catalog, false, $context));

                $this->ProcessBookPage($books, $opds, $dir, $context);


            }





        }

        return 0;
    }

    private function ProcessBookPage($books, $opds, $dir, $context){

        $hasMorePages = false;
        $url = "";
        foreach($books->link as $link){
            try{
                if($link["rel"] == "next"){
                    $hasMorePages = true;
                    $url = dirname($opds->url) . $link["href"];
                }
            }catch(\ErrorException $e){
                unset($e);
            }
        }

        foreach($books->entry as $entry){



            try{
                $author = $entry->author->name ;
                try{
                    mkdir($dir . "/" . $author);
                    $Savedir = $dir . "/" . $author;
                }catch(\ErrorException $e){
                $Savedir = $dir . "/" . $author;
                unset($e);
                }
            }catch(\ErrorException $e){
                unset($e);
                try{
                    mkdir($dir . "/Unsorted");
                }catch(\ErrorException $e){
                    unset($e);
                }
                $Savedir = $dir . "/Unsorted";
            }




            foreach($entry->link as $link) {

                if(!str_contains($link["type"], "image")){

                    if(!ImportedFile::where("opds_feed_id", "=", $opds->id)->where("uuid", "=", $entry->id)->where("url", "=", $link["href"])->exists()){
                        try{
                            $downloadUrl = dirname($opds->url) . $link["href"];
                            $content = get_headers($downloadUrl,1);
                            $content = array_change_key_case($content, CASE_LOWER);

                            // by header
                            if ($content['content-disposition']) {
                                $tmp_name = explode('=', $content['content-disposition']);
                                if ($tmp_name[1]) $realfilename = trim($tmp_name[1],'";\'');

                            } else

// by URL Basename
                            {
                                $stripped_url = preg_replace('/\\?.*/', '', $downloadUrl);
                                $realfilename = urldecode(basename($stripped_url));

                            }


                        }catch(\ErrorException $e){
                            unset($e);
                            $stripped_url = preg_replace('/\\?.*/', '', $downloadUrl);
                            $realfilename = urldecode(basename($stripped_url));
                        }

                        try {
                            $book = file_get_contents($downloadUrl, false, $context);
                            file_put_contents($Savedir . "/" . $realfilename, $book);

                            $x = new ImportedFile();
                            $x->opds_feed_id = $opds->id;
                            $x->uuid = $entry->id;
                            $x->url = $link["href"];
                            $x->save();
                            unset($x);

                        }catch(\ErrorException $e){
                            unset($e);
                        }
                    }



                }


            }

            echo $entry->title . "\n";

        }


        if($hasMorePages){
            $nextBooksUrl = $url;
            $nextBooks = simplexml_load_string(file_get_contents($nextBooksUrl, false, $context));

            $this->ProcessBookPage($nextBooks, $opds, $dir, $context);
        }

    }
}
