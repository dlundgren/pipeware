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
}
