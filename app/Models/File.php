<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = ["filename", "filesize", "directory_id", "library_id"];

    public function directory(){

        return $this->belongsTo(Directory::class, "directory_id", "id");

    }





    public function thumbnail(){

        return $this->hasOne(Thumbnail::class, "file_id", "id");

    }

    public function getFilenameWithoutExtension(){

        if(str_ends_with(strtolower($this->filename), ".cbr")){
            return substr($this->filename, 0, strlen($this->filename) -4);
        }
        if(str_ends_with(strtolower($this->filename), ".cbz")){
            return substr($this->filename, 0, strlen($this->filename) -4);
        }
        if(str_ends_with(strtolower($this->filename), ".pdf")){
            return substr($this->filename, 0, strlen($this->filename) -4);
        }
        if(str_ends_with(strtolower($this->filename), ".mobi")){
            return substr($this->filename, 0, strlen($this->filename) -5);
        }
        if(str_ends_with(strtolower($this->filename), ".epub")){
            return substr($this->filename, 0, strlen($this->filename) -5);
        }
    }

    public function author(){
        try{
        $type = MetaType::where("type", "=", "creator")->first()->id;

        $author = MetaValue::where("metadata_type", "=", $type)->where("file_id", "=", $this->id)->first()->value;

        return $author;
        }catch(\ErrorException $e){
            unset($e);

        }
    }

    public function description(){
        try{
            $type = MetaType::where("type", "=", "description")->first()->id;

            $author = MetaValue::where("metadata_type", "=", $type)->where("file_id", "=", $this->id)->first()->value;

            return $author;
        }catch(\ErrorException $e){
            unset($e);

        }
    }

    public function language(){
        try{
            $type = MetaType::where("type", "=", "language")->first()->id;

            $author = MetaValue::where("metadata_type", "=", $type)->where("file_id", "=", $this->id)->first()->value;

            return $author;
        }catch(\ErrorException $e){
            unset($e);

        }
    }


    public function otherMeta(){

            return MetaValue::where("file_id", "=", $this->id)->get();

    }

    public function titleOrFilename(){
        try{

            $type = MetaType::where("type", "=", "title")->first()->id;

            $author = MetaValue::where("metadata_type", "=", $type)->where("file_id", "=", $this->id)->first()->value;

            if(strlen($author) > 0){
                return $author;
            }else{
                return $this->filename;
            }


        }catch(\ErrorException $e){
            unset($e);
            return $this->filename;
        }

    }


    public function title(){
        try{

        $type = MetaType::where("type", "=", "title")->first()->id;

            $author = MetaValue::where("metadata_type", "=", $type)->where("file_id", "=", $this->id)->first()->value;
            return $author;

          }catch(\ErrorException $e){
             unset($e);

         }

    }
}
