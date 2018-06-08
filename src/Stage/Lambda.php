<?php

namespace Pipeware\Stage;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Lambda
	extends Match
{
	/**
	 * @var callable
	 */
	private $callable;

	public function __construct(callable $callable)
	{
		$this->callable = $callable;
	}

	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		return ($this->callable)($request, $handler);
	}

}