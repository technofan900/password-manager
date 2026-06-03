<?php

namespace Core\Middleware;
use Exception;


class Middleware {

    const MAP = [
        'guest' => Guest::class,
        'auth' => Auth::class,
        'admin' => Admin::class
    ];

    public static function resolve($key) {
        if (! $key) {
            return;
        }

        $middleware = static::MAP[$key] ?? false;
        if ($middleware){
            (new $middleware)->handle();
        } else {
            throw new Exception("No middleware found for key ($key)!");
        }
       
    }

}

