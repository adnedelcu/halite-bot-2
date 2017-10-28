<?php

class Logging
{
    protected static $filePointer;

    /**
     * Set up and truncate the log
     *
     * @param  int    $tag
     * @param  string $name
     *
     * @return void
     */
    public static function initLog($tag, $name)
    {
        $fileName = "{$tag}_{$name}.log";
        self::$filePointer = fopen(__DIR__.'/../'.$fileName, 'w');
        self::log("Initialized bot $name");
    }

    /**
     * Add text to log to log file
     *
     * @param  string $text
     *
     * @return void
     */
    public static function log($text)
    {
        if (empty(self::$filePointer)) {
            return;
        }

        if (!empty($text)) {
            fwrite(self::$filePointer, $text."\n");
        }
    }
}
