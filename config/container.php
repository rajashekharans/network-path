<?php

use NetworkPath\Command\NetworkPathCommand;
use Pimple\Container;
use Symfony\Component\Console\Application;

$container = new Container();

$container['application'] = function (Container $container) {
    $app = new Application('Network Path');

    $app->addCommands([
        $container['command.network.path'],
    ]);

    return $app;
};

$container['command.network.path'] = function (Container $container) {
    return new NetworkPathCommand();
};

return $container;
