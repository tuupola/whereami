#  Common interface for wifi positioning services

[![Latest Version](https://img.shields.io/packagist/v/tuupola/whereami.svg?style=flat-square)](https://packagist.org/packages/tuupola/whereami)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/tuupola/whereami/master.svg?style=flat-square)](https://travis-ci.org/tuupola/whereami)
[![HHVM Status](https://img.shields.io/hhvm/tuupola/whereami.svg?style=flat-square)](http://hhvm.h4cc.de/package/tuupola/whereami)
[![Coverage](http://img.shields.io/codecov/c/github/tuupola/whereami.svg?style=flat-square)](https://codecov.io/github/tuupola/whereami)

## Install

Install the library using [Composer](https://getcomposer.org/). You will also need to choose a [HTTP client](http://docs.php-http.org/en/latest/clients.html) for example [php-http/curl-client](http://docs.php-http.org/en/latest/clients/curl-client.html).

``` bash
$ composer require tuupola/whereami:dev-master
$ composer require php-http/curl-client
```

If you do not install HTTP client you will get `No HTTPlug clients found` and `Puli Factory is not available` errors. Ignore the Puli part, you do not need it. Just install an HTTP client.

Finally if your project does not already have one, you must include a PSR-7 implementation. Good candidate is `zendframework/zend-diactoros`. Many frameworks such as [Slim](https://www.slimframework.com/) ands [Expressive](https://docs.zendframework.com/zend-expressive/) already include PSR-7 by default and this part is not needed.

``` bash
$ composer require zendframework/zend-diactoros
```

## Usage

To you use must provide wifi location service provider and a wifi network scanner. With macOS you can use `AirportScanner` which does not require any extra setup. With Linux based systems you can use `IwlistScanner` which might need root privileges to be run. Example below uses [Mozilla Location Service](https://location.services.mozilla.com/).

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

## Providers
### Combain
### Google
### Mozilla
### Unwired

## Scanners
### Airport
### Iwlist

## Adapters
### Corelocation
### LocateMe

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
