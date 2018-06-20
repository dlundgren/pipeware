<?php

/**
 * @file
 * Contains Pipeware\Stub\BeStagesMatcher
 */

namespace Pipeware\Stub;

use PhpSpec\Exception\Example\FailureException;
use PhpSpec\Matcher\Matcher;
use Pipeware\Stage\Lambda;

class BeStagesMatcher
	implements Matcher
{
	private $names = [
		'beStages',
		'beSameStagesAs'
	];

	public function supports($name, $subject, array $arguments)
	{
		return in_array($name, $this->names) && count($arguments) > 0;
	}

	public function positiveMatch($name, $subject, array $arguments)
	{
		foreach ($subject as $key => $item) {
			$ci = get_class($item);
			$cv = get_class($arguments[0][$key]);
			if (!($item instanceof $arguments[0][$key])) {
				throw new FailureException("Stages do not match. Expecting $cv, got $ci");
			}

			if ($name === 'beSameStagesAs' && $item !== $arguments[0][$key]) {
				throw new FailureException("Stages are not the same. Expecting $cv, got $ci: {$this->exactMatches}");
			}

			$pv = $this->getVariable($item);

			if ($pv && $pv->getValue($item) !== $pv->getValue($arguments[0][$key])) {
				throw new FailureException("Invalid stage callable");
			}
		}
	}

	public function negativeMatch($name, $subject, array $arguments)
	{
		// TODO: Implement negativeMatch() method.
	}

	public function getPriority()
	{
		return 0;
	}

	protected function getVariable($obj, $var = null)
	{
		static $refs = [];

		$class = get_class($obj);

		if ($class === Lambda::class) {
			$var = 'callable';
		}
		else {
			return null;
		}

		if (!isset($refs[$class])) {
			$r = new \ReflectionClass($class);
			$p = $r->getProperty($var);
			$p->setAccessible(true);
			$refs[$class] = $p;
		}

		return $refs[$class];
	}
}
