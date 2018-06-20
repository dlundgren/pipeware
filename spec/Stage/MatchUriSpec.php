<?php

/**
 * @file
 * Contains spec\Pipeware\Stage\MatchUriSpec
 */

namespace spec\Pipeware\Stage;

use PhpSpec\ObjectBehavior;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Http\Response;

class MatchUriSpec
	extends ObjectBehavior
{
	public function let($uri, $request)
	{
		$uri->beADoubleOf(UriInterface::class);
		$uri->getPath()->willReturn('/test');
		$request->beADoubleOf(ServerRequestInterface::class);
		$request->getUri()->willReturn($uri);
	}
	public function it_should_match($classHandler, $requestHandler, $request)
	{
		$classHandler->beADoubleOf(RequestHandlerInterface::class);

		$requestHandler->beADoubleOf(RequestHandlerInterface::class);
		$requestHandler->handle($request)->willReturn(new Response());

		$this->beConstructedWith($classHandler, '/test');

		$r = $this->process($request, $requestHandler);
		$r->shouldBeAnInstanceOf(ResponseInterface::class);
		$r->getStatusCode()->shouldBe(200);
	}

	public function it_should_not_match($classHandler, $requestHandler, $request)
	{
		$classHandler->beADoubleOf(RequestHandlerInterface::class);
		$classHandler->handle($request)->willReturn(new Response(404));

		$requestHandler->beADoubleOf(RequestHandlerInterface::class);

		$this->beConstructedWith($classHandler, '/no-test');

		$r = $this->process($request, $requestHandler);
		$r->shouldBeAnInstanceOf(ResponseInterface::class);
		$r->getStatusCode()->shouldBe(404);
	}
}
