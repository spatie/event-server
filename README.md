# Event server

This is an experimental package, do not use in production! A more in-depth explanation is coming soon.

## Support us

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us). 

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

Don't install this yet!

## Usage

The goal of this package is to have a long-running PHP process with the whole application state in memory. This means you don't need a database or ORM. The application state is built from events stored on the server, hence the whole application is event sourced.

### Architecture

On the one hand there's a server (`php console.php server`), which will build its state from stored events on startup. After it's booted, the server is accessible via socket connections for PHP clients to work with.

In the current test suite, these clients are simple PHP scripts, though they could very well be a Laravel or Symfony application.

Events are sent from these PHP clients to the server, which will store and apply them. Furthermore, the clients can request parts of the server's state via a gateway. This server-client communication is best shown with an example:

```php
// The server is run in the background, all previously stored events are loaded into memory
$server->run();

// A client can make a new aggregate and apply events on it, these events are sent to the server
$aggregate = new TestAggregate();
$aggregate->increase(10);

// When a new request comes in, this aggregate can be resolved from the event server
$aggregate = TestAggregate::find($uuid);
```

There's also support for projections and reactors (process managers). But the setup is far from complete. Here's a non-definitive list of what's missing:

- Resolving projections from the server
- Snapshot support: which is essential to speed up server startup performance
- Proper data store support: if you, for example, want to do complex queries on aggregates
- Versioning support: allowing code changes to events, aggregates and what not
- …

## Testing

``` bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Credits

- [Brent Roose](https://github.com/brendt_gd)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
