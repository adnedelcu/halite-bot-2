<?php

class Game
{
    protected $map;
    protected $initialMap;

    public function __construct($name)
    {
        $tag = intval(self::getString());
    }

    public static function getString()
    {
        $input = fgets(STDIN);

        if ($input === false) {
            exit;
        }

        return rtrim($input, "\n");
    }
}
