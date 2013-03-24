<?php

namespace Rollerworks\Bundle\PasswordStrengthBundle\Tests\DependencyInjection;

use Rollerworks\Bundle\PasswordStrengthBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testConfigTree()
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, array(array()));

        $this->assertEquals('rollerworks_password_strength.blacklist.provider.noop', $config['blacklist']['default_provider']);
    }

    public function testConfigSqlite()
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, array(
                array('blacklist' => array('providers' => array(
                    'sqlite' => array('dsn' => 'sqlite:/path/to/the/db/file')
                )
            ))
        ));

        $this->assertEquals('sqlite:/path/to/the/db/file', $config['blacklist']['providers']['sqlite']['dsn']);
    }

    public function testConfigArray()
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, array(
                array('blacklist' => array('providers' => array(
                    'array' => array('foo', 'foobar', 'kaboom')
                )
            ))
        ));

        $this->assertEquals(array('foo', 'foobar', 'kaboom'), $config['blacklist']['providers']['array']);
    }

    public function testConfigChain()
    {
        $processor = new Processor();
        $configuration = new Configuration();
        $config = $processor->processConfiguration($configuration, array(
                array('blacklist' => array('providers' => array(
                    'chain' => array('providers' => array('rollerworks_password_strength.blacklist.provider.array', 'rollerworks_password_strength.blacklist.provider.sqlite'))
                )
            ))
        ));

        $this->assertEquals(array('rollerworks_password_strength.blacklist.provider.array', 'rollerworks_password_strength.blacklist.provider.sqlite'), $config['blacklist']['providers']['chain']['providers']);
    }
}
