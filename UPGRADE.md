UPGRADE
=======

## Upgrade from 2.x to 3.0

* Support for Symfony 6 was removed, PHP 8.4 and Symfony 7.4 is now the minimum required version.

* The constraints constructor was changed to better support the new Symfony validator.

    * The required options are now the first arguments, and must have a value.
    * Passing options as an array is no longer supported, use named arguments instead.

  ```diff
  - new PasswordRequirements(['minLength' => 8]);
  + new PasswordRequirements(minLength: 8);
  ```

  ```diff
  - new PasswordStrength([minStrength' => 4]);
  + new PasswordStrength(minStrength: 4);
  ```

* Support for annotation mapping was removed.

    ```diff
    -/**
    - * @RollerworksPassword\PasswordStrength(minLength=7)
    - */
    +#[PasswordStrength(minLength: 7)]
    ```
