<?php

/**
 * @file
 * Contains spec\Pipeware\Pipeline\ContainerizedSpec
 */

namespace spec\Pipeware\Pipeline;

use Aura\Di\Container;
use PhpSpec\ObjectBehavior;
use Pipeware\Pipeline\Basic;
use Pipeware\Pipeline\Containerized;
use Pipeware\Stage\RequestHandler;
use Pipeware\Stub\AddOne;
use Pipeware\Stub\RandomRequestHandler;
use Pipeware\Stub\TimesTwo;
use Psr\Container\ContainerInterface;

class ContainerizedSpec
	extends ObjectBehavior
{
	public function let($container)
	{
		$container->beADoubleOf(ContainerInterface::class);
		$this->beConstructedWith($container);
	}

	public function it_is_initializable()
	{
		$this->shouldHaveType(Containerized::class);
	}

	public function it_should_handle_different_stages()
	{
		$f = function () {
		};
		$p = $this->pipe($f)// callable
				  ->pipe(new Basic())// pipeline
				  ->pipe(new AddOne())// class stage
				  ->pipe('test') // container lookup
		;
		$p->shouldHaveType(Containerized::class);

		$this->shouldThrow(\InvalidArgumentException::class)
		  ->duringPipe(new \stdClass());
	}

	public function it_should_be_immutable()
	{
		$p1 = $this->pipe(new AddOne())
				   ->pipe(new TimesTwo());
		$p1->shouldHaveType(Containerized::class);
		$p1->shouldNotBe($this);

		$p2 = $p1->withReversedOrder();
		$p1->shouldHaveType(Containerized::class);
		$p2->shouldNotBe($p1);
	}

	public function it_should_be_iterable()
	{
		$a = new AddOne();
		$p = $this->pipe($a)->pipe($a);
		foreach ($p as $s) {
			$s->shouldYield($a);
		}

		$i = $p->getIterator();
		$i->shouldReturnAnInstanceOf(\Generator::class);
		foreach ($i as $s) {
			$s->shouldYield($a);
		}

	}

	public function it_should_return_the_stages()
	{
		$a = new AddOne();
		$p = $this->pipe($a)->pipe($a);

		$p->stages()->shouldBeSameStagesAs([$a, $a]);
	}

	public function it_should_look_up_strings_in_the_container($container)
	{
		$a = new AddOne();
		$container->has('test')->willReturn(true);
		$container->get('test')->willReturn($a);
		$this->beConstructedWith($container);

		$p = $this->pipe('test');
		$p->stages()->shouldBeSameStagesAs([$a]);
	}

	public function it_should_throw_runtime_exception_when_unable_to_resolve()
	{
		$p = $this->pipe('test');
		$p->shouldThrow(\RuntimeException::class)->duringStages();
	}

	public function it_should_reverse_the_stages()
	{
		$a1 = new AddOne();
		$t2 = new TimesTwo();
		$p1 = $this->pipe($a1)
				   ->pipe($t2);
		$p2 = $p1->withReversedOrder();
		$p2->stages()->shouldBeSameStagesAs([$t2, $a1]);
	}

	public function it_should_support_aura_di($aura)
	{
		$a = new AddOne();
		$aura->beADoubleOf(Container::class);
		$aura->has('test')->willReturn(false);
		$aura->newInstance('test')->willReturn($a);
		$this->beConstructedWith($aura);

		$p = $this->pipe('test');
		$p->stages()->shouldBeSameStagesAs([$a]);
	}

	public function it_should_replace_a_stage($container)
	{
		$a = new AddOne();
		$t = new TimesTwo();

		$container->has('two')->willReturn(true);
		$container->get('two')->willReturn($a);
		$this->beConstructedWith($container);

		$p = $this->pipe('test')->pipe($t)->replace('test', 'two');
		$p->stages()->shouldBeSameStagesAs([$a, $t]);
	}

	public function it_should_allow_requesthandlerinterfaces_to_pipe()
	{
		$r = new RandomRequestHandler();
		$p = $this->pipe($r);

		$p->stages()->shouldBeStages([new RequestHandler($r)]);
	}

	public function it_should_validate_stages_on_build($container)
	{
		$a = new AddOne();
		$t = new \stdClass();

		$container->has('test')->willReturn(true);
		$container->get('test')->willReturn($a);
		$container->has('two')->willReturn(true);
		$container->get('two')->willReturn($t);
		$this->beConstructedWith($container);

		$p = $this->pipe('test')->pipe('two');

		$p->shouldThrow(\RuntimeException::class)->duringStages();
	}
}