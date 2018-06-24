<?php

/**
 * @file
 * Contains Pipeware\Pipeline\Pipeline
 */

namespace Pipeware\Pipeline;

/**
 * Pipeline interface that specifies Pipeware's pipelines, which add withReversedOrder
 *
 * @package Pipeware\Pipeline
 */
interface Pipeline
	extends \SyberIsle\Pipeline\Pipeline
{
	/**
	 * Returns a new instance with the stages in reverse order
	 *
	 * @return mixed
	 */
	public function withReversedOrder();

	/**
	 * Replaces the stage in the pipeline with the given instance
	 *
	 * @param $needle
	 * @param $replacement
	 * @return mixed
	 */
	public function replace($needle, $replacement);
}
