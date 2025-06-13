<?php
declare(strict_types=1);
require_once __DIR__ . '/vendor/autoload.php';

use App\Core\Cache;
use Gimli\Application;
use App\Core\Config;
use Gimli\Application_Registry;

use function Gimli\Injector\resolve_fresh;

define('APP_ROOT', __DIR__);

$App = Application::create(APP_ROOT, $_SERVER);
$App->setConfig(resolve_fresh(Config::class, ['config' => parse_ini_file(APP_ROOT . '/App/Core/config.ini', true)], $App))
	->enableLatte();

$App->Injector->bind(Cache::class, fn() => new Cache(['host' => APP_ROOT . '/App/tmp/cache.sqlite']));

Application_Registry::set($App);
$App->run();