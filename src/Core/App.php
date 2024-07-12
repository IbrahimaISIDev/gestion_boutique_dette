<?php

namespace Src\App;

class App
{
    public static function notFound()
    {
        http_response_code(404);
        echo "Page not found";
        exit();
    }
}




