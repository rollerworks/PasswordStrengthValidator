<?php

namespace Rollerworks\Bundle\PasswordStrengthBundle\Tests\Blacklist;

use Rollerworks\Bundle\PasswordStrengthBundle\Blacklist\SqliteProvider;

class SqliteProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected static $dbFile;

    /**
     * @var SqliteProvider
     */
    protected static $provider;

    public static function setUpBeforeClass()
    {
        if (!class_exists('SQLite3') && (!class_exists('PDO') || !in_array('sqlite', \PDO::getAvailableDrivers()))) {
            self::markTestSkipped('This test requires SQLite support in your environment');
        }

        self::$dbFile = tempnam(sys_get_temp_dir(), 'rw_sqlite_storage');
        if (file_exists(self::$dbFile)) {
            @unlink(self::$dbFile);
        }

        self::$provider = new SqliteProvider('sqlite:' . self::$dbFile);
    }

    public static function tearDownAfterClass()
    {
        @unlink(self::$dbFile);
    }

    public function testAdd()
    {
        $this->assertTrue(self::$provider->add('test'));
        $this->assertTrue(self::$provider->add('foobar'));

        $this->assertTrue(self::$provider->isBlacklisted('test'));
        $this->assertTrue(self::$provider->isBlacklisted('foobar'));
        $this->assertFalse(self::$provider->isBlacklisted('testing'));
    }

    public function testDelete()
    {
        $this->assertTrue(self::$provider->add('test'));
        $this->assertTrue(self::$provider->add('foobar'));

        $this->assertTrue(self::$provider->isBlacklisted('test'));
        $this->assertTrue(self::$provider->isBlacklisted('foobar'));
        $this->assertFalse(self::$provider->isBlacklisted('testing'));

        $this->assertTrue(self::$provider->delete('foobar'));
        $this->assertFalse(self::$provider->isBlacklisted('foobar'));
        $this->assertTrue(self::$provider->isBlacklisted('test'));
    }

    public function testPurge()
    {
        $this->assertTrue(self::$provider->add('test'));
        $this->assertTrue(self::$provider->add('foobar'));

        $this->assertTrue(self::$provider->isBlacklisted('test'));
        $this->assertTrue(self::$provider->isBlacklisted('foobar'));

        self::$provider->purge();
        $this->assertFalse(self::$provider->isBlacklisted('foobar'));
        $this->assertFalse(self::$provider->isBlacklisted('test'));
        $this->assertTrue(self::$provider->add('test'));
        $this->assertTrue(self::$provider->isBlacklisted('test'));
    }

    protected function setUp()
    {
        if (!class_exists('SQLite3') && (!class_exists('PDO') || !in_array('sqlite', \PDO::getAvailableDrivers()))) {
            $this->markTestSkipped('This test requires SQLite support in your environment');
        }
        self::$provider->purge();
    }
}
