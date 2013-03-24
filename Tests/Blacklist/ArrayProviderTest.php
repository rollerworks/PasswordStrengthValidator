<?php

namespace Rollerworks\Bundle\PasswordStrengthBundle\Tests\Blacklist;

use Rollerworks\Bundle\PasswordStrengthBundle\Blacklist\ArrayProvider;

class ArrayProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testBlackList()
    {
        $provider = new ArrayProvider(array('test', 'foobar', 0));

        $this->assertTrue($provider->isBlacklisted('test'));
        $this->assertTrue($provider->isBlacklisted('foobar'));
        $this->assertTrue($provider->isBlacklisted(0));

        $this->assertFalse($provider->isBlacklisted('tests'));
        $this->assertFalse($provider->isBlacklisted(null));
        $this->assertFalse($provider->isBlacklisted(false));
    }
}
