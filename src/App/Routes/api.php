<?php
declare(strict_types=1);

use Gimli\Router\Route;

Route::group('/api', function () {
		Route::get('/test', function () {
			echo 'Hello World!';
		});
	}
);