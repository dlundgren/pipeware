<?php

/**
 * @file
 * Contains Pipeware\Stage\RequestHandler
 */

namespace Pipeware\Stage;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class MatchUri
 *
 * Runs the given request handler instead of the one passed in
 *
 * @package Pipeware\Stage
 */
class RequestHandler
	implements MiddlewareInterface
{
	/**
	 * @var RequestHandlerInterface
	 */
	private $handler;

	public function __construct(RequestHandlerInterface $handler)
	{
		$this->handler = $handler;
	}

	/**
	 * {@inheritdoc}
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler = null): ResponseInterface
	{
		return $this->handler->handle($request);
	}
}