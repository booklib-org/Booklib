<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Directory extends Model
{
    use HasFactory;

    protected $fillable = ["directory", "library_folder_id", "parent_directory_id", "directory_name", "total_files"];
    public function files(){

        return $this->hasMany(File::class, "directory_id", "id");

    }

    public function directories(){

        return $this->hasMany(Directory::class, "parent_directory_id", "id");

    }


    public function thumbnail(){

        return $this->hasOne(Thumbnail::class, "dir_id", "id");

    }


    public function library_folder(){

        return $this->belongsTo(LibraryFolder::class, "library_folder_id", "id");

    }
}
