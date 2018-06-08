# Pipeware library

This is an example library for how to treat Middleware as a pipeline.

```php
$stack = new \Pipeware\Pipe(
	new \Pipeware\Containerized($container), // any psr-11 compatible container
	new \Pipeware\Processor()
);

// append your responder
$stack->append(new Responder());

// append your error handler
$stack->append(new ErrorHandler());


// have the stack handle your request
$response = $stack->handle($request);

// do something with your response if needed

```