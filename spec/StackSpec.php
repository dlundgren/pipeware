<?php

/**
 * @file
 * Contains spec\Pipeware\StackSpec
 */

namespace spec\Pipeware;

use Middlewares\Utils\Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use PhpSpec\ObjectBehavior;
use Pipeware\Pipeline\Basic;
use Pipeware\Processor;
use Pipeware\Stack;
use Pipeware\Stub\AddOne;
use Pipeware\Stub\TimesTwo;
use Prophecy\Argument\Token\AnyValueToken;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;

class StackSpec extends ObjectBehavior
{
	public function let($pipeline, $factory, $processor, $request, $response)
	{
		// this is silly, but required for coverage to work
		ini_set('error_reporting', E_ALL & !E_WARNING);

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
		$p = new Processor(Factory::getResponseFactory());

		$this->beConstructedWith($b, $p);

		$r = $this->handle($request);

		$r->shouldReturnAnInstanceOf(ResponseInterface::class);
		$r->getStatusCode()->shouldBe(418);
	}

	public function it_should_reverse_the_stack($request)
	{
		$b = (new Basic())->pipe(function () { return new Response(418); })
						  ->pipe(function () { return new Response(403);});
		$p = new Processor(Factory::getResponseFactory());

		$this->beConstructedWith($b, $p);

		$this->reverse()->shouldReturn($this);

		$r = $this->handle($request);
		$r->shouldReturnAnInstanceOf(ResponseInterface::class);
		$r->getStatusCode()->shouldBe(403);
	}

	public function it_should_replace_stages_in_stack($request)
	{
		$b = (new Basic())->pipe(new AddOne())
						  ->pipe(new TimesTwo());
		$p = new Processor(Factory::getResponseFactory());
		$counter = new \stdClass();
		$counter->count = 1;
		$request->getAttribute('counter')->willReturn($counter);

		$this->beConstructedWith($b, $p);
		$this->replace(AddOne::class, new TimesTwo());

		$r = $this->handle($request);
		$r->getHeader('counter')->shouldBe([4]);
	}
}