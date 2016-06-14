<?php

namespace Cielo;

use Cielo\Http\OnlyPostHttpClientInterface;
use PHPUnit_Framework_TestCase as TestCase;

class CieloTest extends TestCase
{
    /**
     * @var Cielo
     */
    private $cielo;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $this->cielo = new Cielo('1006993069', '25fbb997438630f30b112d033ce2e621b34f3', Cielo::TEST);
    }

    /**
     * @test
     */
    public function withInvalidEndpoint()
    {
        $this->setExpectedException(\UnexpectedValueException::class);

        new Cielo(
            '1006993069',
            '25fbb997438630f30b112d033ce2e621b34f3',
            'http://'
        );
    }

    /**
     * @test
     */
    public function holderWithOnlyToken()
    {
        $this->assertInstanceOf(Holder::class, $this->cielo->holder('TuS6LeBHWjqFFtE7S3zR052Jl/KUlD+tYJFpAdlA87E='));
    }

    /**
     * @test
     */
    public function holder()
    {
        $this->assertInstanceOf(
            Holder::class,
            $this->cielo->holder(
                '4012001038443335',
                '2015',
                '08',
                Holder::CVV_INFORMED,
                '973'
            )
        );
    }

    /**
     * @test
     */
    public function order()
    {
        $this->assertInstanceOf(Order::class, $this->cielo->order('178148599', 1000));
    }

    /**
     * @test
     */
    public function paymentMethod()
    {
        $this->assertInstanceOf(
            PaymentMethod::class,
            $this->cielo->paymentMethod(PaymentMethod::VISA, PaymentMethod::CREDITO_A_VISTA)
        );
    }

    /**
     * @test
     */
    public function transaction()
    {
        $holder = $this->cielo->holder('4551870000000183', 2018, 5, Holder::CVV_INFORMED, 123);

        $order = $this->cielo->order('178148599', 1000);

        $paymentMethod = $this->cielo->paymentMethod(PaymentMethod::VISA, PaymentMethod::CREDITO_A_VISTA);

        $this->assertInstanceOf(
            Transaction::class,
            $this->cielo->transaction(
                $holder,
                $order,
                $paymentMethod,
                'http://localhost/cielo.php',
                Transaction::AUTHORIZE_WITHOUT_AUTHENTICATION,
                true
            )
        );
    }

    /**
     * @test
     */
    public function transactionRequest()
    {
        /* @var \PHPUnit_Framework_MockObject_MockObject|OnlyPostHttpClientInterface $client */
        $client = $this->getMock(OnlyPostHttpClientInterface::class);

        $client->expects($this->once())
            ->method('__invoke')
            ->with(
                $this->equalTo(Cielo::TEST),
                $this->logicalAnd(
                    $this->arrayHasKey('Content-Type'),
                    $this->arrayHasKey('Accept'),
                    $this->arrayHasKey('User-Agent')
                ),
                $this->arrayHasKey('mensagem')
            )
            ->willReturn(
                // @link https://developercielo.github.io/Webservice-1.5/?xml#tipos-de-retorno
                <<<TRANSACAO
<?xml version="1.0" encoding="ISO-8859-1"?>
<transacao xmlns="http://ecommerce.cbmp.com.br" versao="1.2.1" id="6-e7762cbf8856">
  <tid>10017348980735271001</tid>
  <dados-pedido>
    <numero>178148599</numero>
    <valor>1000</valor>
    <moeda>986</moeda>
    <data-hora>2011-12-05T16:01:28.655-02:00</data-hora>
    <descricao>[origem:255.255.255.255]</descricao>
    <idioma>PT</idioma>
  </dados-pedido>
  <forma-pagamento>
    <bandeira>visa</bandeira>
    <produto>1</produto>
    <parcelas>1</parcelas>
  </forma-pagamento>
  <status>0</status>
  <url-autenticacao>https://ecommerce.cielo.com.br/web/index.cbmp?id=a783251</url-autenticacao>
</transacao>
TRANSACAO
            );

        $cielo = new Cielo('1006993069', '25fbb997438630f30b112d033ce2e621b34f3', Cielo::TEST, $client);

        $transaction = $cielo->transactionRequest($this->createTransaction());

        $this->assertInstanceOf(Transaction::class, $transaction);
        $this->assertInstanceOf(Order::class, $transaction->getOrder());
        $this->assertInstanceOf(PaymentMethod::class, $transaction->getPaymentMethod());

        $this->assertEquals(
            'https://ecommerce.cielo.com.br/web/index.cbmp?id=a783251',
            $transaction->getAuthenticationURL()
        );
    }

    /**
     * @return Transaction
     */
    private function createTransaction()
    {
        $holder = $this->cielo->holder('4551870000000183', 2018, 5, Holder::CVV_INFORMED, 123);

        $order = $this->cielo->order('178148599', 1000);

        $paymentMethod = $this->cielo->paymentMethod(PaymentMethod::VISA, PaymentMethod::CREDITO_A_VISTA);

        return $this->cielo->transaction(
            $holder,
            $order,
            $paymentMethod,
            'http://localhost/cielo.php',
            Transaction::AUTHORIZE_WITHOUT_AUTHENTICATION,
            true
        );
    }

    /**
     * @test
     */
    public function tokenRequest()
    {
        /* @var \PHPUnit_Framework_MockObject_MockObject|OnlyPostHttpClientInterface $client */
        $client = $this->getMock(OnlyPostHttpClientInterface::class);

        $client->expects($this->once())
            ->method('__invoke')
            ->with(
                $this->equalTo(Cielo::TEST),
                $this->logicalAnd(
                    $this->arrayHasKey('Content-Type'),
                    $this->arrayHasKey('Accept'),
                    $this->arrayHasKey('User-Agent')
                ),
                $this->arrayHasKey('mensagem')
            )
            ->willReturn(
                // @link https://developercielo.github.io/Webservice-1.5/?xml#transação-com-token
                <<<TRANSACAO
<?xml version="1.0" encoding="ISO-8859-1"?>
<retorno-token xmlns="http://ecommerce.cbmp.com.br" versao="1.2.1" id="57239017">
  <token>
    <dados-token>
      <codigo-token>TuS6LeBHWjqFFtE7S3zR052Jl/KUlD+tYJFpAdlA87E=</codigo-token>
      <status>1</status>
      <numero-cartao-truncado>455187******0183</numero-cartao-truncado>
    </dados-token>
  </token>
</retorno-token>
TRANSACAO
            );

        $cielo = new Cielo('1006993069', '25fbb997438630f30b112d033ce2e621b34f3', Cielo::TEST, $client);

        $holder = $cielo->holder('4551870000000183', 2018, 5, Holder::CVV_INFORMED, 123);
        $holder->setName('FULANO DA SILVA');

        $token = $cielo->tokenRequest($holder);

        $this->assertInstanceOf(Token::class, $token);
    }
}
