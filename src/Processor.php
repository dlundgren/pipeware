<?php

namespace Pipeware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SyberIsle\Pipeline;

/**
 * PSR-15 Middleware handler
 *
 * Class MiddlewareProcessor
 *
 * @package Pipeware
 */
class Processor
	implements Pipeline\Processor
{
	public function process(Pipeline\Pipeline $pipeline, $payload)
	{
		$stages = $pipeline->stages();
		if (empty($stages)) {
			throw new \InvalidArgumentException("No middleware available");
		}

		// initial payload is the request, and we want responses
		return (new class($stages)
			implements RequestHandlerInterface
		{
			private $stages;

			public function __construct($stages)
			{
				$this->stages = $stages;
			}

			public function handle(ServerRequestInterface $request): ResponseInterface
			{
				$stage = array_pop($this->stages);

				return $stage->process($request, empty($this->stages) ? null : $this);
			}
		})->handle($payload);
	}
}
