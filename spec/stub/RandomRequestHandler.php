<?php

/**
 * @file
 * Contains Pipeware\Stub\RandomRequestHandler
 */

namespace Pipeware\Stub;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Http\Response;

class RandomRequestHandler
	implements RequestHandlerInterface
{
	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		return new Response();
	}
}