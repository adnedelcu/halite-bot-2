<?php

class Game
{
    protected $map;
    protected $initialMap;

    /**
     * Constructor
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $tag = intval(self::getString());
        Logging::initLog($tag, $name);
        $mapSize = explode(' ', self::getString());
        $width = (int)array_shift($mapSize);
        $height = (int)array_shift($mapSize);
        self::sendString($name);
        self::doneSending();
        $this->map = new GameMap($tag, $width, $height);
        $this->updateMap();
        $this->initialMap = clone $this->map;
    }

    /**
     * Parse the map given by the engine
     *
     * @return GameMap
     */
    public function updateMap()
    {
        Logging::log("---NEW TURN---");
        $this->map->parse(self::getString());
        return $this->map;
    }

    /**
     * Send data to the game. Call @see Game::doneSending() once finished.
     *
     * @param  string $toSend
     *
     * @return void
     */
    public static function sendString($toSend)
    {
        fwrite(STDOUT, $toSend);
    }

    /**
     * Finish sending commands to the game
     *
     * @return void
     */
    public static function doneSending()
    {
        fwrite(STDOUT, "\n");
    }

    /**
     * Read input from the game
     *
     * @return string
     */
    public static function getString()
    {
        $input = fgets(STDIN);

        if ($input === false) {
            exit;
        }

        Logging::log("Got string: ".rtrim($input, "\n"));

        return rtrim($input, "\n");
    }

    /**
     * Issue the given list of commands
     *
     * @param  string[]  $commandQueue
     *
     * @return void
     */
    public static function sendCommandQueue(array $commandQueue)
    {
        foreach ($commandQueue as $command) {
            self::sendString($command);
        }

        self::doneSending();
    }
}
