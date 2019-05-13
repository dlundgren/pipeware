<?php

/**
 * @file
 * Contains spec\Pipeware\ProcessorSpec
 */

namespace spec\Pipeware;

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

	public function it_should_be_able_to_be_cloned($request)
	{
		$pipeline = new Basic;
		$call = [];
		$f1 = function ($r, $h) use (&$call){
			$call[] = 'f1';
			return $h->handle($r);
		};
		$f2 = function ($r, $h) use (&$call) {
			do {
				$response = (clone $h)->handle($r);
				$call[] = 'f2';
			} while ($response->getStatusCode() == 404);

			return $response;
		};
		$f3 = function ($r, $h) use (&$call) {
			if (in_array('f3', $call)) {
				$call[] = 'f3>4';
				return $h->handle($r);
			}

			$call[] = 'f3';
			return new Response(404);
		};
		$f4 = function ($r, $h) use (&$call) {
			$call[] = 'f4';
			return new Response(420);
		};

		$request->beADoubleOf(ServerRequestInterface::class);
		$response = $this->process(
			$pipeline->pipe($f1)->pipe($f2)->pipe($f3)->pipe($f4),
			$request
		);
		$response->shouldReturnAnInstanceOf(Response::class);
		$response->getStatusCode()->shouldBeEqualTo(420);
	}
}