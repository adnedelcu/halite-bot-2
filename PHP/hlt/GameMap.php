<?php

class GameMap
{
    /**
     * My ID
     *
     * @var int
     */
    protected $myId;

    /**
     * Width
     *
     * @var int
     */
    protected $width;

    /**
     * Height
     *
     * @var int
     */
    protected $height;

    /**
     * Planets
     *
     * @var Planet[]
     */
    protected $planets;

    /**
     * Players
     *
     * @var Player[]
     */
    protected $players;

    /**
     * Constructor
     *
     * @param int $myId
     * @param int $width
     * @param int $height
     */
    public function __construct($myId, $width, $height)
    {
        $this->myId = $myId;
        $this->width = $width;
        $this->height = $height;
        $this->planets = [];
        $this->players = [];
    }

    /**
     * Returns the user's player
     *
     * @return Player
     */
    public function getMe()
    {
        return $this->players[$this->myId];
    }

    /**
     * Returns the player's object
     *
     * @param  int $playerId
     *
     * @return Player
     */
    public function getPlayer($playerId)
    {
        return $this->players[$playerId];
    }

    /**
     * Returns a list of players
     *
     * @return Player[]
     */
    public function allPlayers()
    {
        return array_values($this->players);
    }

    /**
     * Get planet
     *
     * @param  int $planetId
     *
     * @return Planet
     */
    public function getPlanet($planetId)
    {
        return $this->planets[$planetId];
    }

    /**
     * Get all planets
     *
     * @return Planet[]
     */
    public function allPlanets()
    {
        return array_values($this->planets);
    }

    /**
     * A list containing all entities with their designated distances
     *
     * @param  Entity $entity
     *
     * @return Entity[]
     */
    public function nearbyEntitiesByDistance(Entity $entity)
    {
        $result = [];

        $entities = array_merge($this->allShips(), $this->allPlanets());

        foreach ($entities as $foreignEntity) {
            if ($entity === $foreignEntity) {
                continue;
            }

            $distance = $entity->calculateDistanceBetween($foreignEntity);

            $result[$distance] = $foreignEntity;
        }

        return $result;
    }

    /**
     * Checks whether there is a straight-line path to the given point, without planetary obstacles in between.
     *
     * @param  Ship   $ship
     * @param  Entity $target
     * @param  string $ignore
     *
     * @return Entity[]
     */
    public function obstaclesBetween(Ship $ship, Entity $target, $ignore = null)
    {
        $obstacles = [];
        $entities = [];

        if ($ignore == Planet::class) {
            $entities = array_merge($entities, []);
        } else {
            $entities = array_merge($entities, $this->allPlanets());
        }

        if ($ignore == Ship::class) {
            $entities = array_merge($entities, []);
        } else {
            $entities = array_merge($entities, $this->allShips());
        }

        /** @var Entity $foreignEntity */
        foreach ($entities as $foreignEntity) {
            if ($foreignEntity === $ship || $foreignEntity === $target) {
                continue;
            }

            if (Collision::intersectSegmentCircle($ship, $target, $foreignEntity, $ship->radius + 0.1)) {
                $obstacles[] = $foreignEntity;
            }
        }

        return $obstacles;
    }

    /**
     * Parse the map description from the game
     *
     * @param  string $mapString
     *
     * @return void
     */
    public function parse($mapString)
    {
        $tokens = preg_split("/[\s]+/", trim($mapString));
        $this->players = Player::parse($tokens);
        $this->planets = Planet::parse($tokens);

        if (count($tokens) > 0) {
            throw new Exception("We still have something to parse: ".implode(', ', $tokens));
        }

        $this->link();
    }

    /**
     * Updates all the entities with the correct ship and planet objects
     *
     * @return void
     */
    protected function link()
    {
        $celestialObjects = array_merge($this->allPlanets(), $this->allShips());

        /** @var Entity $celestialObject */
        foreach ($celestialObjects as $celestialObject) {
            $celestialObject->link($this->players, $this->planets);
        }
    }

    /**
     * Get all ships
     *
     * @return Ship[]
     */
    protected function allShips()
    {
        $allShips = [];

        foreach ($this->players as $player) {
            $allShips = array_merge($allShips, $player->allShips());
        }

        return $allShips;
    }

    /**
     * Check if the specified entity (x, y, r) intersects any planets. Entity is assumed to not be a planet.
     *
     * @param  Entity $target
     *
     * @return Entity|null
     */
    protected function intersectsEntity(Entity $target)
    {
        $celestialObjects = array_merge($this->allShips(), $this->allPlanets());

        /** @var Entity $celestialObject */
        foreach ($celestialObjects as $celestialObject) {
            if ($celestialObject === $target) {
                continue;
            }

            $distance = $celestialObject->calculateDistanceBetween($target);

            if ($distance <= $celestialObject->radius + $target->radius + 0.1) {
                return $celestialObject;
            }
        }

        return null;
    }
}
