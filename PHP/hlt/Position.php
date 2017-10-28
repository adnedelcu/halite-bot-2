<?php

class Position extends Entity
{
    /**
     * Constructor
     *
     * @param int $x
     * @param int $y
     */
    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
        $this->radius = 0;
        $this->health = null;
        $this->owner = null;
        $this->id = null;
    }

    public function link(array $players, array $planets)
    {
        throw new Exception('Not implemented');
    }
}
