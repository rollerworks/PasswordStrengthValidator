Password blacklisting
=====================

Usage of the `Rollerworks\Component\PasswordStrength\Validator\Constraints\Blacklist`
constraint works different then other strength validators.

The BlacklistValidator requires a blacklist provider before any validation can be
performed. This library comes already pre-bundled with support for, in-memory, 
SQLite3 and PDO.

**Note.** Blacklisted passwords are case-sensitive.

## Configuration

First you need a blacklist provider (for this example we will use the `ArrayProvider`):

```php
<?php

use Rollerworks\Component\PasswordStrength\Blacklist\ArrayProvider;
use Rollerworks\Component\PasswordStrength\Validator\Constraints\BlacklistValidator;

use Symfony\Component\Validator\ContainerConstraintValidatorFactory;
use Symfony\Component\Validator\Validation;

// ...

$blacklistProvider = new ArrayProvider(['root', 'password']);
$blacklistValidator = new BlacklistValidator($blacklistProvider);

// The service container is expected to have the `BlacklistValidator` loadable as service
// by id (Rollerworks\Component\PasswordStrength\Validator\Constraints\BlacklistValidator).
$container = ...; // \Psr\Container\ContainerInterface

$constraintFactory = new ContainerConstraintValidatorFactory($container);

$validator = Validation::createValidatorBuilder()
    ->setConstraintValidatorFactory($constraintFactory)
    ->getValidator();
```

That's it, you can choose to replace the `$blacklistProvider` with a more powerful provider
like PDO or the ChainProvider so you can use multiple providers at once.

## Annotations

If you are using annotations for validation, include the constraints namespace:

```php
use Rollerworks\Component\PasswordStrength\Validator\Constraints as RollerworksPassword;
```

and then add the PasswordStrength validator to the relevant field:

```php

/**
 * @RollerworksPassword\Blacklist()
 */
protected $password;
```

## Providers

### Array

The `Rollerworks\Component\PasswordStrength\Blacklist\ArrayProvider` uses a static 
list of values to check for blacklisting. This provider is best used for small lists
or testing.

For a blacklist with more then 20 entries it's better to use a database provider.

```php
$blacklistProvider = new ArrayProvider(['root', 'password']);
```

### PDO

The `Rollerworks\Component\PasswordStrength\Blacklist\PdoProvider` cannot be constructed,
you can use this abstract class as a blueprint for creating your own blacklist provider.

This provider can be updated using the provided [Console commands][1].

### Sqlite

The `Rollerworks\Component\PasswordStrength\Blacklist\SqliteProvider` uses a local
SQLite3 flat-file database for keeping the blacklist entries.

This provider requires the SQLite3 extension or the PDO extension 
(with the sqlite driver) is enabled.

**Caution:** SQLite requires a full path for the database file (relative locations
are not supported).

This provider can be updated using the provided [Console commands][1].

```php
$blacklistProvider = new SqliteProvider('sqlite:/path/to/the/db/file.db');
```

### Chain

The `Rollerworks\Component\PasswordStrength\Blacklist\ChainProvider` searches
in the blacklist providers until a positive result is given (a password is blacklisted).

```php
$blacklistProvider = new ChainProvider([
    new ArrayProvider(['root', 'password']),
    new SqliteProvider('sqlite:/path/to/the/db/file.db')
]);
```

### LazyChainProvider

Alternatively it's recommended to use the `Rollerworks\Component\PasswordStrength\Blacklist\LazyChainProvider`
as this allows to load providers lazy (and thus preventing opening resources unneeded).

```php
$container = ...; // \Psr\Container\ContainerInterface
$serviceIds = ['provider1']; // An array of service-id's to use for lazy loading providers. 

$blacklistProvider = new LazyChainProvider($container, $serviceIds);
```

## Updating the blacklist database

To update your blacklist providers with new entries (or purge outdated outdated entries)
this library provides a number of command-line commands which you can use.

To use these commands you need to install the [Symfony Console component][2].
And register these commands for usage (see the Symfony manual for details).

Each command expects a PSR-11 compatible container with the service-id
as the provider name. At least "default" is expected to exists.

### Commands

```php
$providersContainer = ...; // \Psr\Container\ContainerInterface

$application->add(new Rollerworks\Component\PasswordStrength\Command\BlacklistListCommand($providersContainer));
```


To add new passwords to the blacklist:

```bash
$ bin/console rollerworks-password:blacklist:update password password2 "this pass word has spaces"
```

To remove passwords from the blacklist.

```bash
$ bin/console rollerworks-password:blacklist:delete password password2 "this pass word has spaces"
```

Or when you want import a list of passwords from a file, use the --file parameter.

Every line (supports both Windows and Unix file-endings) in the file is considered a password.

```bash
$ bin/console rollerworks-password:blacklist:update --file="/tmp/passwords-blacklist.txt"
```

To remove the database completely (**this will remove all the blacklisted passwords from your database**).

```bash
$ bin/console rollerworks-password:blacklist:purge
```

To export the database (this will display all the blacklisted passwords (one per line)) use.

```bash
$ bin/console rollerworks-password:blacklist:list
```

You can also forward the result to a text file.

```bash
$ bin/console rollerworks-password:blacklist:list > /tmp/exported-blacklist.txt
```

### Use a different provider

To use a different provider then the de "default" use the `--provider` option, eg.
`bin/console rollerworks-password:blacklist:purge --provider=sqlite`

## Existing blacklists

To get started you can use the bad/leaked passwords databases provider by
[Skull Security](http://www.skullsecurity.org/wiki/index.php/Passwords).

Its recommended to use at least the 500-worst-passwords database.
Especially when not enforcing strong passwords using the [PasswordStrengthValidator](strength-validation.md).
