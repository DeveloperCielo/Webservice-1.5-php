<?php

namespace Cielo;

use PHPUnit\Framework\TestCase;

/**
 * @author Andrey K. Vital <andreykvital@gmail.com>
 */
class AuthorizationTest extends TestCase
{
    /**
     * @var Authorization
     */
    private $authorization;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $authorization = new Authorization();

        $authorization->setCode(5);
        $authorization->setMessage('Autorização negada');
        $authorization->setDateTime('2011-12-09T10:58:45.847-02:00');
        $authorization->setTotal(1000);
        $authorization->setLr(57);
        $authorization->setNsu(221766);

        $this->authorization = $authorization;
    }

    /**
     * @test
     */
    public function getCode()
    {
        $this->assertEquals(5, $this->authorization->getCode());
    }

    /**
     * @test
     */
    public function getMessage()
    {
        $this->assertEquals('Autorização negada', $this->authorization->getMessage());
    }

    /**
     * @test
     */
    public function getDateTime()
    {
        $this->assertEquals('2011-12-09T10:58:45.847-02:00', $this->authorization->getDateTime());
    }

    /**
     * @test
     */
    public function getTotal()
    {
        $this->assertEquals(1000, $this->authorization->getTotal());
    }

    /**
     * @test
     */
    public function getLr()
    {
        $this->assertEquals(57, $this->authorization->getLr());
    }

    /**
     * @test
     */
    public function getNsu()
    {
        $this->assertEquals(221766, $this->authorization->getNsu());
    }

    /**
     * @test
     */
    public function getArp()
    {
        $this->assertNull($this->authorization->getArp());
    }

    /**
     * @test
     */
    public function setArp()
    {
        $this->authorization->setArp(123456);

        $this->assertEquals(123456, $this->authorization->getArp());
    }
}
