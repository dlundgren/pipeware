<?php

/**
 * @file
 * Contains Pipeware\Pipeline\Containerized
 */

namespace Pipeware\Pipeline;

use Pipeware\Pipeline\Exception\InvalidMiddlewareArgument;
use Pipeware\Stage\Lambda;
use Pipeware\Pipeline\Pipeline as PipewareInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use SyberIsle\Pipeline\Pipeline;

/**
 * Pipeline implementation that uses a PSR-11 Container to instantiate the middleware stack
 *
 * @package Pipeware\Pipeline
 */
class Containerized
	implements PipewareInterface
{
	use IsPipeline;

	/**
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * The list of middleware
	 *
	 * @var array
	 */
	private $stages = [];

	/**
	 * The list of resolved middleware
	 *
	 * @var array
	 */
	private $resolved;

	public function __construct($container, $middleware = [])
	{
		$this->container = $container;
		foreach ((array)$middleware as $stage) {
			$this->handleStage($this->stages, $stage);
		}
	}

	/**
	 * Pushes the middleware on pipeline
	 *
	 * @param string|MiddlewareInterface $stage
	 * @return Pipeline
	 */
	public function pipe($stage)
	{
		$pipeline = new self($this->container, $this->stages);
		$this->handleStage($pipeline->stages, $stage);

		return $pipeline;
	}

	/**
	 * @param $stage
	 * @param $needle
	 * @return bool
	 */
	protected function matches($stage, $needle)
	{
		return $stage == $needle;
	}

	/**
	 * Reverses the order of the stages
	 *
	 * @return Containerized
	 */
	public function withReversedOrder(): Containerized
	{
		return new self($this->container, array_reverse($this->stages));
	}

	/**
	 * Returns the iterator
	 *
	 * @return \Generator|\Traversable
	 */
	public function getIterator()
	{
		$this->resolve();
		foreach ($this->resolved as &$stage) {
			yield $stage;
		}
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
		$this->resolve();

		return $this->resolved;
	}

	/**
	 * Resolves all stages to middleware
	 */
	protected function resolve()
	{
		if ($this->resolved) {
			return;
		}

		$this->resolved = [];
		foreach ($this->stages as $stage) {
			$this->resolved[] = $this->build($stage);
		}
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

	/**
	 * Handles merging or converting the stage to a callback
	 *
	 * @param array                    $stages
	 * @param Pipeline|string|callable $stage
	 */
	private function handleStage(&$stages, $stage)
	{
		if ($stage instanceof Pipeline) {
			$stages = array_merge($stages, $stage->stages());
		}
		elseif ($stage instanceof MiddlewareInterface || is_string($stage)) {
			$stages[] = $stage;
		}
		elseif (is_callable($stage)) {
			$stages[] = new Lambda($stage);
		}
		else {
			throw new InvalidMiddlewareArgument(get_class($stage));
		}
	}
}