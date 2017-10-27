<?php

class GameMap
{
    protected $myId;
    protected $width;
    protected $height;
    protected $planets;
    protected $players;

    public function __construct($myId, $width, $height)
    {
        $this->myId = $myId;
        $this->width = $width;
        $this->height = $height;
    }
}
