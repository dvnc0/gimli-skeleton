<?php
declare(strict_types=1);
require_once __DIR__ . '/vendor/autoload.php';

use Gimli\Application;
use App\Core\Config;
use App\Core\Cache;

define('APP_ROOT', __DIR__);

$App = Application::create(APP_ROOT, $_SERVER);
$App->Config = $App->Injector->resolveFresh(Config::class, ['config' => parse_ini_file(APP_ROOT . '/App/Core/config.ini', true)]);
if (class_exists('Redis')) {
	$App->Injector->register(Cache::class, Cache::getCache($App->Config->cache));
}

$App->run();