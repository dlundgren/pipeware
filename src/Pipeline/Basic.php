<?php

/**
 * @file
 * Contains Pipeware\Pipeline\Basic
 */

namespace Pipeware\Pipeline;

use Pipeware\Stage\Lambda;
use Pipeware\Pipeline\Pipeline as PipewareInterface;
use Psr\Http\Server\MiddlewareInterface;
use SyberIsle\Pipeline\Pipeline;

/**
 * A basic pipeline implementation
 *
 * @package Pipeware\Pipeline
 */
class Basic
	extends Pipeline\Simple
	implements PipewareInterface
{
	use IsPipeline;

	/**
	 * @param $stage
	 * @param $needle
	 * @return bool
	 */
	protected function matches($stage, $needle)
	{
		return $stage instanceof $needle;
	}

	/**
	 * {@inheritdoc}
	 */
	public function withReversedOrder()
	{
		$pipeline = clone $this;
		$pipeline->stages = array_reverse($this->stages);

		return $pipeline;
	}

	/**
	 * Returns the generator
	 *
	 * @return \Generator
	 */
	public function getIterator()
	{
		foreach ($this->stages as $stage) {
			yield $stage;
		}
	}

	/**
	 * Handles merging or converting the stage to a callback
	 *
	 * @param array                                 $stages
	 * @param PipewareInterface|MiddlewareInterface|callable $stage
	 */
	protected function handleStage(&$stages, $stage)
	{
		if ($stage instanceof PipewareInterface) {
			$stages = array_merge($stages, $stage->stages());
		}
		elseif ($stage instanceof MiddlewareInterface) {
			$stages[] = $stage;
		}
		elseif (is_callable($stage)) {
			$stages[] = new Lambda($stage);
		}
		else {
			$data = is_object($stage) ? get_class($stage) : json_encode($stage);
			throw new \InvalidArgumentException("Middleware must be an instance of MiddlewareInterface, Pipeline, or a callable: {$data}");
		}
	}
}