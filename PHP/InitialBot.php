<?php

require_once "hlt/autoload.php";

$game = new Game("Random");
Logging::log("Starting my Random bot!");

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
                $navigateCommand = $ship->navigate(
                    $ship->closestPointTo($planet),
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
