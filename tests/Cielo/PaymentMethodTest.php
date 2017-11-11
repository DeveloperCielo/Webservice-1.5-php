<?php

namespace Cielo;

use PHPUnit\Framework\TestCase;

class PaymentMethodTest extends TestCase
{
    /**
     * @var PaymentMethod
     */
    private $paymentMethod;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->paymentMethod = new PaymentMethod(PaymentMethod::VISA);
    }

    /**
     * @test
     */
    public function getIssuer()
    {
        $this->assertEquals(PaymentMethod::VISA, $this->paymentMethod->getIssuer());
    }

    /**
     * @test
     */
    public function getProduct()
    {
        $this->assertEquals(PaymentMethod::CREDITO_A_VISTA, $this->paymentMethod->getProduct());
    }

    /**
     * @test
     */
    public function getInstallments()
    {
        $this->assertEquals(1, $this->paymentMethod->getInstallments());
    }

    /**
     * @test
     */
    public function setIssuer()
    {
        $this->paymentMethod->setIssuer(PaymentMethod::DINERS);

        $this->assertEquals(PaymentMethod::DINERS, $this->paymentMethod->getIssuer());
    }

    /**
     * @test
     */
    public function setIssuerThrowsUnexpectedValue()
    {
        $this->expectException(\UnexpectedValueException::class);

        $this->paymentMethod->setIssuer('invalid');
    }

    /**
     * @test
     */
    public function setProduct()
    {
        $this->paymentMethod->setProduct(PaymentMethod::PARCELADO_LOJA);

        $this->assertEquals(PaymentMethod::PARCELADO_LOJA, $this->paymentMethod->getProduct());
    }

    /**
     * @test
     */
    public function setProductThrowsUnexpectedValue()
    {
        $this->expectException(\UnexpectedValueException::class);

        $this->paymentMethod->setProduct('invalid');
    }

    /**
     * @test
     */
    public function setInstallments()
    {
        $this->paymentMethod->setProduct(PaymentMethod::PARCELADO_LOJA);

        $this->paymentMethod->setInstallments('06');

        $this->assertEquals('06', $this->paymentMethod->getInstallments());
    }

    /**
     * @return \string[][]
     */
    public function provideInvalidProductAndInstallments()
    {
        return [
            [PaymentMethod::CREDITO_A_VISTA, '02'],
            [PaymentMethod::DEBITO, '06'],
            [PaymentMethod::PARCELADO_LOJA, '001'],
            [PaymentMethod::PARCELADO_LOJA, '0']
        ];
    }

    /**
     * @test
     * @param string $product
     * @param string $installments
     * @dataProvider provideInvalidProductAndInstallments
     */
    public function setInstallmentsThrowsUnexpectedValue($product, $installments)
    {
        $this->expectException(\UnexpectedValueException::class);

        $this->paymentMethod->setProduct($product);

        $this->paymentMethod->setInstallments($installments);
    }
}
