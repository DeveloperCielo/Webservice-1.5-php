<?php

namespace Cielo;

use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase
{
    /**
     * @var Token
     */
    private $token;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->token = new Token();
    }

    /**
     * @test
     */
    public function getCode()
    {
        $this->token->setCode('TuS6LeBHWjqFFtE7S3zR052Jl/KUlD+tYJFpAdlA87E=');

        $this->assertEquals('TuS6LeBHWjqFFtE7S3zR052Jl/KUlD+tYJFpAdlA87E=', $this->token->getCode());
    }

    /**
     * @test
     */
    public function getStatus()
    {
        $this->token->setStatus(1);

        $this->assertEquals(1, $this->token->getStatus());
    }

    /**
     * @test
     */
    public function getNumero()
    {
        $this->token->setNumero('9999999999999999');

        $this->assertEquals('9999999999999999', $this->token->getNumero());
    }
}
