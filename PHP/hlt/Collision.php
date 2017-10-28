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
    public static function intersectSegmentCircle(Entity $start, Entity $end, Entity $circle, $fudge)
    {
        $dx = $end->x - $start->x;
        $dy = $end->y - $start->y;

        $a = $dx ** 2 + $dy ** 2;

        if ($a === 0.0) {
            return $start->calculateDistanceBetween($circle) <= ($circle->radius + $fudge);
        }

        $b = -2 * ($start->x ** 2 - $start->x * $end->x - $start->x * $circle->x + $end->x * $circle->x + $start->y ** 2 - $start->y * $end->y - $start->y * $circle->y + $end->y * $circle->y);
        $c = ($start->x - $circle->x) ** 2 + ($start->y - $circle->y) ** 2;

        $t = min(-$b / (2 * $a), 1.0);

        if ($t < 0) {
            return false;
        }

        $closestX = $start->x + $dx * $t;
        $closestY = $start->y + $dy * $t;
        $closestPosition = new Position($closestX, $closestY);
        $closestDistance = $closestPosition->calculateDistanceBetween($circle);

        return $closestDistance <= ($circle->radius + $fudge);
    }
}
