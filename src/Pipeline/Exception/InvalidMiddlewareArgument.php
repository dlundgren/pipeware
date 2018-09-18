<?php

/**
 * @file
 * Contains Pipeware\Pipeline\Exception\InvalidMiddlewareArgument
 */

namespace Pipeware\Pipeline\Exception;

use Throwable;

class InvalidMiddlewareArgument
	extends \InvalidArgumentException
{
	public function __construct(string $message = "", int $code = 0, Throwable $previous = null)
	{
		$message = "Middleware must be an instance of MiddlewareInterface, Pipeline, or a callable: {$message}";
		parent::__construct($message, $code, $previous);
	}
}