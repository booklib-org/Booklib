<?php

namespace App\Jobs;

use App\Models\Directory;
use App\Models\File;
use App\Models\Setting;
use App\Models\Thumbnail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use lywzx\epub\EpubParser;

class GenerateThumbnails implements ShouldQueue
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
    public function handle()
    {

        if(file_exists("/tmp/GenerateThumbnails.lock")){
            return 0;
        }else{
            exec("touch /tmp/GenerateThumbnails.lock");
        }

        foreach(File::where("has_thumbnail", "=", false)->where("thumbnail_generation_tried", "=", false)->take(100)->get() as $file){
            exec("mkdir -pv \"" . storage_path("app/tmp/thumbnails/") . "\"");
            $file->thumbnail_generation_tried = true;
            $file->save();
            if(file_exists($file->directory->directory . "/" . $file->filename)){

                if(str_ends_with(strtolower($file->filename), ".cbr")){

                    //RAR
                    if(getenv('APP_DEBUG') == true){
                        echo "Currently Processing: " . $file->filename . "\n";
                    }

                    exec("unrar lb \"" .
                        str_replace("\"", "\\\"",$file->directory->directory)
                        . "/" .

                        str_replace("\"", "\\\"",$file->filename) . "\"", $files);
                    sort($files);


                    foreach($files as $fil){
                        if(str_ends_with(strtolower($fil), ".png") || str_ends_with(strtolower($fil), ".jpg") || str_ends_with(strtolower($fil), ".jpeg")
                            || str_ends_with(strtolower($fil), ".bmp")  || str_ends_with(strtolower($fil), ".gif")){
                            $fileToExport = $fil;
                            break;
                        }
                    }

                    if(isset($fileToExport)){


                        exec("unrar x -o \"" .

                            str_replace("\"", "\\\"",$file->directory->directory)



                            . "/" .
                            str_replace("\"", "\\\"",$file->filename)

                            . "\" -- \"" . str_replace("`", "\`",str_replace("\"", "\\\"",$fileToExport)) . "\" " . storage_path("app/tmp/thumbnails"));

                        $this->SaveThumb($fileToExport, $file);
                    }

                    unset($files);
                    unset($fileToExport);

                    exec("rm -rf \"" . storage_path("app/tmp/thumbnails/") . "\"");



                }elseif(str_ends_with(strtolower($file->filename), ".cbz")){

                    if(getenv('APP_DEBUG') == true){
                        echo "Currently Processing: " . $file->filename . "\n";
                    }
                    $za = new \ZipArchive();

                    $za->open($file->directory->directory . "/" . $file->filename);

                    $firstPageData = $this->getZipFirstPageData($za, 0);

                    if(!is_null($firstPageData)){
                        file_put_contents(storage_path("app/tmp/thumbnails/thumb." . pathinfo($firstPageData->pageName, PATHINFO_EXTENSION)), $firstPageData->pageContent);

                        $this->SaveThumb("thumb." . pathinfo($firstPageData->pageName, PATHINFO_EXTENSION), $file);

                        $za->close();

                        exec("rm -rf \"" . storage_path("app/tmp/thumbnails/") . "\"");


                    }

                }elseif(str_ends_with(strtolower($file->filename), ".pdf")){
                    if(getenv('APP_DEBUG') == true){
                        echo "Currently Processing: " . $file->filename . "\n";
                    }
                    exec("pdftk \"" . $file->directory->directory . "/" . $file->filename . "\" cat 1 output \"" . storage_path("app/tmp/thumbnails/1.pdf") . "\"");
                    exec("convert -colorspace RGB -interlace none -quality 100 \"" . storage_path("app/tmp/thumbnails/1.pdf") . "\" \"" . storage_path("app/tmp/thumbnails/1.jpg") . "\"");

                    $this->SaveThumb("1.jpg", $file);

                    exec("rm -rf \"" . storage_path("app/tmp/thumbnails/") . "\"");


                }elseif(str_ends_with(strtolower($file->filename), ".epub")){
                    if(getenv('APP_DEBUG') == true){
                        echo "Currently Processing: " . $file->filename . "\n";
                    }
                    try{
                        $epubParser = new EpubParser($file->directory->directory . "/" . $file->filename);
                        $epubParser->parse();
                        $images = $epubParser->getManifestByType("/image\/\w+/");

                        if(is_array($images)){
                            foreach($images as $image){
                                $firstimage = $image;
                                break;
                            }

                            if($firstimage){
                                $epubParser->extract(storage_path("app/tmp/thumbnails/"), '/image\/\w+/');
                                $firstPage = $firstimage["href"];

                                $this->SaveThumb($firstPage, $file);
                            }

                        }



                        unset($epubParser);
                    }catch(\ErrorException $e){
                        unset($e);
                        $file->thumbnail_generation_tried = true;
                        $file->save();

                    }catch(\TypeError $e){
                        unset($e);
                        $file->thumbnail_generation_tried = true;
                        $file->save();
                    }catch(\Exception $e){
                        unset($e);
                        $file->thumbnail_generation_tried = true;
                        $file->save();
                    }

                }


            }

        }

        unlink("/tmp/GenerateThumbnails.lock");

        if(File::where("has_thumbnail", "=", false)->where("thumbnail_generation_tried", "=", false)->count() > 0){

            $dispatch = GenerateThumbnails::dispatch()->onConnection('database');

        }

    }

    private function SaveThumb($firstPage, $file){
        try{
            if(str_ends_with(strtolower($firstPage), ".jpeg") || str_ends_with(strtolower($firstPage), ".jpg")){
                $img = imagecreatefromjpeg(storage_path("app/tmp/thumbnails/" . $firstPage));
            }elseif(str_ends_with(strtolower($firstPage), ".png")){
                $img = imagecreatefrompng(storage_path("app/tmp/thumbnails/" . $firstPage));
            }elseif(str_ends_with(strtolower($firstPage), ".bmp")) {
                $img = imagecreatefrombmp(storage_path("app/tmp/thumbnails/" . $firstPage));
            }elseif(str_ends_with(strtolower($firstPage), ".gif")) {
                $img = imagecreatefromgif(storage_path("app/tmp/thumbnails/" . $firstPage));
            }

            if($img){

                $thumbnail = imagescale($img, 150);

                if($thumbnail) {

                    $compression = 0;
                    $thumbnailQuality = Setting::where("setting", "=", "thumbnail_quality")->first();

                    if($thumbnailQuality->value == "High"){
                        $compression = 100;
                    }elseif($thumbnailQuality->value == "Medium"){
                        $compression = 75;
                    }
                    elseif($thumbnailQuality->value == "Low"){
                        $compression = 25;
                    }

                    imagejpeg($thumbnail, public_path("/img/thumb/" . $file->id . ".jpg"), $compression);
                    imagedestroy($thumbnail);
                    $x = new Thumbnail();
                    $x->filename = $file->id . ".jpg";
                    $x->filesize = 0;
                    $x->storage_path = "/img/thumb/";
                    $x->file_id = $file->id;
                    $x->save();
                    $file->has_thumbnail = true;
                    $file->save();

                }
            }

        }catch(\ErrorException $e){
            //TODO: Log this to some kind of error log for user review. Ticket ID 8
            unset($e);

        }


    }

    protected function getZipFirstPageData(\ZipArchive $za, $index){

        $details = new \stdClass();

        try{
            $details->pageName = $za->statIndex($index)["name"];
            $details->pageContent = $za->getFromIndex($index, 0);

            if(str_ends_with($details->pageName, "/")){
                $index = $index + 1;
                return $this->getZipFirstPageData($za, $index);
            }else{
                return $details;
            }

        }catch(\ValueError $valueError){
            unset($valueError);
            //TODO: Log this to some kind of error log for user review. Ticket ID 8
            return null;
        }


    }

}
