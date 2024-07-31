<?php
declare(strict_types=1);

use App\Jobs\Hello_World;
use Gimli\Router\Route;

Route::cli('hello-world', Hello_World::class);