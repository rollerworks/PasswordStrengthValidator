RollerworksPasswordStrengthBundle
=================================

This bundle provides a validator for ensuring strong passwords in Symfony2 applications.

Passwords are validated using strength-levels (weak, medium, strong etc).

If you want to use a more detailed configuration (pattern requirement),
you can use the [PasswordStrengthBundle](https://github.com/jbafford/PasswordStrengthBundle) provided by John Bafford.

    You can use this bundle and the one provided by John Bafford side by side without any conflict.

    Its however not recommended to use both the pattern-requirement and strength-level constraint
    at the same property/method, as both provide similar functionality.

## Installation

### Step 1: Using Composer (recommended)

To install RollerworksPasswordStrengthBundle with Composer just add the following to your
`composer.json` file:

```js
// composer.json
{
    // ...
    require: {
        // ...
        "rollerworks/password-strength-bundle": "master-dev"
    }
}
```

**NOTE**: Please replace `master-dev` in the snippet above with the latest stable
branch, for example ``1.0.*``.

Then, you can install the new dependencies by running Composer's ``update``
command from the directory where your ``composer.json`` file is located:

```bash
$ php composer.phar update rollerworks/password-strength-bundle
```

Now, Composer will automatically download all required files, and install them
for you. All that is left to do is to update your ``AppKernel.php`` file, and
register the new bundle:

```php
<?php

// in AppKernel::registerBundles()
$bundles = array(
    // ...
    new Rollerworks\Bundle\PasswordStrengthBundle\RollerworksPasswordStrengthBundle(),
    // ...
);
```

### Step2: Enable the bundle

Enable the bundle in the kernel:

``` php
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

### [Strength validation](docs/strength-validation.md)

Validates the passwords strength-level (weak, medium, strong etc).

### [Password blacklisting](docs/blacklist.md)

There are times you want forbid (blacklist) a password from usage.

Passwords are blacklisted using providers which can either an array or
(flat-file) database (which you can update regularly).

With the default installation the following providers can be used.

* Noop: Default provider, does nothing.
* Array: Simple in memory blacklist provider (not recommended for big lists)
* Sqlite: Provides the blacklist using a SQLite3 database file.

But building your own is also possible.
__Documentation on this is currently missing,
see current providers for more information.__
