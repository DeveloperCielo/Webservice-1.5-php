<?php

namespace Cielo;

class Consultation
{

    /**
     * @var string
     */
    public $tid;

    /**
     * @var Order
     */
    private $order;

    /**
     * @var PaymentMethod
     */
    private $paymentMethod;

    /**
     * @var Merchant
     */
    private $merchant;

    /**
     * @var string
     */
    private $pan;

    /**
     * @var int
     */
    private $status;
    
    /**
     * @var Authorization
     */
    private $authorization;

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
     * @param string        $tid
     */
    public function __construct(
        Merchant $merchant,
        $tid
    ) {
        $this->setMerchant($merchant);
        $this->order = new Order;
        $this->paymentMethod = new PaymentMethod;
        $this->setTidOrOrderNumber($tid);
        
    }

    /**
     * @param Merchant $merchant
     */
    public function setMerchant(Merchant $merchant)
    {
        $this->merchant = $merchant;
    }

    /**
     * @param  string $tid
     * @throws \UnexpectedValueException se o número do tid não estiver de 1
     * à 20 caracteres
     */
    public function setTidOrOrderNumber($tid = null)
    {
        
        if (strlen($tid) < 1 || strlen($tid) > 20) {
            throw new \UnexpectedValueException("O string de consulta (Tid) deve ter entre 1 à 20 caracteres", 1);
        }

        $this->tid = $tid;
            
        
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
     * @param Order $order
     */
    public function setOrder(Order $order)
    {
        $this->order = $order;
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
     * @param Authorization $authorization
     */
    public function setAuthorization(Authorization $authorization)
    {
        $this->authorization = $authorization;
    }

    /**
     * @param Authorization $authorization
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
     * @param Token $token
     */
    public function setToken(Token $token)
    {
        $this->token = $token;
    }

    /**
     * @return Consultation Id
     */
    public function getConsultationId()
    {
        return md5(date("YmdHisu"));
    }

    /**
     * @return Merchant
     */
    public function getMerchant()
    {
        return $this->merchant;
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
    
}
