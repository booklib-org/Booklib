<?php

namespace App\Jobs;

use App\DBHandler\LikeHandler;
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
            try{
                $epubParser = new EpubParser($file->directory->directory . "/" . $file->filename);

                $epubParser->parse();
            }catch (\ErrorException $e){
                unset($e);
                continue;
            }
            catch (\Exception $e){
                unset($e);
                continue;
            }
            catch (\TypeError $e){
                unset($e);
                continue;
            }


            foreach($epubParser->getDcItem() as $key => $value){

                if(is_array($value)){
                    foreach($value as $v){
                        $this->SetData($file, $key, $v);
                    }
                }

                if(is_string($value)){
                    $this->SetData($file, $key, $value);
                }
            }

            $file->has_metadata = true;
            $file->save();
            unset($epubParser);

        }

        $mobiMetaTypes = [
            100 => "creator",
            101 => "publisher",
            102 => "imprint",
            103 => "description",
            104 => "identifier",
            105 => "subject",
            106 => "date",
            108 => "contributor",
            109 => "rights",
            110 => "subjectcode",
            111 => "type",
            112 => "source",
            524 => "language"

        ];

        foreach(File::where("has_metadata", "=", false)->where("filename", LikeHandler::getLikeString(), "%.mobi")->get() as $file){
            echo "Setting metadata for: $file->filename\n";
            $mobi = new \Choccybiccy\Mobi\Reader($file->directory->directory . "/" . $file->filename);

            foreach($mobi->getExthHeader() as $exthRecord){

                if(key_exists($exthRecord->getType(), $mobiMetaTypes)){
                    $this->SetData($file, $mobiMetaTypes[$exthRecord->getType()], $exthRecord->getData());
                }
            }

            $file->has_metadata = true;
            $file->save();
            unset($mobi);

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
