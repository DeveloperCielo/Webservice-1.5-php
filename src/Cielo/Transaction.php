<?php

namespace Cielo;

class Transaction
{
    /**
     * @var int
     */
    const ONLY_AUTHENTICATE = 0;

    /**
     * @var int
     */
    const AUTHORIZE_IF_AUTHENTICATED = 1;

    /**
     * @var int
     */
    const AUTHORIZE = 2;

    /**
     * @var int
     */
    const AUTHORIZE_WITHOUT_AUTHENTICATION = 3;

    /**
     * @var int
     */
    const RECURRENCE = 4;

    /**
     * @var string
     */
    private $tid;

    /**
     * @var string
     */
    private $pan;

    /**
     * @var int
     */
    private $status;

    /**
     * @var string
     */
    private $authenticationUrl;

    /**
     * @var Authentication
     */
    private $authentication;

    /**
     * @var Authorization
     */
    private $authorization;

    /**
     * @var Merchant
     */
    private $merchant;

    /**
     * @var Holder
     */
    private $holder;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var PaymentMethod
     */
    private $paymentMethod;

    /**
     * @var string
     */
    private $returnURL;

    /**
     * @var int
     */
    private $authorize;

    /**
     * @var Capture
     */
    private $capture;

    /**
     * @var string
     */
    private $freeField;

    /**
     * @var int
     */
    private $bin;

    /**
     * @var bool
     */
    private $generateToken = false;

    /**
     * @var string
     */
    private $avs;

    /**
     * @var string
     */
    private $avsResponse;

    /**
     * @var Token
     */
    private $token;

    /**
     * @var CaptureInformation
     */
    private $captureInformation;

    /**
     * @var CancellationInformation
     */
    private $cancellationInformation;

    /**
     * @param Merchant      $merchant
     * @param Holder        $holder
     * @param Order         $order
     * @param PaymentMethod $paymentMethod
     * @param string        $returnURL
     * @param bool          $authorize
     * @param bool          $capture
     */
    public function __construct(
        Merchant $merchant,
        Holder $holder,
        Order $order,
        PaymentMethod $paymentMethod,
        $returnURL,
        $authorize,
        $capture
    ) {
        $this->setMerchant($merchant);
        $this->setHolder($holder);
        $this->setOrder($order);
        $this->setPaymentMethod($paymentMethod);
        $this->setReturnURL($returnURL);
        $this->setAuthorize($authorize);
        $this->setCapture($capture);
    }

    /**
     * @return string
     */
    public function getTid()
    {
        return $this->tid;
    }

