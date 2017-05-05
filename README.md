#  Whereami

[![Latest Version](https://img.shields.io/packagist/v/tuupola/whereami.svg?style=flat-square)](https://packagist.org/packages/tuupola/whereami)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/tuupola/whereami/master.svg?style=flat-square)](https://travis-ci.org/tuupola/whereami)
[![HHVM Status](https://img.shields.io/hhvm/tuupola/whereami.svg?style=flat-square)](http://hhvm.h4cc.de/package/tuupola/whereami)
[![Coverage](http://img.shields.io/codecov/c/github/tuupola/whereami.svg?style=flat-square)](https://codecov.io/github/tuupola/whereami)

Common interface for wifi positioning services, inspired by [whoami](https://linux.die.net/man/1/whoami).

![Whereami](http://www.appelsiini.net/img/whereami-headline-1400-2.png)


## Install

Install the library using [Composer](https://getcomposer.org/). You will also need to choose a [HTTP client](http://docs.php-http.org/en/latest/clients.html) for example [php-http/curl-client](http://docs.php-http.org/en/latest/clients/curl-client.html).

``` bash
$ composer require tuupola/whereami
$ composer require php-http/curl-client
```

If you do not install HTTP client you will get `No HTTPlug clients found` and `Puli Factory is not available` errors. Ignore the Puli part, you do not need it. Just install an HTTP client.

Finally if your project does not already have one, you must include a PSR-7 implementation. Good candidate is `zendframework/zend-diactoros`. Many frameworks such as [Slim](https://www.slimframework.com/) and [Expressive](https://docs.zendframework.com/zend-expressive/) already include PSR-7 by default and this part is not needed.

``` bash
$ composer require zendframework/zend-diactoros
```

## Usage
### Current location

To find current computers location you must provide wifi location service provider and optionally a wifi network scanner. With macOS you can use `AirportScanner` which does not require any extra setup. With Linux based systems you can use `IwlistScanner` which needs root privileges to be run. Example below uses [Mozilla Location Service](https://location.services.mozilla.com/).

```php
require __DIR__ . "/vendor/autoload.php";

use Whereami\Provider\MozillaProvider;
use Whereami\Scanner\AirportScanner;
use Whereami\Whereami;

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

Pro tip! Like mentioned above providing scanner is optional. If scanner is not provided system will try to autodetect it. This way same code should work in both macOS and *NIX systems.

```php
require __DIR__ . "/vendor/autoload.php";

use Whereami\Provider\MozillaProvider;
use Whereami\Whereami;

$provider = new MozillaProvider("your-api-key-here");
$locator = new Whereami($provider);

$location = $locator->whereami();
```

### IP Address Fallback

All providers are able to ip address as geolocation fallback. It is disabled by default but it can be enabled with the options array. Note that this will use public facing ip address of the client computer. In case of a webserver the ip address of the said webserver.

```php
require __DIR__ . "/vendor/autoload.php";

use Whereami\Provider\MozillaProvider;
use Whereami\Whereami;

$provider = new MozillaProvider("your-api-key-here", ["ip" => true]);
$locator = new Whereami($provider);

$location = $locator->whereami();
```

### Third party location

Sometimes it is useful to locate third party locations. For example you might have several IoT devices whose location you need to track. You can locate these by calling `$locator->whereis($networks)` method with network info as a parameter.

```php
require __DIR__ . "/vendor/autoload.php";

use Whereami\Provider\MozillaProvider;
use Whereami\Whereami;

$provider = new MozillaProvider("your-api-key-here");
$locator = new Whereami($provider);

$networks[] = [
    "name" => "#WiFi@Changi",
    "address" => "64:d8:14:72:60:0c",
    "signal" => -90,
    "channel" => 149,
];

$networks[] = [
    "name" => "#WiFi@Changi",
    "address" => "10:bd:18:5f:e9:83",
    "signal" => -70,
    "channel" => 6,
];

$location = $locator->whereis($networks);

/*
Array
(
    [latitude] => 1.3558172
    [longitude] => 103.9915859
    [accuracy] => 38
)
*/
```

## Providers
### Combain Provider

This provider uses [Combain CPS API](https://combain.com/api/). It requires an API key but they do offer [free evaluation account](https://combain.com/sign-up/) for developers.

```php
use Whereami\Provider\CombainProvider;

$provider = new CombainProvider("your-api-key-here");
```

### Google Provider

This provider uses [The Google Maps Geolocation API](https://developers.google.com/maps/documentation/geolocation/intro). You will need an [API key](https://developers.google.com/maps/documentation/geolocation/get-api-key). There are some [limits on free usage](https://developers.google.com/maps/documentation/geolocation/usage-limits).

```php
use Whereami\Provider\GoogleProvider;

$provider = new GoogleProvider("your-api-key-here");
```

### Mozilla Provider

This provider uses [Mozilla Location Service (MLS)](https://location.services.mozilla.com/). It requires an API key but you can use the key `test` for developing.

```php
use Whereami\Provider\MozillaProvider;

$provider = new MozillaProvider("your-api-key-here");
```

### Unwired Provider

This provider uses [Unwired Labs LocationAPI](https://unwiredlabs.com/locationapi). API key is required but you can sign up for [free developer account](https://unwiredlabs.com/trial).

```php
use Whereami\Provider\UnwiredProvider;

$provider = new UnwiredProvider("your-api-key-here");
```

## Scanners

Scanners scan the surrounding wifi networks and return the results as an array. This array is then given to a provider which uses the network data to retrieve the geoposition.

### Airport Scanner

This is the default scanner for macOS. It uses the `airport` commandline tool from Apple80211 framework. Custom command can be passed via constructor. This is optional and should be used for example if your `airport` binary is located in non standard place

```php
use Whereami\Scanner\AirportScanner;

$scanner = new AirportScanner("/tmp/airport  --scan 2>&1");
```

### Iwlist With Sudo

This is the default scanner for Linux and other *NIX based systems. It uses the [iwlist](https://linux.die.net/man/8/iwlist) commandline tool. This scanner is bit more complicated to set up since it needs root privileges to be run.

There are several ways to get around this. The default one is to give your webserver privileges to run `sudo iwlist` without password by editing the `/etc/sudoers` file.

```
$ sudo visudo
```

Then add the following to the end of the file.

```
apache ALL=(ALL) NOPASSWD: /sbin/iwlist
```

After editing the file save and exit. Make sure you can run the command as your webserver user.

```
$ sudo su apache
$ /sbin/iwlist scan
```

If you can see the scan results you are good to go.

```php
use Whereami\Scanner\IwlistScanner;

$scanner = new IwlistScanner;
```

### Iwlist Without Sudo

If you do not want to give the webserver privileges to run `iwlist` as root you could set up a root cronjob which writes the scan results to text file instead.

```
$ sudo crontab -e
```

Add the following line to the crontab. This will run the scan every 10 minutes.

```
*/10 * * * * /sbin/iwlist scan > /tmp/iwlist.txt
```

After editing the file save and exit. Wait for 10 minutes to make sure your cron entry works.

```
$ cat /tmp/iwlist.txt
```

If you can see the scan results you are good to go. However you must use custom command which uses the output from the cronjob.

```php
use Whereami\Scanner\IwlistScanner;

$scanner = new IwlistScanner("cat /tmp/iwlist.txt");
```

## Adapters

Some operating have commandline tools for determining the current position. Adapters provide an interface for these. You can use an adapter for developing if you do not have access to any external geopositioning provider.

```php
require __DIR__ . "/vendor/autoload.php";

use Whereami\Adapter\CoreLocationAdapter;
use Whereami\Whereami;

$adapter = new CoreLocationAdapter;
$locator = new Whereami($adapter);

$location = $locator->whereami();
```

Note that adapters always return the location of the current machine. They cannot be used to determine third party location. Trying to do so will trigger a PHP warning.

### Corelocation

This adapter uses the macOS [CoreLocationCLI](https://github.com/fulldecent/corelocationcli) tool. Internally it uses [Core Location](https://developer.apple.com/reference/corelocation) services. Install it using Homebrew.

```
$ brew install corelocationcli
```

```php
use Whereami\Adapter\CoreLocationAdapter;

$adapter = new CoreLocationAdapter;
```

### LocateMe

This adapter uses the macOS [LocateMe](http://iharder.sourceforge.net/current/macosx/locateme/) tool. Internally it uses [Core Location](https://developer.apple.com/reference/corelocation) services. Install it using Homebrew.

```
$ brew install locateme
```

```php
use Whereami\Adapter\LocateMeAdapter;

$adapter = new LocateMeAdapter;
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
