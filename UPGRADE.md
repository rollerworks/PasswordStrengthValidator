UPGRADE
=======

## Upgrade from 1.7 to 2.0

* The blacklist validator was removed.

  Use the [NotCompromisedPassword](https://symfony.com/doc/current/reference/constraints/NotCompromisedPassword.html)
  validator or [PasswordCommonList Validator](https://github.com/rollerworks/password-common-list) instead.

* The PwnedPassword validator was removed in favor of the Symfon
  [NotCompromisedPassword](https://symfony.com/doc/current/reference/constraints/NotCompromisedPassword.html) validator.

## Upgrade from 1.6 to 1.7

* The blacklist validator was deprecated in favor of the [PasswordCommonList Validator](https://github.com/rollerworks/password-common-list).

## Upgrade from 1.3 to 1.4

* The PwnedPassword validator is deprecated in favor of the Symfony [NotCompromisedPassword](https://symfony.com/doc/current/reference/constraints/NotCompromisedPassword.html) validator

