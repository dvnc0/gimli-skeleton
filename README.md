## Gimli Skeleton Application

`composer create-project danc0/gimli-skeleton my-app`

This skeleton application sets up the directory structure and a few basic Hello World routes to get started.

Make sure to run `composer install` to install the dependencies inside the `src` directory and `npm install` to install the dependencies inside the `vue` directory.

There is a basic docker-compose.yml file included to get you started with a development environment. You can run `docker compose up -d` to start the containers and `docker compose down` to stop them. The included Dockerfile includes PHP 8.3 with some commonly installed extensions and Redis.