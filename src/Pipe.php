<?php

namespace Pipeware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SyberIsle\Pipeline\Pipeline;
use SyberIsle\Pipeline\Processor;

/**
 * Class Pipe
 *
 * This is a generic middleware stack.
 *
 * @package Pipeware
 */
class Pipe
	implements RequestHandlerInterface
{
	/**
	 * @var Pipeline
	 */
	protected $pipeline;

	/**
	 * @var Processor
	 */
	protected $processor;

	/**
	 * Stack constructor.
	 *
	 * @param Pipeline  $pipeline
	 * @param Processor $processor
	 */

	public function __construct(Pipeline $pipeline, Processor $processor)
	{
		$this->pipeline  = $pipeline;
		$this->processor = $processor;
	}

	/**
	 * Appends middleware to the stack
	 *
	 * @param $middleware
	 */
	public function append(MiddlewareInterface $middleware)
	{
		// decorate the callable to be a middleware
		if (is_callable($middleware)) {
			$middleware = new Stage\Lambda($middleware);
		}

		$this->pipeline->pipe($middleware);
	}

	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		return $this->processor->process($this->pipeline, $request);
	}
}