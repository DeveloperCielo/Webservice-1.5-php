<?php

namespace Cielo;

use PHPUnit\Framework\TestCase;

/**
 * @author Andrey K. Vital <andreykvital@gmail.com>
 */
class TransactionTest extends TestCase
{
    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->transaction = new Transaction(
            new Merchant('1006993069', '25fbb997438630f30b112d033ce2e621b34f3'),
            new Holder('TuS6LeBHWjqFFtE7S3zR052Jl/KUlD+tYJFpAdlA87E='),
            new Order('1234', 1000),
            new PaymentMethod(PaymentMethod::VISA, PaymentMethod::CREDITO_A_VISTA),
            'http://localhost/cielo.php',
            Transaction::AUTHORIZE_WITHOUT_AUTHENTICATION,
            true
        );
    }

    /**
     * @test
     */
    public function getTid()
    {
        $this->transaction->setTid('10017348980735271001');

        $this->assertEquals('10017348980735271001', $this->transaction->getTid());
    }

    /**
     * @test
     */
    public function getPan()
    {
        $this->transaction->setPan('IqVz7P9zaIgTYdU41HaW/OB/d7Idwttqwb2vaTt8MT0=');

        $this->assertEquals('IqVz7P9zaIgTYdU41HaW/OB/d7Idwttqwb2vaTt8MT0=', $this->transaction->getPan());
    }

    public function testGetStatus()
    {
        $this->transaction->setStatus(2);

        $this->assertEquals(2, $this->transaction->getStatus());
    }

    /**
     * @test
     */
    public function getAuthentication()
    {
        /* @var Authentication $authentication */
        $authentication = $this->createMock(Authentication::class);

        $this->transaction->setAuthentication($authentication);

        $this->assertSame($authentication, $this->transaction->getAuthentication());
    }

    /**
     * @test
     */
    public function getAuthorization()
    {
        /* @var Authorization $authorization */
        $authorization = $this->createMock(Authorization::class);

        $this->transaction->setAuthorization($authorization);

        $this->assertSame($authorization, $this->transaction->getAuthorization());
    }

    /**
     * @test
     */
    public function setReturnURLThrowsUnexpectedValue()
    {
        $this->expectException(\UnexpectedValueException::class);

        $this->transaction->setReturnURL(null);
    }

    /**
     * @test
     */
    public function setAuthorizeThrowsUnexpectedValue()
    {
        $this->expectException(\UnexpectedValueException::class);

        $this->transaction->setAuthorize(-1);
    }

    /**
     * @test
     */
    public function setCaptureThrowsUnexpectedValue()
    {
        $this->expectException(\UnexpectedValueException::class);

        $this->transaction->setCapture(0);
    }

    /**
     * @test
     */
    public function setFreeField()
    {
        $this->transaction->setFreeField('1234');

        $this->assertEquals('1234', $this->transaction->getFreeField());
    }

    /**
     * @test
     */
    public function setFreeFieldThrowsUnexpectedValue()
    {
        $this->expectException(\UnexpectedValueException::class);

        $this->transaction->setFreeField(str_repeat('1', 129));
    }

    /**
     * @test
     */
    public function setBinThrowsUnexpectedValue()
    {
        $this->expectException(\UnexpectedValueException::class);

        $this->transaction->setBin(false);
    }

    /**
     * @test
     */
    public function setGenerateToken()
    {
        $this->transaction->setGenerateToken(true);

        $this->assertTrue($this->transaction->getGenerateToken());
    }

    /**
     * @test
     */
    public function setGenerateTokenThrowsUnexpectedValue()
    {
        $this->expectException(\UnexpectedValueException::class);

        $this->transaction->setGenerateToken(0);
    }

    public function testSetHolder_shouldBeSuccess_whenHolderHasToken()
    {
        $holder = new Holder('TuS6LeBHWjqFFtE7S3zR052Jl/KUlD+tYJFpAdlA87E=');
        $this->transaction->setHolder($holder);
    }
}
