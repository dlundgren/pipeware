<?php

/**
 * @file
 * Contains spec\Pipeware\Pipeline\BasicSpec
 */

namespace spec\Pipeware\Pipeline;

use PhpSpec\ObjectBehavior;
use Pipeware\Pipeline\Basic;
use Pipeware\Stage\Lambda;
use Pipeware\Stage\RequestHandler;
use Pipeware\Stub\AddOne;
use Pipeware\Stub\RandomRequestHandler;
use Pipeware\Stub\TimesTwo;

class BasicSpec
	extends ObjectBehavior
{
	public function let()
	{
		$this->beAnInstanceOf(Basic::class);
	}

	public function it_is_initializable()
	{
		$this->shouldHaveType(Basic::class);
	}

	public function it_should_be_immutable()
	{
		$l = new Lambda(function() {});
		$b = new Basic();
		$a = new AddOne();

		$p = $this->pipe($l)
				  ->pipe($b)
				  ->pipe($a);
		$p->shouldHaveType(Basic::class);
		$p->shouldNotBe($this);
	}

	public function it_should_not_accept_strings()
	{
		$this->shouldThrow(\InvalidArgumentException::class)
			 ->during('pipe', ['test']);
	}

	public function it_should_be_iterable()
	{
		$a = new AddOne();
		$p = $this->pipe($a)->pipe($a);

		foreach ($p as $s) {
			$s->shouldYield($a);
		}

		$p->getIterator()->shouldReturnAnInstanceOf(\Generator::class);
	}

	public function it_should_return_stages()
	{
		$a = new AddOne();
		$p = $this->pipe($a)->pipe($a);
		$p->stages()->shouldBeSameStagesAs([$a, $a]);
	}

	public function it_should_replace_a_stage()
	{
		$a = new AddOne();
		$t = new TimesTwo();
		$p = $this->pipe($a)->pipe($t)->replace(AddOne::class, $t);
		$p->stages()->shouldBeSameStagesAs([$t, $t]);
	}

	public function it_should_allow_requesthandlerinterfaces_to_pipe($request)
	{
		$r = new RandomRequestHandler();
		$p = $this->pipe($r);

		$p->stages()->shouldBeStages([new RequestHandler($r)]);
	}
}