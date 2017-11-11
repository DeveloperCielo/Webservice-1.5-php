<?php

namespace Cielo;

use PHPUnit\Framework\TestCase;

/**
 * @author Andrey K. Vital <andreykvital@gmail.com>
 */
class MerchantTest extends TestCase
{
    /**
     * @var Merchant
     */
    private $merchant;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->merchant = new Merchant('1006993069', '25fbb997438630f30b112d033ce2e621b34f3');
    }

    /**
     * @test
     */
    public function getAffiliationId()
    {
        $this->assertEquals('1006993069', $this->merchant->getAffiliationId());
    }

    /**
     * @test
     */
    public function getAffiliationKey()
    {
        $this->assertEquals('25fbb997438630f30b112d033ce2e621b34f3', $this->merchant->getAffiliationKey());
    }

    /**
     * @return array
     */
    public function provideInvalidAffiliationIds()
    {
        return [
            [null],
            [false],
            [new \stdClass()],
            [''],
            [str_repeat('9', 50)]
        ];
    }

    /**
     * @return array
     */
    public function provideInvalidAffiliationKeys()
    {
        return [
            [-1],
            [false],
            [null],
            [0],
            [''],
            [new \stdClass()]
        ];
    }

    /**
     * @test
     * @param mixed $affiliationId
     * @dataProvider provideInvalidAffiliationIds
     */
    public function setAffiliationIdThrowsUnexpectedValue($affiliationId)
    {
        $this->expectException(\UnexpectedValueException::class);

        $this->merchant->setAffiliationId($affiliationId);
    }

    /**
     * @test
     * @param mixed $affiliationKey
     * @dataProvider provideInvalidAffiliationKeys
     */
    public function setAffiliationKeyThrowsUnexpectedValue($affiliationKey)
    {
        $this->expectException(\UnexpectedValueException::class);

        $this->merchant->setAffiliationKey($affiliationKey);
    }
}
