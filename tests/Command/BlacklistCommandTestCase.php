<?php

/*
 * This file is part of the RollerworksPasswordStrengthValidator package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\PasswordStrength\Tests\Command;

use PHPUnit\Framework\TestCase;
use Rollerworks\Component\PasswordStrength\Blacklist\SqliteProvider;
use Rollerworks\Component\PasswordStrength\Tests\BlackListMockProviderTrait;

abstract class BlacklistCommandTestCase extends TestCase
{
    use BlackListMockProviderTrait;

    protected static $dbFile;
    protected static $storage;

    /**
     * @var SqliteProvider
     */
    protected static $blackListProvider;

    public static function setUpBeforeClass()
    {
        if (!class_exists('SQLite3') && (!class_exists('PDO') || !in_array('sqlite', \PDO::getAvailableDrivers(), true))) {
            self::markTestSkipped('This test requires SQLite support in your environment');
        }

        self::$dbFile = tempnam(sys_get_temp_dir(), 'rw_sqlite_storage');
        if (file_exists(self::$dbFile)) {
            @unlink(self::$dbFile);
        }

        self::$blackListProvider = new SqliteProvider('sqlite:'.self::$dbFile);
    }

    public static function tearDownAfterClass()
    {
        @unlink(self::$dbFile);
    }

    protected function setUp()
    {
        self::$blackListProvider->purge();
    }
}
