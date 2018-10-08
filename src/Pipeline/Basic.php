<?php

/**
 * @file
 * Contains Pipeware\Pipeline\Basic
 */

namespace Pipeware\Pipeline;

use Pipeware\Pipeline\Exception\InvalidMiddlewareArgument;
use Pipeware\Stage\Lambda;
use Pipeware\Pipeline\Pipeline as PipewareInterface;
use Pipeware\Stage\RequestHandler;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
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
	 * {@inheritdoc}
	 */
	public function withReversedOrder()
	{
		$pipeline         = clone $this;
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
	 * @param $stage
	 * @param $needle
	 * @return bool
	 */
	protected function matches($stage, $needle)
	{
		return $stage instanceof $needle;
	}
}