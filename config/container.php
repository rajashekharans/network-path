<?php

use NetworkPath\Command\NetworkPathCommand;
use NetworkPath\Service\NetworkPathService;
use NetworkPath\Collection\NetworkPathInfoCollection;
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
    return new NetworkPathCommand(
        $container['command.network.path.info.service']
    );
};

$container['command.network.path.info.service'] = function (Container $container) {
    return new NetworkPathService(
        $container['command.network.path.info.collection']
    );
};

$container['command.network.path.info.collection'] = function () {
    return new NetworkPathInfoCollection();
};
return $container;
