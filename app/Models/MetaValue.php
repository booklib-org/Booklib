<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetaValue extends Model
{
    use HasFactory;
    protected $table = "metadata_values";
    protected $fillable = ["value", "file_id", "metadata_type"];


    public function getFile(){

        return $this->belongsTo(File::class, "file_id", "id");

    }

    public function Author(){

        try{
        $type = MetaType::where("type", "=", "creator")->first()->id;

            $author = MetaValue::where("metadata_type", "=", $type)->where("file_id", "=", $this->file_id)->first()->value;

            return $author;
          }catch(\ErrorException $e){
          unset($e);

        }

    }

    public function type(){

        return $this->hasOne(MetaType::class, "id", "metadata_type");

    }

    public function getTitleOrFilename(){


        try{
            $type = MetaType::where("type", "=", "title")->first()->id;

            $title = MetaValue::where("metadata_type", "=", $type)->where("file_id", "=", $this->file_id)->first()->value;

            if(strlen($title) >0 ) {
                return $title;
            }else{
                return File::findOrFail($this->file_id)->filename;
            }
        }catch(\ErrorException $e){
            unset($e);
            return File::findOrFail($this->file_id)->filename;

        }


    }
}
