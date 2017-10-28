<?php

class Collision
{
    /**
     * Test wheter a line segment and circle intersect
     *
     * @param  Entity $start
     * @param  Entity $end
     * @param  Entity $circle
     * @param  float  $fudge
     *
     * @return bool
     */
    public static function intersectSegmentCircle(Entity $start, Entity $end, Entity $circle, $fudge = 0.5)
    {
        $dx = $end->x - $start->x;
        $dy = $end->y - $start->y;

        $a = pow($dx, 2) + pow($dy, 2);
        $b = -2 * (pow($start->x, 2) - $start->x * $end->x - $start->x * $circle->x + $end->x * $circle->x +
                pow($start->y, 2) - $start->y * $end->y - $start->y * $circle->y + $end->y * $circle->y);
        $c = pow($start->x - $circle->x, 2) + pow($start->y - $circle->y, 2);

        if ($a == 0.0) {
            return $start->calculateDistanceBetween($circle) <= $circle->radius + $fudge;
        }

        $t = min(-$b / (2 * $a), 1.0);

        if ($t < 0) {
            return false;
        }

        $closesX = $start->x + $dx * $t;
        $closesY = $start->y + $dy * $t;
        $closesPosition = new Position($closesX, $closesY);
        $closesDistance = $closesPosition->calculateDistanceBetween($circle);

        return $closestDistance <= $circle->radius + $fudge;
    }
}
