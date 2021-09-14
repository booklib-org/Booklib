<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Library extends Model
{
    use HasFactory;

    public function folders(){

        return $this->hasMany(LibraryFolder::class, "library_id", "id");

    }

    public function folderIDs(){

        $folders = $this->hasMany(LibraryFolder::class, "library_id", "id");
        return $folders->pluck('id');

    }

    public function directories(){

        return $this->hasManyThrough(Directory::class, LibraryFolder::class, "library_id", "library_folder_id")->orderBy("directory_name");

    }
}
