<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    use HasFactory;

    public function isAdmin()
    {
        if($this->role === "Administrator")
        {
            return true;
        }
        else
        {
            return false;
        }
    }

}
