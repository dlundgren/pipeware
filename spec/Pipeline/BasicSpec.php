<?php

/**
 * @file
 * Contains spec\Pipeware\Pipeline\BasicSpec
 */

namespace spec\Pipeware\Pipeline;

use PhpSpec\ObjectBehavior;
use Pipeware\Pipeline\Basic;
use Pipeware\Stage\Lambda;
use Pipeware\Stub\AddOne;

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
}