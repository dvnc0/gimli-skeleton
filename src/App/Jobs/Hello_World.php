<?php
declare(strict_types=1);

namespace App\Jobs;

use Gimli\Http\Response;

class Hello_World
{
	/**
	 * Constructor
	 * 
	 */
	public function __construct(
	) {}

	/**
	 * Invoke
	 * 
	 * @param Response $Response
	 * @param string $subcommand
	 * @param array $options
	 * @param array $flags
	 * 
	 * @return Response
	 */
	public function __invoke(Response $Response, string $subcommand, array $options, array $flags): Response {
		return $Response->setResponse('Hello, World!');
	}
}