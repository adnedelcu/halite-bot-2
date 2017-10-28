<?php

class Planet extends Entity
{
    /**
     * Docking spots
     *
     * @var int
     */
    protected $numDockingSpots;

    /**
     * Current production
     *
     * @var int
     */
    protected $currentProduction;

    /**
     * Remaining resources
     *
     * @var int
     */
    protected $remainingResources;

    /**
     * A list of docked ships ids
     *
     * @var int[]
     */
    protected $dockedShipIds;

    /**
     * A list of docked ships
     *
     * @var Ship[]
     */
    protected $dockedShips;

    /**
     * Constructor
     *
     * @param int    $planetId
     * @param int    $x
     * @param int    $y
     * @param int    $health
     * @param float  $radius
     * @param int    $dockingSpots
     * @param int    $current
     * @param int    $remaining
     * @param int    $owned
     * @param int    $owner
     * @param Ship[] $dockedShips
     */
    public function __construct($planetId, $x, $y, $health, $radius, $dockingSpots, $current, $remaining, $owned, $owner, $dockedShips)
    {
        $this->id = $planetId;
        $this->x = $x;
        $this->y = $y;
        $this->radius = $radius;
        $this->numDockingSpots = $dockingSpots;
        $this->currentProduction = $current;
        $this->remainingResources = $remaining;
        $this->health = $health;
        $this->owner = $owned ? $owner : null;
        $this->dockedShipIds = $dockedShips;
        $this->dockedShips = [];
    }

    public function __toString()
    {
        return "Planet (id: $this->id) at position: (x = $this->x, y = $this->y), with radius = $this->radius, owner: ".($this->owner instanceof Player ? $this->owner->id : $this->owner);
    }

    /**
     * Return the docked ship designated by its id.
     *
     * @param  int $shipId
     *
     * @return Ship
     */
    public function getDockedShip($shipId)
    {
        return $this->dockedShips[$shipId];
    }

    /**
     * Return a list of all ships docked into the planet
     *
     * @return Ship[]
     */
    public function allDockedShips()
    {
        return array_values($this->dockedShips);
    }

    /**
     * Determines if the planet has an owner
     *
     * @return boolean
     */
    public function isOwned()
    {
        return $this->owner instanceof Player;
    }

    /**
     * Determines if the planet has been fully occupied (all possible ships are docked)
     *
     * @return boolean
     */
    public function isFull()
    {
        return count($this->dockedShipIds) >= $this->numDockingSpots;
    }

    /**
     * This function serves to take the id values set in the parse function and use it to populate the planet
     * owner and dockedShips params with the actual objects representing each, rather than IDs
     *
     * @param  Player[] $players
     * @param  Planet[] $planets
     *
     * @return void
     */
    public function link(array $players, array $planets)
    {
        if ($this->owner != null && !($this->owner instanceof Player)) {
            $this->owner = $players[$this->owner];

            foreach ($this->dockedShipIds as $shipId) {
                $this->dockedShips[$shipId] = $this->owner->getShip($shipId);
            }
        }
    }

    /**
     * Parse a single planet given tokenized input from the game environment
     *
     * @param  string[]  &$tokens
     *
     * @return [int, Planet]
     */
    private static function parseSingle(array &$tokens)
    {
        $plid = (int)array_shift($tokens);
        $x = (float)array_shift($tokens);
        $y = (float)array_shift($tokens);
        $health = (int)array_shift($tokens);
        $radius = (float)array_shift($tokens);
        $docking = (int)array_shift($tokens);
        $current = (int)array_shift($tokens);
        $remaining = (int)array_shift($tokens);
        $owned = (bool)array_shift($tokens);
        $owner = (int)array_shift($tokens);
        $numDockingShips = (int)array_shift($tokens);

        $dockedShips = [];

        for ($i = 0; $i < $numDockingShips; $i++) {
            $shipId = (int)array_shift($tokens);
            $dockedShips[] = $shipId;
        }

        $planet = new Planet($plid, $x, $y, $health, $radius, $docking, $current, $remaining, $owned, $owner, $dockedShips);

        return [$plid, $planet];
    }

    /**
     * Parse planet data given a tokenized input
     *
     * @param  string[]  &$tokens
     *
     * @return Planet[]
     */
    public static function parse(array &$tokens)
    {
        $numPlanets = (int)array_shift($tokens);
        $planets = [];

        for ($i = 0; $i < $numPlanets; $i++) {
            list($plid, $planets[$plid]) = self::parseSingle($tokens);
        }

        return $planets;
    }
}
