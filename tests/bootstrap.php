<?php

/*
 * This file is part of the RollerworksPasswordStrengthBundle package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Composer\Autoload\ClassLoader;

error_reporting(E_ALL | E_STRICT);

if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
    throw new \RuntimeException('Did not find vendor/autoload.php. Please Install vendors using command: composer.phar install --dev');
}

/**
* @var $loader ClassLoader
*/
$loader = require_once __DIR__ . '/../vendor/autoload.php';
$loader->add('Rollerworks\\Bundle\\PasswordStrengthBundle\\Tests', __DIR__);
