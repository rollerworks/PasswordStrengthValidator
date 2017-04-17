Strength validation
===================

You can use the `Rollerworks\Bundle\PasswordStrengthBundle\Validator\Constraints\PasswordStrength`
constraint with the following options.

* message: The validation message (default: password_too_weak)
* minLength: Minimum length of the password, should be at least 6 (or 8 for better security)
* minStrength: Minimum required strength of the password.
* unicodeEquality: Consider characters from other scripts (unicode) as equal.
  When set to false (default) `²` will seen as a special character rather then then 2 in another script.

The strength is computed from various measures including length and usage of (special) characters.

**Note:** A strength is measured by the presence of a character and total length.
One can have a 'medium' password consisting of only a-z and A-Z, but with a length higher than 12 characters.

The strength is calculated using the following rules:

* Does the password contain an alpha character?
* Does the password contain both lowercase and uppercase alpha characters?
* Does the password contain a digit?
* Does the password contain a special character?
* Does the password have a length of at least 13 characters.

Each of these rules will add 1 point to the password strength. The minimum strength is 1 and the maximum strength is 5.

If the password consists of only numbers or a-z/A-Z the final strength decreases.

The strengths are listed as follows:

*  1: Very Weak (any character)
*  2: Weak (at least one lower and capital)
*  3: Medium (at least one lower and capital and number)
*  4: Strong (at least one lower and capital and number) (recommended for most usages)
*  5: Very Strong (recommended for admin or finance related services)

If you are using annotations for validation, include the constraints namespace:

```php
use Rollerworks\Bundle\PasswordStrengthBundle\Validator\Constraints as RollerworksPassword;
```

and then add the PasswordStrength constraint to the relevant field:

```php
/**
 * @RollerworksPassword\PasswordStrength(minLength=7, minStrength=3)
 */
protected $password;
```
