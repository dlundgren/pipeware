<?php

namespace Pipeware;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use SyberIsle\Pipeline\Pipeline;

class Containerized
	implements Pipeline
{
	/**
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * The list of middleware
	 *
	 * @var array
	 */
	private $stages;

	/**
	 * The list of resoved middleware
	 * @var array
	 */
	private $resolved;

	public function __construct($container, $middleware = [])
	{
		$this->container = $container;
		$this->stages    = $middleware;
	}

	/**
	 * Pushes the middleware on pipeline
	 *
	 * @param string|MiddlewareInterface $stage
	 * @return Pipeline|void
	 */
	public function pipe($stage)
	{
		if (!$stage instanceof MiddlewareInterface && !is_string($stage)) {
			throw new \InvalidArgumentException("Stage must implement " . MiddlewareInterface::class);
		}

		$this->stages[] = $stage;
	}

	/**
	 * Returns the list of stages
	 *
	 * This will resolve any string based middleware from the given container
	 *
	 * @return array
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	public function stages()
	{
		if (!$this->resolved) {
			$this->resolved = [];
			foreach ($this->stages as $stage) {
				$this->resolved[] = $this->build($stage);
			}
		}

		return $this->resolved;
	}

	/**
	 * Builds the stage as a
	 *
	 * @param $stage
	 * @return MiddlewareInterface
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	private function build($stage)
	{
		if ($stage instanceof MiddlewareInterface) {
			return $stage;
		}

		if ($this->container->has($stage)) {
			return $this->container->get($stage);
		}

		// Support aura/di
		if (method_exists($this->container, 'newInstance')) {
			return $this->container->newInstance($stage);
		}

		throw new \RuntimeException("Unable to resolve $stage");
	}
}