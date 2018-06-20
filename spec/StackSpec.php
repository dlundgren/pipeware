<?php

/**
 * @file
 * Contains spec\Pipeware\StackSpec
 */

namespace spec\Pipeware;

use Interop\Http\Factory\ResponseFactoryInterface;
use Middlewares\Utils\Factory\ResponseFactory;
use PhpSpec\ObjectBehavior;
use Pipeware\Pipeline\Basic;
use Pipeware\Processor;
use Pipeware\Stack;
use Prophecy\Argument\Token\AnyValueToken;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;

class StackSpec extends ObjectBehavior
{
	public function let($pipeline, $factory, $processor, $request, $response)
	{
		$pipeline->beADoubleOf(Basic::class);
		$pipeline->pipe(new AnyValueToken())->willReturn($pipeline);
		$response->beADoubleOf(ResponseInterface::class);
		$request->beADoubleOf(ServerRequestInterface::class);
		$factory->beADoubleOf(ResponseFactoryInterface::class);
		$factory->createResponse(404)->willReturn($response);
		$processor->beADoubleOf(Processor::class);

		$this->beConstructedWith($pipeline, $processor);
	}

	public function it_should_be_initializable()
	{
		$this->shouldHaveType(Stack::class);
	}

	public function it_should_add_middleware()
	{
		$this->push(function ($r, $h) {})->shouldReturn($this);
	}

	public function it_should_return_a_response($request)
	{
		$b = (new Basic())->pipe(function () { return new Response(418); })
						  ->pipe(function () { return new Response(403);});
		$p = new Processor(new ResponseFactory());

		$this->beConstructedWith($b, $p);

		$r = $this->handle($request);

		$r->shouldReturnAnInstanceOf(ResponseInterface::class);
		$r->getStatusCode()->shouldBe(418);
	}

	public function it_should_reverse_the_stack($request)
	{
		$b = (new Basic())->pipe(function () { return new Response(418); })
						  ->pipe(function () { return new Response(403);});
		$p = new Processor(new ResponseFactory());

		$this->beConstructedWith($b, $p);

		$this->reverse()->shouldReturn($this);

		$r = $this->handle($request);
		$r->shouldReturnAnInstanceOf(ResponseInterface::class);
		$r->getStatusCode()->shouldBe(403);
	}
}