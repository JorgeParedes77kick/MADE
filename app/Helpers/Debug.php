<?php

namespace App\Helpers;

class Debug {
    public static function info($arg) {
        \Debugbar::info($arg);
    }
    public static function infoJson($arg) {
        Debug::info(json_decode(json_encode($arg)));
    }
}
