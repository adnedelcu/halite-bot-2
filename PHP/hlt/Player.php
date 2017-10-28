<?php

class Player
{
    protected $id;
    protected $ships;

    /**
     * Constructor
     *
     * @param int    $playerId
     * @param array  $ships
     */
    public function __construct($playerId, $ships = [])
    {
        $this->id = $playerId;
        $this->ships = $ships;
    }

    public function __toString()
    {
        return "Player {$this->id} with ships {$this->allShips()}";
    }

    /**
     * Returns a list of all ships which belong to the user
     *
     * @return Ship[]
     */
    public function allShips()
    {
        return array_values($this->ships);
    }

    /**
     * Returns the ship designated by shipId belonging to this user
     *
     * @param  int $shipId
     *
     * @return Ship
     */
    public function getShip($shipId)
    {
        return $this->ships[$shipId];
    }

    /**
     * Parse an entire user input string from the Halite engine for all users.
     *
     * @param  string[]  &$tokens
     *
     * @return Player[]
     */
    public static function parse(array &$tokens)
    {
        $playersNo = (int)array_shift($tokens);
        Logging::log("Tokens remaining after retriving how many players: ".count($tokens));
        Logging::log("Number of players: {$playersNo}");
        $players = [];

        for ($i = 0; $i < $playersNo; $i++) {
            list($playerId, $players[$playerId]) = self::parseSingle($tokens);
        }

        return $players;
    }

    /**
     * Parse one user given an input string from the Halite engine.
     *
     * @param  string[]  &$tokens
     *
     * @return [int, Player]
     */
    protected static function parseSingle(array &$tokens)
    {
        $playerId = (int)array_shift($tokens);
        Logging::log("Tokens remaining after retriving playerId: ".count($tokens));
        Logging::log("Player ID: {$playerId}");
        $ships = Ship::parse($playerId, $tokens);
        $player = new Player($playerId, $ships);

        return [$playerId, $player];
    }
}
