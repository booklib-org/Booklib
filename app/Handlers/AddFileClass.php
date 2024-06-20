<?php

namespace App\Handlers;

use App\Models\File;

class AddFileClass
{

    public function addFile($filename, $directory, $directory_id, $library_id)
    {
        $f = new File();
        $f->filename = $filename;
        $f->filesize = filesize($directory . "/" . $filename);
        $f->directory_id = $directory_id;
        $f->library_id = $library_id;
        $f->save();
    }
}
