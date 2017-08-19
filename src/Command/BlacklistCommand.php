<?php

/*
 * This file is part of the RollerworksPasswordStrengthBundle package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\PasswordStrength\Command;

use Rollerworks\Component\PasswordStrength\Blacklist\BlacklistProviderInterface;
use Rollerworks\Component\PasswordStrength\Blacklist\UpdatableBlacklistProviderInterface;
use Symfony\Component\Console\Command\Command;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
abstract class BlacklistCommand extends Command
{
    /**
     * @var BlacklistProviderInterface|UpdatableBlacklistProviderInterface
     */
    protected $blacklistProvider;

    public function __construct(BlacklistProviderInterface $blacklistProvider)
    {
        parent::__construct(null);

        $this->blacklistProvider = $blacklistProvider;
    }

    public function isEnabled()
    {
        return $this->blacklistProvider instanceof UpdatableBlacklistProviderInterface;
    }
}
