RollerworksPasswordStrengthBundle
=================================

This bundle provides a validator for ensuring strong passwords in Symfony2 applications.

Passwords can be validated using either strength-levels (weak, medium, strong etc)
or by configuring explicit requirements (needs letters, numbers etc)

> This bundle provides the same level of functionality as the
> [PasswordStrengthBundle](https://github.com/jbafford/PasswordStrengthBundle) created by John Bafford.
> And is considered a replacement of the original bundle.

## Installation

### Step 1: Using Composer (recommended)

To install RollerworksPasswordStrengthBundle with Composer just run:

```bash
$ php composer.phar require rollerworks/password-strength-bundle
```

Now, Composer will automatically download all required files, and install them
for you.

### Step2: Enable the bundle

Enable the bundle in the kernel:

```php
<?php

// in AppKernel::registerBundles()
$bundles = array(
    // ...
    new Rollerworks\Bundle\PasswordStrengthBundle\RollerworksPasswordStrengthBundle(),
    // ...
);
```

Congratulations! You're ready!

## Basic Usage

**Caution:**

> The password validators do not enforce that the field must have a value!
> To make a field "required" use the [NotBlank constraint](http://symfony.com/doc/current/reference/constraints/NotBlank.html)
> in combination with the password validator(s).

### [Strength validation](docs/strength-validation.md)

Validates the passwords strength-level (weak, medium, strong etc).

### [Requirements validation](docs/requirements-validation.md)

Validates the passwords using explicitly configured requirements (letters, caseDiff, numbers, requireSpecialCharacter).

### [Password blacklisting](docs/blacklist.md)

There are times you want forbid (blacklist) a password from usage.

Passwords are blacklisted using providers which can either an array or
(flat-file) database (which you can update regularly).

With the default installation the following providers can be used.

* Noop: Default provider, does nothing.
* Array: Simple in memory blacklist provider (not recommended for big lists)
* Sqlite: Provides the blacklist using a SQLite3 database file.
* Pdo: Provides the blacklist using the PDO extension.

But building your own is also possible.
__Documentation on this is currently missing,
see current providers for more information.__
