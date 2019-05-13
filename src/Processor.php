<?php

/**
 * @file
 * Contains Pipeware\Processor
 */

namespace Pipeware;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SyberIsle\Pipeline;

/**
 * PSR-15 pipeline processor
 *
 * @package Pipeware
 */
class Processor
	implements Pipeline\Processor, RequestHandlerInterface
{
	/**
	 * @var Pipeline\Pipeline
	 */
	protected $pipeline;

	/**
	 * @var \Generator
	 */
	protected $stages;

	/**
	 * @var ResponseFactoryInterface
	 */
	protected $responseFactory;

	public function __construct(ResponseFactoryInterface $responseFactory)
	{
		$this->responseFactory = $responseFactory;
	}

	/**
	 * Handle the request
	 *
	 * @param ServerRequestInterface $request
	 * @return ResponseInterface
	 */
	public function handle(ServerRequestInterface $request): ResponseInterface
	{
		if ($this->stages->valid()) {
			$stage = $this->stages->current();
			$this->stages->next();

			return $stage->process($request, $this);
		}

		return $this->responseFactory->createResponse(404);
	}

	/**
	 * Process the payload
	 *
	 * Returns this with the stages set
	 *
	 * @param Pipeline\Pipeline $pipeline
	 * @param                   $payload
	 * @return mixed|ResponseInterface
	 */
	public function process(Pipeline\Pipeline $pipeline, $payload)
	{
		$runner           = clone($this);
		$runner->stages   = $pipeline->getIterator();
		$runner->pipeline = $pipeline;

		return $runner->handle($payload);
	}

	/**
	 * Clones the stages from the pipeline to be used from the clone
	 */
	public function __clone()
	{
		if (isset($this->pipeline)) {
			$index        = $this->stages->key();
			$this->stages = $this->pipeline->getIterator();
			while ($this->stages->key() != $index) {
				$this->stages->next();
			}
		}
	}
}
