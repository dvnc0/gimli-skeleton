<?php
declare(strict_types=1);

namespace App\Core;

use Gimli\Environment\Config as Config_Base;

class Config extends Config_Base
{
	/**
	 * Configuration settings
	 * 
	 * @var array{
	 *         is_live: bool,
	 *         is_dev: bool,
	 *         is_staging: bool,
	 *         is_cli: bool,
	 *         is_unit_test: bool,
	 *         database: array{
	 *             driver: string,
	 *             host: string,
	 *             database: string,
	 *             username: string,
	 *             password: string,
	 *             port: int
	 *         },
	 *         autoload_routes: bool,
	 *         route_directory: string,
	 *         enable_latte: bool,
	 *         template_base_dir: string,
	 *         cache: array{
	 *             host: string,
	 *             port: int
	 *         }
	 * } $config
	 */
	protected array $config = [
		'is_live' => FALSE,
		'is_dev' => TRUE,
		'is_staging' => FALSE,
		'is_cli' => FALSE,
		'is_unit_test' => FALSE,
		'database' => [
			'driver' => 'mysql',
			'host' => '',
			'database' => '',
			'username' => '',
			'password' => '',
			'port' => 3306,
		],
		'autoload_routes' => TRUE,
		'route_directory' => '/App/Routes/',
		'enable_latte' => TRUE,
		'template_base_dir' => '/App/views/',
		'template_temp_dir' => '/tmp',
		'cache' => [
			'host' => '',
			'port' => 6379
		],
		'use_web_route_file' => FALSE,
	];
}