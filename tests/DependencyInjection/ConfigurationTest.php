<?php

/*
 * This file is part of the RollerworksPasswordStrengthBundle package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\PasswordStrengthBundle\Tests\DependencyInjection;

use Matthias\SymfonyConfigTest\PhpUnit\AbstractConfigurationTestCase;
use Rollerworks\Bundle\PasswordStrengthBundle\DependencyInjection\Configuration;

class ConfigurationTest extends AbstractConfigurationTestCase
{
    protected function getConfiguration()
    {
        return new Configuration();
    }

    public function testNoBlacklistProvidersConfiguredByDefault()
    {
        $this->assertProcessedConfigurationEquals(
            array(
                array(),
            ),
            array(
                'blacklist' => array(
                    'default_provider' => 'rollerworks_password_strength.blacklist.provider.noop',
                ),
            )
        );
    }

    public function testSqlLiteBlacklistProviderIsConfigured()
    {
        $this->assertProcessedConfigurationEquals(
            array(
                array(
                    'blacklist' => array(
                        'providers' => array(
                            'sqlite' => array('dsn' => 'sqlite:/path/to/the/db/file'),
                        ),
                    ),
                ),
            ),
            array(
                'blacklist' => array(
                    'providers' => array(
                        'sqlite' => array('dsn' => 'sqlite:/path/to/the/db/file'),
                        'array' => array(),
                    ),
                    'default_provider' => 'rollerworks_password_strength.blacklist.provider.noop',
                ),
            )
        );
    }

    public function testArrayBlacklistProviderIsConfigured()
    {
        $this->assertProcessedConfigurationEquals(
            array(
                array(
                    'blacklist' => array(
                        'providers' => array(
                            'array' => array('foo', 'foobar', 'kaboom'),
                        ),
                    ),
                ),
            ),
            array(
                'blacklist' => array(
                    'providers' => array(
                        'array' => array('foo', 'foobar', 'kaboom'),
                    ),
                    'default_provider' => 'rollerworks_password_strength.blacklist.provider.noop',
                ),
            )
        );
    }

    public function testConfigChain()
    {
        $this->assertProcessedConfigurationEquals(
            array(
                array(
                    'blacklist' => array(
                        'providers' => array(
                            'chain' => array(
                                'providers' => array(
                                    'rollerworks_password_strength.blacklist.provider.array',
                                    'rollerworks_password_strength.blacklist.provider.sqlite',
                                ),
                            ),
                        ),
                    ),
                ),
            ),
            array(
                'blacklist' => array(
                    'providers' => array(
                        'chain' => array(
                            'providers' => array(
                                'rollerworks_password_strength.blacklist.provider.array',
                                'rollerworks_password_strength.blacklist.provider.sqlite',
                            ),
                        ),
                        'array' => array(),
                    ),
                    'default_provider' => 'rollerworks_password_strength.blacklist.provider.noop',
                ),
            )
        );
    }
}
