<?php

namespace Rollerworks\Bundle\PasswordStrengthBundle\Tests\Command;

use Rollerworks\Bundle\PasswordStrengthBundle\Blacklist\SqliteProvider;
use Symfony\Component\DependencyInjection\Container;

abstract class BlacklistCommandTestCase extends \PHPUnit_Framework_TestCase
{
    protected static $container;
    protected static $dbFile;
    protected static $storage;

    public static function setUpBeforeClass()
    {
        if (!class_exists('SQLite3') && (!class_exists('PDO') || !in_array('sqlite', \PDO::getAvailableDrivers()))) {
            self::markTestSkipped('This test requires SQLite support in your environment');
        }

        self::$dbFile = tempnam(sys_get_temp_dir(), 'rw_sqlite_storage');
        if (file_exists(self::$dbFile)) {
            @unlink(self::$dbFile);
        }

        $sqliteProvider = new SqliteProvider('sqlite:' . self::$dbFile);

        self::$container = new Container();
        self::$container->set('rollerworks_password_strength.blacklist.provider.sqlite', $sqliteProvider);
    }

    public static function tearDownAfterClass()
    {
        @unlink(self::$dbFile);
    }

    protected function setUp()
    {
        $this->getProvider()->purge();
    }

    /**
     * @return SqliteProvider
     */
    protected function getProvider()
    {
        return self::$container->get('rollerworks_password_strength.blacklist.provider.sqlite');
    }
}
