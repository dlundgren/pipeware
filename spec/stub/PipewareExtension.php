<?php

/**
 * @file
 * Contains Pipeware\Stub\PipewareExtension
 */

namespace Pipeware\Stub;

use PhpSpec\Extension;
use PhpSpec\ServiceContainer;

class PipewareExtension
	implements Extension
{
	public function load(ServiceContainer $container, array $params)
	{
		$container->define(
			'custom.matchers.be_stages',
			function ($c) {
				return new BeStagesMatcher();
			},
			['matchers']
		);
	}

}