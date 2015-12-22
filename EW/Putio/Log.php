<?php

namespace EW\Putio;

class Log
{
    const LEVEL_INFO = 0;
    const LEVEL_DEBUG = 1;

    /** @var callable */
    protected static $logCallback = null;
    protected static $logLevel = self::LEVEL_DEBUG;

    public static function setCallback(callable $callback) {
        self::$logCallback = $callback;
    }

    public static function setLogLevel($level) {
        self::$logLevel = $level;
    }

    public static function log($message, $level = self::LEVEL_DEBUG) {
        if(is_null(self::$logCallback)) {
            return; //logging not configured
        }

        if(!is_scalar($message)) {
            $message = print_r($message, true);
        }

        $message = trim($message) . "\n";

        if($level <= self::$logLevel) {
            self::$logCallback->__invoke($message, $level);
        }
    }
}