Requirements validation
=======================

## Options

You can use the `Rollerworks\Component\PasswordStrength\Validator\Constraints\PasswordRequirements`
constraint with the following options:

|         Option          |  Type  |                                                     Description                                                     |
| ----------------------- | ------ | ------------------------------------------------------------------------------------------------------------------- |
| minLength               | `int`  | Minimum length of the password, should be at least 6 (or 8 for better security)                                     |
| requireLetters          | `bool` | Require that the password should at least contain one letter (default `true`)                                       |
| requireCaseDiff         | `bool` | Require that the password should at least contain one lowercase and one uppercase letter (default `false`)          |
| requireNumbers          | `bool` | Require that the password should at least contain one number (default `false`)                                      |
| requireSpecialCharacter | `bool` | Require that the password should at least contain one non lateral or numerical character like `@` (default `false`) |

**Note:** Validation is performed with respect to international encodings,
so the international character `９` will be treated as 9 in ASCII.

## Translations

You can customize the validation error messages with the following:

|            Message             |                           Default (en)                            |
| ------------------------------ | ----------------------------------------------------------------- |
| tooShortMessage                | _'Your password must be at least {{length}} characters long.'_    |
| missingLettersMessage          | _'Your password must include at least one letter.'_               |
| requireCaseDiffMessage         | _'Your password must include both upper and lower case letters.'_ |
| missingNumbersMessage          | _'Your password must include at least one number.'_               |
| missingSpecialCharacterMessage | _'Your password must contain at least one special character.'_    |

## Annotations

If you are using annotations for validation, include the constraints namespace:

```php
use Rollerworks\Component\PasswordStrength\Validator\Constraints as RollerworksPassword;
```

and then add the PasswordRequirements constraint to the relevant field:

```php
/**
 * @RollerworksPassword\PasswordRequirements(requireLetters=true, requireNumbers=true, requireCaseDiff=true)
 */
protected $password;
```