    /**
     * @return string
     */
    public function getPan()
    {
        return $this->pan;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return Authentication
     */
    public function getAuthentication()
    {
        return $this->authentication;
    }

    /**
     * @return Authorization
     */
    public function getAuthorization()
    {
        return $this->authorization;
    }

    /**
     * @return CaptureInformation
     */
    public function getCaptureInformation()
    {
        return $this->captureInformation;
    }

    /**
     * @return CancellationInformation
     */
    public function getCancellationInformation()
    {
        return $this->cancellationInformation;
    }

    /**
     * @return string
     */
    public function getAuthenticationURL()
    {
        return $this->authenticationUrl;
    }

    /**
     * @return Merchant
     */
    public function getMerchant()
    {
        return $this->merchant;
    }

    /**
     * @return Holder
     */
    public function getHolder()
    {
        return $this->holder;
    }

    /**
     * @return Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @return PaymentMethod
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @return string
     */
    public function getReturnURL()
    {
        return $this->returnURL;
    }

    /**
     * @return int
     */
    public function getAuthorize()
    {
        return $this->authorize;
    }

    /**
     * @return bool
     */
    public function getCapture()
    {
        return $this->capture;
    }

    /**
     * @return string
     */
    public function getFreeField()
    {
        return $this->freeField;
    }

    /**
     * @return int
     */
    public function getBin()
    {
        return $this->bin;
    }

    /**
     * @return bool
     */
    public function getGenerateToken()
    {
        return $this->generateToken;
    }

    /**
     * @return string
     */
    public function getAvs()
    {
        return $this->avs;
    }

    /**
     * @return string
     */
    public function getAvsResponse()
    {
        return $this->avsResponse;
    }

    /**
     * @return Token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param Merchant $merchant
     */
    public function setMerchant(Merchant $merchant)
    {
        $this->merchant = $merchant;
    }

    /**
     * @param Holder $holder
     */
    public function setHolder(Holder $holder)
    {
        $this->holder = $holder;

        if (($bin = substr($holder->getCreditCardNumber(), 0, 6)) !== false) {
            $this->setBin($bin);
        }
    }

    /**
     * @param Order $order
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @param PaymentMethod $paymentMethod
     */
    public function setPaymentMethod(PaymentMethod $paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * @param string $returnURL
     */
    public function setReturnURL($returnURL)
    {
        if ($returnURL !== null && !filter_var($returnURL, FILTER_VALIDATE_URL)) {
            throw new \UnexpectedValueException('URL de retorno inválida');
        }

        $this->returnURL = $returnURL;
    }

    /**
     * @param int $authorize
     */
    public function setAuthorize($authorize)
    {
        switch ($authorize) {
            case Transaction::ONLY_AUTHENTICATE:
            case Transaction::AUTHORIZE_IF_AUTHENTICATED:
            case Transaction::AUTHORIZE:
            case Transaction::AUTHORIZE_WITHOUT_AUTHENTICATION:
            case Transaction::RECURRENCE:
                $this->authorize = $authorize;
                break;
            default:
                throw new \UnexpectedValueException('Indicador de autorização inválido');
        }
    }

    /**
     * @param bool $capture
     */
    public function setCapture($capture)
    {
        if (! is_bool($capture)) {
            throw new \UnexpectedValueException('Indicador de captura deve ser um boolean');
        }

        $this->capture = $capture;
    }

    /**
     * @param string $freeField
     */
    public function setFreeField($freeField)
    {
        if (strlen($freeField) > 128) {
            throw new \UnexpectedValueException('O campo livre deve ter, no máximo, 128 caracteres');
        }

        $this->freeField = $freeField;
    }

    /**
     * @param string $bin
     */
    public function setBin($bin)
    {
        if (! is_numeric($bin) || strlen($bin) != 6) {
            throw new \UnexpectedValueException(
                'O campo bin deve ser informado com os 6 primeiros dígitos do número do cartão'
            );
        }

        $this->bin = $bin;
    }

    /**
     * @param bool $generateToken
     */
    public function setGenerateToken($generateToken)
    {
        if (! is_bool($generateToken)) {
            throw new \UnexpectedValueException('O campo generate-token deve ser um boolean');
        }

        $this->generateToken = $generateToken;
    }

    /**
     * @param string $avs
     */
    public function setAvs($avs)
    {
        $this->avs = $avs;
    }

    /**
     * @param string $avsResponse
     */
    public function setAvsResponse($avsResponse)
    {
        $this->avsResponse = $avsResponse;
    }

    /**
     * @param string $tid
     */
    public function setTid($tid)
    {
        $this->tid = $tid;
    }

    /**
     * @param string $pan
     */
    public function setPan($pan)
    {
        $this->pan = $pan;
    }

    /**
     * @param $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @param string $authenticationUrl
     */
    public function setAuthenticationURL($authenticationUrl)
    {
        $this->authenticationUrl = $authenticationUrl;
    }

    /**
     * @param Authentication $authentication
     */
    public function setAuthentication(Authentication $authentication)
    {
        $this->authentication = $authentication;
    }

    /**
     * @param CaptureInformation $captureInformation
     */
    public function setCaptureInformation(CaptureInformation $captureInformation)
    {
        $this->captureInformation = $captureInformation;
    }

    /**
     * @param CancellationInformation $cancellationInformation
     */
    public function setCancellationInformation(CancellationInformation $cancellationInformation)
    {
        $this->cancellationInformation = $cancellationInformation;
    }

    /**
     * @param Authorization $authorization
     */
    public function setAuthorization(Authorization $authorization)
    {
        $this->authorization = $authorization;
    }

    /**
     * @param Token $token
     */
    public function setToken(Token $token)
    {
        $this->token = $token;
    }
}
