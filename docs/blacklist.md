Password blacklisting
=====================

Usage of the ```Rollerworks\Bundle\PasswordStrengthBundle\Validator\Constraints\Blacklist```
constraint is very simple.

    **Note.** The blacklisted passwords are case-sensitive.

## Configuration

First you need to configure a blacklist provider.

    **Tip.** You can use the ChainProvider for using multiple providers at once.

```default_provider``` contains the service-name you want to use for BlackListValidator.

You can choose from:

* rollerworks_password_strength.blacklist.provider.noop: Default implementation, always returns false.
* [rollerworks_password_strength.blacklist.provider.array](#array): In-memory-array blacklist, not recommended for big lists.
* [rollerworks_password_strength.blacklist.provider.sqlite](#sqlite): SQLite3 database file, updatable using the rollerworks-password:blacklist:update console command.
* [rollerworks_password_strength.blacklist.provider.chain](#chain): Allows using multiple blacklist providers.

Or create your own service.

    Your blacklist provider must implement the Rollerworks\Bundle\PasswordStrengthBundle\Blacklist\BlacklistProviderInterface.

Add the following to your config file:

``` yaml
# app/config/config.yml

rollerworks_password_strength:
    blacklist:
        # Replace rollerworks_password_strength.blacklist.provider.noop with the service you want to use
        default_provider: rollerworks_password_strength.blacklist.provider.noop
```

### Array

Add the following to your config file:

``` yaml
# app/config/config.yml

rollerworks_password_strength:
    blacklist:
        default_provider: rollerworks_password_strength.blacklist.provider.array
        providers:
            # The 'array' contains a list with all the blacklisted words
            array: [blacklisted-word-1, blacklisted-word-2]
```

### Sqlite

Add the following to your config file:

``` yaml
# app/config/config.yml

rollerworks_password_strength:
    blacklist:
        default_provider: rollerworks_password_strength.blacklist.provider.sqlite
        providers:
            sqlite:
                # Make sure the location is outside the cache dir
                dsn: "file:%kernel.root_dir%/Resources/password_blacklist.db"
```

### Chain

The chain provider works by searching in the registered providers.

You can add as many providers as you want.

Add the following to your config file:

``` yaml
# app/config/config.yml

rollerworks_password_strength:
    blacklist:
        default_provider: rollerworks_password_strength.blacklist.provider.sqlite
        providers:
            chain:
                providers:
                    # Add a list of services to search in
                    - rollerworks_password_strength.blacklist.provider.array
                    - rollerworks_password_strength.blacklist.provider.sqlite
```

## Annotations

If you are using annotations for validation, include the constraints namespace:

```php
use Rollerworks\Bundle\PasswordStrengthBundle\Validator\Constraints as RollerworksPassword;
```

and then add the PasswordStrength validator to the relevant field:

```php

/**
 * @RollerworksPassword\Blacklist()
 */
protected $password;
```

## Updating the blacklist database (SQLite only)

You can use the following app/console commands to manage your blacklist-database.

To add new passwords to the blacklist:

```bash
$ app/console rollerworks-password:blacklist:update password password2 "this pass word has spaces"
```

To remove passwords from the blacklist.

```bash
$ app/console rollerworks-password:blacklist:delete password password2 "this pass word has spaces"
```

Or when you want import a list of passwords from a file, use the --file parameter.

Every line in the file is considered a password.

```bash
$ app/console rollerworks-password:blacklist:update --file="/tmp/passwords-blacklist.txt"
```

To remove the database completely (warning this will remove all the blacklisted passwords from your database).

```bash
$ app/console rollerworks-password:blacklist:purge
```

To export the database, this will display all the blacklisted passwords (one per line).

You can then forward the result to a text file.

```bash
$ app/console rollerworks-password:blacklist:list > /tmp/exported-blacklist.txt
```

## Existing blacklists

To get started you can use the bad/leaked passwords databases provider by
[Skull Security](http://www.skullsecurity.org/wiki/index.php/Passwords).

Its recommended to use at least the 500-worst-passwords database.
Especially when not enforcing strong passwords using the [PasswordStrengthValidator](strength-validation.md).
