# Pipeware library

This is an example library for how to treat Middleware as a pipeline.

This uses the [SyberIsle Pipeline](https://packagist.org/packages/syberisle/pipeline) as it's pipeline implementation.

## Usage

```php
$stack = new \Pipeware\Stack(
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

## Credits

- [David Lundgren](https://github.com/dlundgren)
- [All Contributors](https://github.com/dlundgren/pipeware/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.