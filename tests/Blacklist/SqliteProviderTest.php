<?php

/*
 * This file is part of the RollerworksPasswordStrengthValidator package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\PasswordStrength\Tests\Blacklist;

use PHPUnit\Framework\TestCase;
use Rollerworks\Component\PasswordStrength\Blacklist\SqliteProvider;

class SqliteProviderTest extends TestCase
{
    /**
     * @var string
     */
    protected static $dbFile;

    /**
     * @var SqliteProvider
     */
    protected static $provider;

    public static function setUpBeforeClass(): void
    {
        if (!class_exists('SQLite3') && (!class_exists('PDO') || !in_array('sqlite', \PDO::getAvailableDrivers(), true))) {
            self::markTestSkipped('This test requires SQLite support in your environment');
        }

        self::$dbFile = tempnam(sys_get_temp_dir(), 'rw_sqlite_storage');
        if (file_exists(self::$dbFile)) {
            @unlink(self::$dbFile);
        }

        self::$provider = new SqliteProvider('sqlite:'.self::$dbFile);
    }

    public static function tearDownAfterClass(): void
    {
        @unlink(self::$dbFile);
    }

    public function testAdd()
    {
        self::assertTrue(self::$provider->add('test'));
        self::assertTrue(self::$provider->add('foobar'));

        self::assertTrue(self::$provider->isBlacklisted('test'));
        self::assertTrue(self::$provider->isBlacklisted('foobar'));
        self::assertFalse(self::$provider->isBlacklisted('testing'));
    }

    public function testDelete()
    {
        self::assertTrue(self::$provider->add('test'));
        self::assertTrue(self::$provider->add('foobar'));

        self::assertTrue(self::$provider->isBlacklisted('test'));
        self::assertTrue(self::$provider->isBlacklisted('foobar'));
        self::assertFalse(self::$provider->isBlacklisted('testing'));

        self::assertTrue(self::$provider->delete('foobar'));
        self::assertFalse(self::$provider->isBlacklisted('foobar'));
        self::assertTrue(self::$provider->isBlacklisted('test'));
    }

    public function testPurge()
    {
        self::assertTrue(self::$provider->add('test'));
        self::assertTrue(self::$provider->add('foobar'));

        self::assertTrue(self::$provider->isBlacklisted('test'));
        self::assertTrue(self::$provider->isBlacklisted('foobar'));

        self::$provider->purge();
        self::assertFalse(self::$provider->isBlacklisted('foobar'));
        self::assertFalse(self::$provider->isBlacklisted('test'));
        self::assertTrue(self::$provider->add('test'));
        self::assertTrue(self::$provider->isBlacklisted('test'));
    }

    protected function setUp(): void
    {
        if (!class_exists('SQLite3') && (!class_exists('PDO') || !in_array('sqlite', \PDO::getAvailableDrivers(), true))) {
            $this->markTestSkipped('This test requires SQLite support in your environment');
        }
        self::$provider->purge();
    }
}
