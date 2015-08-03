<?php

/*
 * This file is part of the RollerworksPasswordStrengthBundle package.
 *
 * (c) Sebastiaan Stok <s.stok@rollerscapes.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Rollerworks\Bundle\PasswordStrengthBundle\Tests\Validator;

use Rollerworks\Bundle\PasswordStrengthBundle\Blacklist\ArrayProvider;
use Rollerworks\Bundle\PasswordStrengthBundle\Validator\Constraints\Blacklist;
use Rollerworks\Bundle\PasswordStrengthBundle\Validator\Constraints\BlacklistValidator;
use Symfony\Component\Validator\Tests\Constraints\AbstractConstraintValidatorTest;
use Symfony\Component\Validator\Validation;

class BlacklistValidationTest extends AbstractConstraintValidatorTest
{
    protected function getApiVersion()
    {
        return Validation::API_VERSION_2_5;
    }

    protected function createValidator()
    {
        $provider = new ArrayProvider(array('test', 'foobar'));

        return new BlacklistValidator($provider);
    }

    public function testNullIsValid()
    {
        $this->validator->validate(null, new Blacklist());

        $this->assertNoViolation();
    }

    public function testEmptyStringIsValid()
    {
        $this->validator->validate('', new Blacklist());

        $this->assertNoViolation();
    }

    /**
     * @expectedException \Symfony\Component\Validator\Exception\UnexpectedTypeException
     */
    public function testExpectsStringCompatibleType()
    {
        $this->validator->validate(new \stdClass(), new Blacklist());
    }

    public function testNotBlackListed()
    {
        $constraint = new Blacklist();
        $this->validator->validate('weak', $constraint);
        $this->validator->validate('tests', $constraint);

        $this->assertNoViolation();
    }

    public function testBlackListed()
    {
        $constraint = new Blacklist(array(
            'message' => 'myMessage',
        ));
        $this->validator->validate('test', $constraint);

        $this->buildViolation('myMessage')
            ->setInvalidValue('test')
            ->assertRaised();
    }
}
