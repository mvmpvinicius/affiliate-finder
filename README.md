# AFFILIATE FINDER

## Installation

1. Git clone the project
2. Run "composer install" to install all its dependencies
3. Copy .env.example to a new file called .env and set the following: DUBLIN_OFFICE_LATITUDE, DUBLIN_OFFICE_LONGITUDE, MAX_RANGE_DISTANCE
4. Run "php artisan key:generate" in the console
5. Run the project "php artisan serve" in the root directory to run locally your project or use docker/laradock to spin up the project containers.

## FAQ

1. This project must be ran alongside with affiliate-finder-frontend.
2. Certify each address modifying config files so frontend can comunicate with backend.
