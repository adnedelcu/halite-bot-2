<?php

abstract class Entity
{
    public $x;
    public $y;
    public $radius;
    public $health;
    public $owner;
    public $id;

    /**
     * Constructor
     *
     * @param int $x
     * @param int $y
     * @param float $radius
     * @param int $health
     * @param int $player
     * @param int $entityId
     */
    public function __construct($x, $y, $radius, $health, $player, $entityId)
    {
        $this->x = $x;
        $this->y = $y;
        $this->radius = $radius;
        $this->health = $health;
        $this->owner = $player;
        $this->id = $entityId;
    }

    /**
     * Calculates the distance between this object and the target
     *
     * @param  Entity $target
     *
     * @return float
     */
    public function calculateDistanceBetween(Entity $target)
    {
        $dx = $target->x - $this->x;
        $dy = $target->y - $this->y;

        return sqrt($dx ** 2 + $dy ** 2);
    }

    /**
     * Calculates the angle between this object and the target in degress
     *
     * @param  Entity $target
     *
     * @return float
     */
    public function calculateAngleBetween(Entity $target)
    {
        Logging::log("this = ".get_class($this).", target = ".get_class($target));
        $atan = atan2($target->y - $this->y, $target->x - $this->x);
        Logging::log("atan2($target->y - $this->y, $target->x - $this->x) = ".$atan);
        Logging::log("degrees($atan) = ".rad2deg($atan));
        return rad2deg($atan) % 360;
    }

    /**
     * Find the closes point to the given ship near the given target, outside its given radius,
     * with an added fudge of minDistance
     *
     * @param  Entity  $target
     * @param  integer $minDistance
     *
     * @return Position
     */
    public function closestPointTo(Entity $target, $minDistance = 3)
    {
        $angle = $target->calculateAngleBetween($this);
        $radius = $target->radius + $minDistance;
        $x = $target->x + $radius * cos(deg2rad($angle));
        $y = $target->y + $radius * sin(deg2rad($angle));

        return new Position($x, $y);
    }

    /**
     * Replace the ids with their respective objects
     *
     * @param  Player[] $players
     * @param  Planet[] $planets
     *
     * @return void
     */
    abstract public function link(array $players, array $planets);

    /**
     * To String
     *
     * @return string
     */
    public function __toString()
    {
        return "Entity ".static::class." (id: {$this->id}) at position: (x = {$this->x}, y = {$this->y}), with radius = {$this->radius}";
    }
}
