<?php

namespace Cielo;

use PHPUnit\Framework\TestCase;

/**
 * @author Andrey K. Vital <andreykvital@gmail.com>
 */
class AuthenticationTest extends TestCase
{
    /**
     * @var Authentication
     */
    private $authentication;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $authentication = new Authentication();

        $authentication->setCode(2);
        $authentication->setMessage('Autenticada com sucesso');
        $authentication->setDateTime('2011-12-08T10:44:47.311-02:00');
        $authentication->setTotal('1000');
        $authentication->setEci(5);

        $this->authentication = $authentication;
    }

    /**
     * @test
     */
    public function getCode()
    {
        $this->assertEquals(2, $this->authentication->getCode());
    }

    /**
     * @test
     */
    public function getMessage()
    {
        $this->assertEquals('Autenticada com sucesso', $this->authentication->getMessage());
    }

    /**
     * @test
     */
    public function getDateTime()
    {
        $this->assertEquals('2011-12-08T10:44:47.311-02:00', $this->authentication->getDateTime());
    }

    /**
     * @test
     */
    public function getTotal()
    {
        $this->assertEquals('1000', $this->authentication->getTotal());
    }

    /**
     * @test
     */
    public function getEci()
    {
        $this->assertEquals(5, $this->authentication->getEci());
    }
}
