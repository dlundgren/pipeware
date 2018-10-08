<?php

/**
 * @file
 * Contains Pipeware\Pipeline\Containerized
 */

namespace Pipeware\Pipeline;

use Pipeware\Pipeline\Pipeline as PipewareInterface;
use Pipeware\Stage\RequestHandler;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SyberIsle\Pipeline\Pipeline;

/**
 * Pipeline implementation that uses a PSR-11 Container to instantiate the middleware stack
 *
 * @package Pipeware\Pipeline
 */
class Containerized
	implements PipewareInterface
{
	use IsPipeline {
		IsPipeline::handleStage as traitHandleStage;
	}

	/**
	 * @var ContainerInterface
	 */
	protected $container;

	/**
	 * The list of middleware
	 *
	 * @var array
	 */
	protected $stages = [];

	/**
	 * The list of resolved middleware
	 *
	 * @var array
	 */
	protected $resolved;

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
	 * Builds the stage as a
	 *
	 * @param $stage
	 * @return MiddlewareInterface
	 * @throws \Psr\Container\ContainerExceptionInterface
	 * @throws \Psr\Container\NotFoundExceptionInterface
	 */
	protected function build($stage)
	{
		if ($stage instanceof MiddlewareInterface) {
			return $stage;
		}

		if ($this->container->has($stage)) {
			$stage = $this->container->get($stage);
			if ($stage instanceof RequestHandlerInterface) {
				return new RequestHandler($stage);
			}

			if ($stage instanceof MiddlewareInterface) {
				return $stage;
			}

			throw new \RuntimeException("Stage is not a valid " . MiddlewareInterface::class);
		}

		// Support aura/di
		if (method_exists($this->container, 'newInstance')) {
			return $this->container->newInstance($stage);
		}

		throw new \RuntimeException("Unable to resolve $stage");
	}

	/**
	 * {@inheritdoc}
	 */
	protected function handleStage(&$stages, $stage)
	{
		if (is_string($stage)) {
			$stages[] = $stage;
		}
		else {
			$this->traitHandleStage($stages, $stage);
		}
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
}