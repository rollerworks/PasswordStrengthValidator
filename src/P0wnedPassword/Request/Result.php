<?php

/*
 * This file is part of the RollerworksPasswordStrengthValidator package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Component\PasswordStrength\P0wnedPassword\Request;

/**
 * @internal
 */
final class Result
{
    /**
     * @var int
     */
    private $useCount = 0;

    /**
     * Result constructor.
     *
     * @param int $useCount
     */
    public function __construct($useCount)
    {
        $this->useCount = (int) $useCount;
    }

    /**
     * @return int
     */
    public function getUseCount()
    {
        return $this->useCount;
    }

    /**
     * @return bool
     */
    public function wasFound()
    {
        return $this->useCount > 0;
    }
}
