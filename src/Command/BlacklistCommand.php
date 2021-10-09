<?php

/*
 * This file is part of the RollerworksPasswordStrengthValidator package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\PasswordStrength\Command;

use Psr\Container\ContainerInterface;
use Rollerworks\Component\PasswordStrength\Blacklist\BlacklistProviderInterface;
use Rollerworks\Component\PasswordStrength\Blacklist\UpdatableBlacklistProviderInterface;
use Rollerworks\Component\PasswordStrength\Validator\Constraints\Blacklist;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class BlacklistCommand extends Command
{
    /**
     * @var BlacklistProviderInterface|UpdatableBlacklistProviderInterface
     */
    protected $blacklistProvider;

    /**
     * @var ContainerInterface
     */
    private $providers;

    public function __construct(ContainerInterface $providers)
    {
        parent::__construct(null);

        $this->addOption('provider', null, InputOption::VALUE_REQUIRED, 'Blacklist Provider name', 'default');
        $this->providers = $providers;
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        trigger_deprecation('rollerworks/password-strength-validator', '1.7', 'The Blacklist validator is deprecated and will be removed in the next major version. Use the NotInPasswordCommonList from rollerworks/password-common-list package instead, or use the NotCompromisedPassword validator from the symfony/validator package instead.', Blacklist::class);

        $this->blacklistProvider = $this->providers->get($input->getOption('provider'));

        if (! $this->blacklistProvider instanceof UpdatableBlacklistProviderInterface) {
            throw new \RuntimeException(sprintf('Blacklist provider "%s" is not updatable.', $input->getOption('provider')));
        }
    }
}
