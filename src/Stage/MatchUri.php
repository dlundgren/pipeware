<?php

/**
 * @file
 * Contains Pipeware\Stage\MatchUri
 */

namespace Pipeware\Stage;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Class MatchUri
 *
 * Runs an alternate handler if the URI matches
 *
 * @package Pipeware\Stage
 */
class MatchUri
	implements MiddlewareInterface
{
	/**
	 * @var RequestHandlerInterface
	 */
	private $handler;

	/**
	 * @var string
	 */
	private $match;

	public function __construct(RequestHandlerInterface $handler, $match)
	{
		$this->handler = $handler;
		$this->match   = $match;
	}

	/**
	 * {@inheritdoc}
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		return $request->getUri()->getPath() === $this->match
			? $handler->handle($request)
			: $this->handler->handle($request);
	}
}