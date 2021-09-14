<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetaType extends Model
{
    use HasFactory;
    protected $table = "metadata_types";
    protected $fillable = ["type"];
}
