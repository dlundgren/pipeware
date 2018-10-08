<?php

/**
 * @file
 * Contains Pipeware\Pipeline\IsPipeline
 */

namespace Pipeware\Pipeline;

use Pipeware\Pipeline\Exception\InvalidMiddlewareArgument;
use Pipeware\Stage\Lambda;
use Pipeware\Stage\RequestHandler;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

trait IsPipeline
{
	/**
	 * Replaces the first occurrence of the stage in the pipeline
	 *
	 * If no replacement is made the original pipeline is returned
	 *
	 * @param string $needle The class name to replace
	 * @param        $replacement
	 * @return Pipeline
	 */
	public function replace($needle, $replacement): Pipeline
	{
		$stages = [];
		$found  = false;
		foreach ($this->stages as $stage) {
			if ($this->matches($stage, $needle)) {
				$stages[] = $replacement;
				$found    = true;
				continue;
			}

			$stages[] = $stage;
		}

		if ($found) {
			$pipeline         = clone $this;
			$pipeline->stages = $stages;

			return $pipeline;
		}

		unset($stages);

		return $this;
	}

	/**
	 * Placeholder since pipeline don't extend a common base
	 *
	 * @param $stage
	 * @param $needle
	 * @return bool
	 */
	protected function matches($stage, $needle): bool
	{
		throw new \RuntimeException("Please implement " . get_class($this) . '::matches');
	}

	/**
	 * Handles merging or converting the stage to a callback
	 *
	 * @param array                    $stages
	 * @param Pipeline|MiddlewareInterface|RequestHandlerInterface|string|callable $stage
	 */
	private function handleStage(&$stages, $stage)
	{
		if ($stage instanceof Pipeline) {
			$stages = array_merge($stages, $stage->stages());
		}
		elseif ($stage instanceof MiddlewareInterface || is_string($stage)) {
			$stages[] = $stage;
		}
		elseif ($stage instanceof RequestHandlerInterface) {
			$stages[] = new RequestHandler($stage);
		}
		elseif (is_callable($stage)) {
			$stages[] = new Lambda($stage);
		}
		else {
			throw new InvalidMiddlewareArgument(get_class($stage));
		}
	}
}