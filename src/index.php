<?php
declare(strict_types=1);
require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Cache;
use Gimli\Application;
use App\Core\Config;
use function Gimli\Injector\resolve_fresh;

define('APP_ROOT', __DIR__);

$App = Application::create(APP_ROOT, $_SERVER)
	->setConfig(resolve_fresh(Config::class, ['config' => parse_ini_file(APP_ROOT . '/App/Core/config.ini', true)]))
	->enableLatte();

if (class_exists('Redis')) {
	$App->Injector->bind(Cache::class, Cache::getCache($App->Config->cache));
}

$App->run();