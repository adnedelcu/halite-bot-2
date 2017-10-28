<?php

class Ship extends Entity
{
    const UNDOCKED = 0;
    const DOCKING = 1;
    const DOCKED = 2;
    const UNDOCKING = 3;

    const SHIP_RADIUS = 0.5;
    const DOCK_RADIUS = 4.0;

    const MAX_SPEED = 7;

    protected static $dockingStatuses = [
        0 => self::UNDOCKED,
        1 => self::DOCKING,
        2 => self::DOCKED,
        3 => self::UNDOCKING,
    ];

    /**
     * Docking status
     *
     * @var int
     */
    public $dockingStatus;

    /**
     * Planet
     *
     * @var Planet
     */
    public $planet;

    /**
     * Docking progress
     *
     * @var int
     */
    public $dockingProgress;

    /**
     * Weapon cooldown
     *
     * @var int
     */
    public $weaponCooldown;

    /**
     * Constructor
     *
     * @param int $playerId
     * @param int $shipId
     * @param float $x
     * @param float $y
     * @param int $health
     * @param float $velX
     * @param float $velY
     * @param int $dockingStatus
     * @param int $planet
     * @param int $progress
     * @param int $cooldown
     */
    public function __construct($playerId, $shipId, $x, $y, $health, $velX, $velY, $dockingStatus, $planet, $progress, $cooldown)
    {
        $this->id = $shipId;
        $this->x = $x;
        $this->y = $y;
        $this->owner = $playerId;
        $this->radius = self::SHIP_RADIUS;
        $this->health = $health;
        $this->dockingStatus = $dockingStatus;
        $this->planet = $dockingStatus != self::UNDOCKED ? $planet : null;
        $this->dockingProgress = $progress;
        $this->weaponCooldown = $cooldown;
    }

    public function __toString()
    {
        return "Ship (id: $this->id), with radius = $this->radius, status = $this->dockingStatus, owner: $this->owner";
    }

    /**
     * Generate a command to accelerate this ship
     *
     * @param  int $magnitude
     * @param  int $angle
     *
     * @return string
     */
    public function thrust($magnitude, $angle)
    {
        $magnitude = (int)$magnitude;
        $angle = round($angle);
        return "t $this->id $magnitude $angle";
    }

    /**
     * Generate a command to dock this ship
     *
     * @param  Planet $planet
     *
     * @return string
     */
    public function dock(Planet $planet)
    {
        return "d {$this->id} {$planet->id}";
    }

    /**
     * Generate a command to undock from the current planet
     *
     * @return string
     */
    public function undock()
    {
        return "u {$this->id}";
    }

    /**
     * Move a ship to a specific target position (Entity). It is recommended to place the position
     * itself here, else navigate will crash into the target. If avoid_obstacles is set to true (default)
     * will avoid obstacles on the way, with up to maxCorrections corrections. Note that each correction accounts
     * for angularStep degrees difference, meaning that the algorithm will naively try maxCorrection degrees before
     * giving up (and returning null). The navigation will only consist of up to one command; call this method again
     * in the next turn to continue navigating to the position.
     *
     * @param  Entity  $target
     * @param  GameMap $gameMap
     * @param  int     $speed
     * @param  boolean $avoidObstacle
     * @param  integer $maxCorrections
     * @param  integer $angularStep
     * @param  boolean $ignoreShips
     * @param  boolean $ignorePlanets
     *
     * @return string
     */
    public function navigate(Entity $target, GameMap $gameMap, $speed, $avoidObstacle = true, $maxCorrections = 90, $angularStep = 1, $ignoreShips = false, $ignorePlanets = false)
    {
        if ($maxCorrections <= 0) {
            return null;
        }

        $distance = $this->calculateDistanceBetween($target);
        $angle = $this->calculateAngleBetween($target);

        if ($ignoreShips && !$ignorePlanets) {
            $ignore = Ship::class;
        } elseif (!$ignoreShips && $ignorePlanets) {
            $ignore = Planet::class;
        } elseif ($ignoreShips && $ignorePlanets) {
            $ignore = Entity::class;
        } else {
            $ignore = null;
        }

        if ($avoidObstacle && $gameMap->obstaclesBetween($this, $target, $ignore)) {
            $newTargetX = cos(deg2rad($angle + $angularStep)) * $distance;
            $newTargetY = sin(deg2rad($angle + $angularStep)) * $distance;
            $newTarget = new Position($this->x + $newTargetX, $this->y + $newTargetY);

            return $this->navigate($newTarget, $gameMap, $speed, true, $maxCorrections - 1, $angularStep);
        }

        $speed = ($distance >= $speed) ? $speed : $distance;

        return $this->thrust($speed, $angle);
    }

    /**
     * Determine whether a ship can dock to a planet
     *
     * @param  Planet $planet
     *
     * @return boolean
     */
    public function canDock(Planet $planet)
    {
        return $this->calculateDistanceBetween($planet) <= $planet->radius + self::DOCK_RADIUS;
    }

    /**
     * This function serves to take the id values set in the parse function and use it to populate the ship
     * owner and dockedShips params with the actual objects representing each, rather than IDs
     *
     * @param  Player[] $players
     * @param  Planet[] $planets
     *
     * @return void
     */
    public function link(array $players, array $planets)
    {
        $this->owner = !empty($players[$this->owner]) ? $players[$this->owner] : null;
        $this->planet = !empty($planets[$this->planet]) ? $planets[$this->planet] : null;
    }

    /**
     * Parse a single ship given tokenized input from the game environment
     *
     * @param  int      $playerId
     * @param  string[] &$tokens
     *
     * @return [int, Ship]
     */
    protected static function parseSingle($playerId, array &$tokens)
    {
        $sid = (int)array_shift($tokens);
        $x = (float)array_shift($tokens);
        $y = (float)array_shift($tokens);
        $hp = (int)array_shift($tokens);
        $velX = (float)array_shift($tokens);
        $velY = (float)array_shift($tokens);
        $docked = (int)array_shift($tokens);
        $dockedPlanet = (int)array_shift($tokens);
        $progress = (int)array_shift($tokens);
        $cooldown = (int)array_shift($tokens);

        $ship = new Ship($playerId, $sid, $x, $y, $hp, $velX, $velY, $docked, $dockedPlanet, $progress, $cooldown);

        return [$sid, $ship];
    }

    /**
     * Parse ship data given a tokenized input
     *
     * @param  int      $playerId
     * @param  string[] &$tokens
     *
     * @return Ship[]
     */
    public static function parse($playerId, array &$tokens)
    {
        $ships = [];

        $numShips = (int)array_shift($tokens);

        for ($i = 0; $i < $numShips; $i++) {
            list($shipId, $ships[$shipId]) = self::parseSingle($playerId, $tokens);
        }

        return $ships;
    }
}
