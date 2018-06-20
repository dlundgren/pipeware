<?php

/**
 * @file
 * Contains Pipeware\Stage\Lambda
 */

namespace Pipeware\Stage;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Callback to represent a callable as a PSR-15 MiddlewareInterface
 *
 * @package Pipeware\Stage
 */
class Lambda
	implements MiddlewareInterface
{
	/**
	 * @var callable
	 */
	private $callable;

	public function __construct(callable $callable)
	{
		$this->callable = $callable;
	}

	/**
	 * {@inheritdoc}
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		return ($this->callable)($request, $handler);
	}

}