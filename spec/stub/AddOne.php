<?php

/**
 * @file
 * Contains Pipeware\Stub\AddOne
 */

namespace Pipeware\Stub;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AddOne
	implements MiddlewareInterface
{
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler ): ResponseInterface
	{
		$counter = $request->getAttribute('counter');

		return $handler->handle($request->withAttribute('counter', $counter + 1));
	}

}