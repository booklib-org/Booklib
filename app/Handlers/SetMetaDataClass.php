<?php

namespace App\Handlers;

use App\Models\File;
use App\Models\MetaType;
use App\Models\MetaValue;
use lywzx\epub\EpubParser;

class SetMetaDataClass
{


    public function getMobiFileMetaData($filename)
    {
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

        $mobi = new \Choccybiccy\Mobi\Reader($filename);

        return $mobi->getExthHeader();

    }

    public function setMobiFileMetaData(File $file)
    {
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
    public function setEpubFileMetaData(File $file)
    {

        $filename = $file->directory->directory . "/" . $file->filename;
        try{
            $epubData = shell_exec("exiftool -s \"$filename\" -j | jq 'map(with_entries(.key |= ascii_downcase))'");

        }catch (\ErrorException $e){
            unset($e);
            return 0;
        }



        foreach(json_decode($epubData, true)[0] as $key => $value){

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



    public function getEpubFileMetaData($file)
    {

        try{
            $epubData = shell_exec("exiftool -s \"$file\" -j | jq 'map(with_entries(.key |= ascii_downcase))'");

        }catch (\ErrorException $e){

            unset($e);
            return 0;
        }

        if(json_decode($epubData)){
            return json_decode($epubData, true)[0];
        }

    }

    private function SetData($file,$key,$value){



        if(is_string($value)){


            $type = MetaType::firstOrCreate([
                "type" => $key
            ]);
            if(is_array($value)) {
                foreach($value as $v){
                    MetaValue::firstOrCreate([

                        "value" => substr(iconv("UTF-8", "ASCII//TRANSLIT",$v), 0, 500),
                        "file_id" => $file->id,
                        "metadata_type" => $type->id
                    ]);
                }

            }else{
                MetaValue::firstOrCreate([

                    "value" => substr(iconv("UTF-8", "ASCII//TRANSLIT",$value), 0, 500),
                    "file_id" => $file->id,
                    "metadata_type" => $type->id
                ]);
            }


        }

    }
}
