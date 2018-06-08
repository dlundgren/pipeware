<?php

namespace Pipeware\Stage;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Match
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
		$this->handler = $match;
		$this->match   = $match;
	}

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		if ($request->getUri()->getPath() !== $this->match) {
			return ($handler)($request);
		}

		return ($this->handler)($request, $handler);
	}

}