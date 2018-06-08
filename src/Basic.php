<?php

namespace Pipeware;

use Psr\Http\Server\MiddlewareInterface;
use SyberIsle\Pipeline\Pipeline as PipelineInterface;

/**
 * Class Base
 *
 * The base
 * @package Phlim\Middleware
 */
class Basic
	implements PipelineInterface
{
	/**
	 * The list of middleware
	 *
	 * @var array
	 */
	private $stages;

	public function __construct($middleware = [])
	{
		$this->stages = $middleware;
	}

	public function pipe($middleware)
	{
		if (! $middleware instanceof MiddlewareInterface) {
			throw new \InvalidArgumentException("Stage must implement " . MiddlewareInterface::class);
		}

		$this->stages[] = $middleware;
	}

	public function stages()
	{
		return $this->stages;
	}
}