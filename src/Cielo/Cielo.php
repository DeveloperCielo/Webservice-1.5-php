<?php

namespace Cielo;

use Cielo\Http\CurlOnlyPostHttpClient;
use Cielo\Http\OnlyPostHttpClientInterface;
use Cielo\Serializer\AuthorizationRequestSerializer;
use Cielo\Serializer\TransactionRequestSerializer;
use Cielo\Serializer\TransactionResponseUnserializer;
use Cielo\Serializer\ConsultationRequestSerializer;
use Cielo\Serializer\CancellationRequestSerializer;
use Cielo\Serializer\CaptureRequestSerializer;
use Cielo\Serializer\TokenRequestSerializer;
use Cielo\Serializer\TokenResponseUnserializer;

class Cielo
{
    /**
     * @var string
     */
    const PRODUCTION = 'https://ecommerce.cielo.com.br/servicos/ecommwsec.do';

    /**
     * @var string
     */
    const TEST = 'https://qasecommerce.cielo.com.br/servicos/ecommwsec.do';

    /**
     * @var Merchant
     */
    private $merchant;

    /**
     * @var string
     */
    private $endpoint = Cielo::PRODUCTION;

    /**
     * @var OnlyPostHttpClientInterface
     */
    private $onlyPostClient;

    /**
     * @param string                      $id
     * @param string                      $key
     * @param string                      $endpoint
     * @param OnlyPostHttpClientInterface $onlyPostClient
     */
    public function __construct(
        $id,
        $key,
        $endpoint = Cielo::PRODUCTION,
        OnlyPostHttpClientInterface $onlyPostClient = null
    ) {
        if (! filter_var($endpoint, FILTER_VALIDATE_URL)) {
            throw new \UnexpectedValueException('Endpoint inválido.');
        }

        $this->merchant       = new Merchant($id, $key);
        $this->endpoint       = $endpoint;
        $this->onlyPostClient = $onlyPostClient ?: new CurlOnlyPostHttpClient();
    }

    /**
     * @param  string      $tokenOrNumber
     * @param  null|string $expirationYear
     * @param  null|string $expirationMonth
     * @param  int         $indicator
     * @param  null|string $cvv
     * @return Holder
     */
    public function holder(
        $tokenOrNumber,
        $expirationYear = null,
        $expirationMonth = null,
        $indicator = Holder::CVV_NOT_INFORMED,
        $cvv = null
    ) {
        if (func_num_args() == 1) {
            return new Holder($tokenOrNumber);
        }

        return new Holder($tokenOrNumber, $expirationYear, $expirationMonth, $indicator, $cvv);
    }

    /**
     * @param  string      $number
     * @param  int         $total
     * @param  int         $currency
     * @param  null|string $dateTime
     * @return Order
     */
    public function order($number, $total, $currency = 986, $dateTime = null)
    {
        return new Order($number, $total, $currency, $dateTime);
    }

    /**
     * @param  string      $tidOrOrderNumber
     * @return Consultation
     */
    public function consultation($tid = null)
    {
        return new Consultation($this->merchant, $tid);
    }

    /**
     * @param  string      $tidOrOrderNumber
     * @return Cancellation
     */
    public function cancellation($tid = null)
    {
        return new Cancellation($this->merchant, $tid);
    }

    /**
     * @param  string      $tidOrOrderNumber
     * @return Capture
     */
    public function capture($tid = null)
    {
        return new Capture($this->merchant, $tid);
    }

    /**
     * @param  string     $issuer
     * @param  int        $product
     * @param  string|int $installments
     * @return PaymentMethod
     */
    public function paymentMethod($issuer, $product = PaymentMethod::CREDITO_A_VISTA, $installments = 1)
    {
        return new PaymentMethod($issuer, $product, $installments);
    }

    /**
     * @param  Holder        $holder
     * @param  Order         $order
     * @param  PaymentMethod $paymentMethod
     * @param  string        $returnURL
     * @param  int           $authorize
     * @param  bool          $capture
     * @return Transaction
     */
    public function transaction(
        Holder $holder,
        Order $order,
        PaymentMethod $paymentMethod,
        $returnURL,
        $authorize,
        $capture
    ) {
        return new Transaction($this->merchant, $holder, $order, $paymentMethod, $returnURL, $authorize, $capture);
    }

    /**
     * @param  string $message
     * @return string
     */
    private function sendHttpRequest($message)
    {
        /* @var callable|OnlyPostHttpClientInterface $sendPostRequest */
        $sendPostRequest = $this->onlyPostClient;

        return $sendPostRequest(
            $this->endpoint,
            [
                'Content-Type' => 'application/x-www-form-urlencoded; charset=utf-8',
                'Accept' => 'text/xml; charset=utf-8',
                'User-Agent' => 'PHP-SDK: 1.0'
            ],
            [
                'mensagem' => $message
            ]
        );
    }

    /**
     * @param  Transaction $transaction
     * @return Transaction
     * @throws CieloException se algum erro ocorrer com na requisição pela
     * autorização
     */
    public function authorizationRequest(Transaction $transaction)
    {
        $serializer = new AuthorizationRequestSerializer();

        $response = $this->sendHttpRequest($serializer->serialize($transaction));

        $unserializer= new TransactionResponseUnserializer($transaction);

        return $unserializer->unserialize($response);
    }

    /**
     * @param  Transaction $transaction
     * @return Transaction
     * @throws CieloException se algum erro ocorrer na requisição pela transação
     */
    public function transactionRequest(Transaction $transaction)
    {
        $serializer = new TransactionRequestSerializer();

        $response = $this->sendHttpRequest($serializer->serialize($transaction));

        $unserializer = new TransactionResponseUnserializer($transaction);

        return $unserializer->unserialize($response);
    }

     /**
     * @param  Consultation $transaction
     * @return Consultation
     * @throws CieloException se algum erro ocorrer na requisição pela transação
     */
    public function consultationRequest(Consultation $transaction)
    {

        $serializer = new ConsultationRequestSerializer();

        $response = $this->sendHttpRequest($serializer->serialize($transaction));

        $unserializer = new TransactionResponseUnserializer($transaction);

        return $unserializer->unserialize($response);
    }

    /**
     * @param  Cancellation $transaction
     * @return Cancellation
     * @throws CieloException se algum erro ocorrer na requisição pela transação
     */
    public function cancellationRequest(Cancellation $transaction)
    {

        $serializer = new CancellationRequestSerializer();

        $response = $this->sendHttpRequest($serializer->serialize($transaction));

        $unserializer = new TransactionResponseUnserializer($transaction);

        return $unserializer->unserialize($response);
    }

   /**
     * @param  Capture $transaction
     * @return Capture
     * @throws CieloException se algum erro ocorrer na requisição pela transação
     */
    public function captureRequest(Capture $transaction)
    {

        $serializer = new CaptureRequestSerializer();

        $response = $this->sendHttpRequest($serializer->serialize($transaction));

        $unserializer = new TransactionResponseUnserializer($transaction);

        return $unserializer->unserialize($response);
    }

    /**
     * @param  Holder $holder
     * @return Token
     * @throws CieloException se algum erro ocorrer na requisição pela transação
     */
    public function tokenRequest(Holder $holder)
    {
        $serializer = new TokenRequestSerializer($this->merchant);

        $response = $this->sendHttpRequest($serializer->serialize($holder));

        $unserializer = new TokenResponseUnserializer;

        return $unserializer->unserialize($response);
    }
}
