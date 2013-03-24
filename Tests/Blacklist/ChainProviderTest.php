<?php

namespace Rollerworks\Bundle\PasswordStrengthBundle\Tests\Blacklist;

use Rollerworks\Bundle\PasswordStrengthBundle\Blacklist\ArrayProvider;
use Rollerworks\Bundle\PasswordStrengthBundle\Blacklist\ChainProvider;

class ChainProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testBlackList()
    {
        $provider = new ChainProvider();
        $provider->addProvider(new ArrayProvider(array('test', 'foobar', 0)));
        $provider->addProvider(new ArrayProvider(array('weak', 'god')));

        $this->assertTrue($provider->isBlacklisted('test'));
        $this->assertTrue($provider->isBlacklisted('foobar'));
        $this->assertTrue($provider->isBlacklisted(0));

        $this->assertTrue($provider->isBlacklisted('weak'));
        $this->assertTrue($provider->isBlacklisted('god'));

        $this->assertFalse($provider->isBlacklisted('tests'));
        $this->assertFalse($provider->isBlacklisted(null));
        $this->assertFalse($provider->isBlacklisted(false));
    }

    public function testProvidersByConstruct()
    {
        $provider1 = new ArrayProvider(array('test', 'foobar', 0));
        $provider2 = new ArrayProvider(array('weak', 'god'));

        $provider = new ChainProvider(array($provider1, $provider2));

        $this->assertEquals(array($provider1, $provider2), $provider->getProviders());
    }

    public function testGetProviders()
    {
        $provider = new ChainProvider();

        $provider1 = new ArrayProvider(array('test', 'foobar', 0));
        $provider2 = new ArrayProvider(array('weak', 'god'));

        $provider->addProvider($provider1);
        $provider->addProvider($provider2);

        $this->assertEquals(array($provider1, $provider2), $provider->getProviders());
    }

    public function testNoAssignSelf()
    {
        $provider = new ChainProvider();

        $this->setExpectedException('\RuntimeException', 'Unable to add ChainProvider to its self.');
        $provider->addProvider($provider);
    }
}
