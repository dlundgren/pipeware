# Pipeware library

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]

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

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed.

## Credits

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

- [David Lundgren](https://github.com/dlundgren)
- [All Contributors](https://github.com/dlundgren/pipeware/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/dlundgren/pipeware.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/dlundgren/pipeware.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/dlundgren/pipeware
[link-downloads]: https://packagist.org/packages/dlundgren/pipeware
[link-author]: https://github.com/:author_username
[link-contributors]: ../../contributors