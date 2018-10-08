<?php

/**
 * @file
 * Contains spec\Pipeware\ProcessorSpec
 */

namespace spec\Pipeware;

use Pipeware\Pipeline\Containerized;
use Pipeware\Stub\RandomRequestHandler;
use Psr\Http\Message\ResponseFactoryInterface;
use PhpSpec\ObjectBehavior;
use Pipeware\Pipeline\Basic;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;

class ProcessorSpec extends ObjectBehavior
{
	public function let($factory)
	{
		$factory->beADoubleOf(ResponseFactoryInterface::class);
		$factory->createResponse(404)->willReturn(new Response(404));

		$this->beConstructedWith($factory);
	}

	public function it_should_process_the_pipeline($request)
	{
		$f = function ($r, $h) {
			return new Response();
		};
		$pipeline = (new Basic())->pipe($f);

		$request->beADoubleOf(ServerRequestInterface::class);
		$this->process($pipeline, $request)->shouldReturnAnInstanceOf(Response::class);
	}

	public function it_should_return_a_not_found_response($request)
	{
		$pipeline = new Basic();

		$request->beADoubleOf(ServerRequestInterface::class);
		$response = $this->process($pipeline, $request);
		$response->shouldReturnAnInstanceOf(Response::class);
		$response->getStatusCode()->shouldBeEqualTo(404);
	}
}