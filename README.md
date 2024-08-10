## Gimli Skeleton Application

`composer create-project danc0/gimli-skeleton my-app`

This skeleton application sets up the directory structure and a few basic Hello World routes to get started. You will need to add your `tmp` directory for Latte and in dev give it `chmod 777` permissions.

You will also need to create your `/App/Core/config.ini` file if you decide to use that, if not remove the line from `index.php` that loads it.

Make sure to run `composer install` to install the dependencies inside the `src` directory and `npm install` to install the dependencies inside the `vue` directory.

There is a basic docker-compose.yml file included to get you started with a development environment. You can run `docker compose up -d` to start the containers and `docker compose stop` to stop them. The included Dockerfile includes PHP 8.3 with some commonly installed extensions and Redis.

Vue comes with [WaveUI](https://antoniandre.github.io/wave-ui/) ready to use for UI components.