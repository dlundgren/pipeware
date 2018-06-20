<?php

/**
 * @file
 * Contains Pipeware\Stack
 */

namespace Pipeware;

use Pipeware\Pipeline\Pipeline;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SyberIsle\Pipeline\Processor;

/**
 * Generic Middleware stack
 *
 * @package Pipeware
 */
class Stack
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
	 * Reverses the direction of the pipeline
	 */
	public function reverse()
	{
		$this->pipeline = $this->pipeline->withReversedOrder();

		return $this;
	}

	/**
	 * Pushes middleware on the stack
	 *
	 * @param $middleware
	 * @return Stack
	 */
	public function push($middleware)
	{
		$this->pipeline = $this->pipeline->pipe($middleware);

		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		return $this->processor->process($this->pipeline, $request);
	}
}