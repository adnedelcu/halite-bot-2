<?php

require_once "hlt/autoload.php";

$game = new Game("Random");

while(true) {
    $gameMap = $game->updateMap();

    $commandQueue = [];

    /** @var Ship $ship */
    foreach ($gameMap->getMe()->allShips() as $ship) {
        if ($ship->dockingStatus != Ship::UNDOCKED) {
            continue;
        }

        foreach ($gameMap->allPlanets() as $planet) {
            if ($planet->isOwned()) {
                continue;
            }

            if ($ship->canDock($planet)) {
                $commandQueue[] = $ship->dock($planet);
            } else {
                $newPosition = $ship->closestPointTo($planet);
                Logging::log("Planet position (x: $planet->x, y: $planet->y)");
                Logging::log("New position (x: $newPosition->x, y: $newPosition->y)");
                $navigateCommand = $ship->navigate(
                    $newPosition,
                    $gameMap,
                    Ship::MAX_SPEED / 2,
                    true,
                    90,
                    1,
                    true,
                    false
                );

                if ($navigateCommand) {
                    $commandQueue[] = $navigateCommand;
                }
            }

            break;
        }
    }

    Game::sendCommandQueue($commandQueue);
}
