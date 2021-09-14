<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LibraryFolder extends Model
{
    use HasFactory;

    public function directories(){

        return $this->hasMany(Directory::class, "library_folder_id", "id");

    }

    public function library(){

        return $this->belongsTo(Library::class, "library_id", "id");

    }


}
