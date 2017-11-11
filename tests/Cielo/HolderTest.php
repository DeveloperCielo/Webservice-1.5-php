<?php

namespace Cielo;

use PHPUnit\Framework\TestCase;

/**
 * @author Andrey K. Vital <andreykvital@gmail.com>
 */
class HolderTest extends TestCase
{
    /**
     * @var Holder
     */
    private $holder;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->holder = new Holder(
            '4012001038443335',
            '2015',
            '08',
            Holder::CVV_INFORMED,
            '973'
        );
    }

    /**
     * @test
     */
    public function getToken()
    {
        $this->assertNull($this->holder->getToken());
    }

    /**
     * @test
     */
    public function definesTokenOnCtor()
    {
        $token = 'TuS6LeBHWjqFFtE7S3zR052Jl/KUlD+tYJFpAdlA87E=';

        $holder = new Holder($token);

        $this->assertEquals($token, $holder->getToken());
    }

    /**
     * @test
     */
    public function getCreditCardNumber()
    {
        $this->assertEquals('4012001038443335', $this->holder->getCreditCardNumber());
    }

    /**
     * @test
     */
    public function getExpiration()
    {
        $this->assertEquals('201508', $this->holder->getExpiration());
    }

    /**
     * @test
     */
    public function getCVVIndicator()
    {
        $this->assertEquals(Holder::CVV_INFORMED, $this->holder->getCVVIndicator());
    }

    /**
     * @test
     */
    public function getCVV()
    {
        $this->assertEquals(973, $this->holder->getCVV());
    }

    public function testGetName()
    {
        $this->holder->setName('Ciclano');

        $this->assertEquals('Ciclano', $this->holder->getName());
    }

    /**
     * @test
     */
    public function setNameThrowsUnexpectedValue()
    {
        $this->expectException(\UnexpectedValueException::class);

        $this->holder->setName(null);
    }

    /**
     * @test
     */
    public function setToken()
    {
        $this->holder->setToken('TuS6LeBHWjqFFtE7S3zR052Jl/KUlD+tYJFpAdlA87E=');

        $this->assertEquals('TuS6LeBHWjqFFtE7S3zR052Jl/KUlD+tYJFpAdlA87E=', $this->holder->getToken());
    }

    /**
     * @test
     */
    public function setTokenThrowsUnexpectedValue()
    {
        $this->expectException(\UnexpectedValueException::class);

        $this->holder->setToken(null);
    }

    /**
     * @test
     */
    public function setCVVThrowsUnexpectedValue()
    {
        $this->expectException(\UnexpectedValueException::class);

        $this->holder->setCVV(null);
    }

    /**
     * @return array
     */
    public function provideInvalidExpirationParameters()
    {
        return [
            [null, null],
            [false, false],
            ['', ''],
            [-1, -1],
            ['99999', '2'],
            ['2015', '13'],
        ];
    }

    /**
     * @test
     * @param mixed $year
     * @param mixed $month
     * @dataProvider provideInvalidExpirationParameters
     */
    public function setExpirationThrowsUnexpectedValue($year, $month)
    {
        $this->expectException(\UnexpectedValueException::class);

        $this->holder->setExpiration($year, $month);
    }

    /**
     * @test
     */
    public function setCVVIndicatorAssignsNullToCvvProperty()
    {
        $this->assertNotNull($this->holder->getCVV());

        $this->holder->setCVVIndicator(Holder::CVV_NOT_INFORMED);

        $this->assertNull($this->holder->getCVV());
    }

    /**
     * @test
     */
    public function setCVVIndicatorThrowsUnexpectedValue()
    {
        $this->expectException(\UnexpectedValueException::class);

        $this->holder->setCVVIndicator(-1);
    }

    /**
     * @test
     */
    public function setCreditCardNumberThrowsUnexpectedValue()
    {
        $this->expectException(\UnexpectedValueException::class);

        $this->holder->setCreditCardNumber(false);
    }
}
