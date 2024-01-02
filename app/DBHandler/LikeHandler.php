<?php

namespace App\DBHandler;

class LikeHandler
{
    public static function getLikeString(){

        if(getenv("DB_CONNECTION") == "pgsql") {
            return "ILIKE";
        }else{
            return "LIKE";
        }
    }
}
