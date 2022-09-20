# Network Path Test

## Requirements
- To run locally, PHP 7 or above are required to be installed. 
- `composer` - the dependency manager is required, to install required packages

## Let's get this running - clone this Repo!
1. Open terminal
1. Clone the repo `git clone git@github.com:rajashekharans/network-path.git`
1. `cd network-path`
1. Run `composer install`, to install dependencies
1. To run the application, `php bin/console nw-path-test <filepath>`
1. To run tests, `vendor/bin/phpunit`

## Technical Overview
Network Path Test is symfony console command application. It uses pimple container for dependency injection.

Command takes csv file as a input, to get all the possible routes in the network between devices.  The network information is stored as adjacency list, where each node contains information about its neighbours.

DFS algorithm is used to traverse the network starting from the given node.



