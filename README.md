#  Common interface for wifi positioning services

[![Latest Version](https://img.shields.io/packagist/v/tuupola/whereami.svg?style=flat-square)](https://packagist.org/packages/tuupola/whereami)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/tuupola/whereami/master.svg?style=flat-square)](https://travis-ci.org/tuupola/whereami)
[![HHVM Status](https://img.shields.io/hhvm/tuupola/whereami.svg?style=flat-square)](http://hhvm.h4cc.de/package/tuupola/whereami)
[![Coverage](http://img.shields.io/codecov/c/github/tuupola/whereami.svg?style=flat-square)](https://codecov.io/github/tuupola/whereami)

## Install

Install using [Composer](https://getcomposer.org/).

``` bash
$ composer require tuupola/whereami:dev-master
```

## Usage

```php
require __DIR__ . "/vendor/autoload.php";

$provider = new MozillaProvider("your-api-key-here");
$scanner = new AirportScanner;
$locator = new Whereami($provider, $scanner);

$location = $locator->whereami();

/*
Array
(
    [latitude] => 1.355989
    [longitude] => 103.992365
    [accuracy] =>  65
)
*/
```


## Testing

You can run tests either manually...

``` bash
$ composer test
```
... or automatically on every code change. This requires [entr](http://entrproject.org/) to work:

``` bash
$ composer watch
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email tuupola@appelsiini.net instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
