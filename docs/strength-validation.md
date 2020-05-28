Strength validation
===================

The PasswordStrength constraint computes a password strength based on the following rules: 

* Does the password contain an alpha character?
* Does the password contain both lowercase and uppercase alpha characters?
* Does the password contain a digit?
* Does the password contain a special character (non-alpha/digit)?
* Does the password have a length of at least 13 characters.

When a rules matches with the supplied password, 1 point is added to the total password strength. The minimum strength is 1 and the maximum strength is 5.

The textual representation of the strength levels are as follows:

*  1: Very Weak (matches one rule)
*  2: Weak
*  3: Medium
*  4: Strong (recommended for most usages)
*  5: Very Strong (matches all rules, recommended for admin or finance related services)

The validator adds tips as parameter to the validation message. See the [translation files](https://github.com/rollerworks/PasswordStrengthValidator/tree/master/src/Resources/translations) for the available tips.

## Options

You can use the `Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordStrength`
constraint with the following options.

|     Option      |   Type   |                                       Description                                       |
| --------------- | -------- | --------------------------------------------------------------------------------------- |
| message         | `string` | The validation message (default: `password_too_weak`)                                   |
| minLength       | `int`    | Minimum length of the password, should be at least 6 (or 8 for better security)         |
| minStrength     | `int`    | Minimum required strength of the password.                                              |
| unicodeEquality | `bool`   | Consider characters from other scripts (unicode) as equal (default: `false`).           |
|                 |          | When set to false `²` will seen as a special character rather then 2 in another script. |

## Annotations

If you are using annotations for validation, include the constraints namespace:

```php
use Rollerworks\Component\PasswordStrength\Validator\Constraints as RollerworksPassword;
```

and then add the PasswordStrength constraint to the relevant field:

```php
/**
 * @RollerworksPassword\PasswordStrength(minLength=7, minStrength=3)
 */
protected $password;
```
