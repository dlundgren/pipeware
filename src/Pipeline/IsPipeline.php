<?php

/**
 * @file
 * Contains Pipeware\Pipeline\IsPipeline
 */

namespace Pipeware\Pipeline;

trait IsPipeline
{
	/**
	 * Replaces the first occurence of the stage in the pipeline
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

		return $this;
	}

	protected function matches($stage, $needle): bool
	{
		throw new \RuntimeException("Please implement " . get_class($this) . '::matches');
	}
}