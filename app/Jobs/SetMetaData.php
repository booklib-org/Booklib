<?php

namespace App\Jobs;

use App\DBHandler\LikeHandler;
use App\Handlers\SetMetaDataClass;
use App\Models\File;
use App\Models\MetaType;
use App\Models\MetaValue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use lywzx\epub\EpubParser;

class SetMetaData implements ShouldQueue
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

        if(file_exists("/tmp/SetMetaData.lock")){
            return 0;
        }else{
            exec("touch /tmp/SetMetaData.lock");
        }

        foreach(File::where("has_metadata", "=", false)->where("filename", LikeHandler::getLikeString(), "%.epub")->get() as $file){
            echo "Setting metadata for: $file->filename\n";

            $set = new SetMetaDataClass();
            $set->setEpubFileMetaData($file);
            unset($set);

        }


        foreach(File::where("has_metadata", "=", false)->where("filename", LikeHandler::getLikeString(), "%.mobi")->get() as $file){
            echo "Setting metadata for: $file->filename\n";

            $set = new SetMetaDataClass();
            $set->setMobiFileMetaData($file);
            unset($set);

        }

        unlink("/tmp/SetMetaData.lock");


    }

    private function SetData($file,$key,$value){



        if(is_string($value)){


            $type = MetaType::firstOrCreate([
                "type" => $key
            ]);

            $value = MetaValue::firstOrCreate([
                "value" => substr(iconv("UTF-8", "ASCII//TRANSLIT",$value), 0, 500),
                "file_id" => $file->id,
                "metadata_type" => $type->id
            ]);

        }

    }
}
