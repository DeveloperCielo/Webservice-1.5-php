<?php

namespace Cielo\Serializer;

use Cielo\Transaction;
use DOMDocument;

class TransactionRequestSerializer extends RequestSerializer
{
    /**
     * {@inheritDoc}
     */
    public function serialize($transaction)
    {
        libxml_use_internal_errors(true);

        $document = new DOMDocument('1.0', 'utf-8');

        $requisicaoTransacao = $this->createRequisicaoTransacao($transaction, $document);

        $document->appendChild($requisicaoTransacao);

        if (is_file('ecommerce.xsd') && is_readable('ecommerce.xsd')) {
            $document->schemaValidate('ecommerce.xsd');
        }

        $exception = new \DomainException('Erro na criação do XML');
        $count = 0;

        foreach (libxml_get_errors() as $error) {
            $exception = new \DomainException($error->message, $error->code, $exception);
            ++$count;
        }

        libxml_clear_errors();

        if ($count) {
            echo $document->saveXML();
            throw $exception;
        }

        return $document->saveXML();
    }

    /**
     * @param \DOMElement $root
     * @param string      $name
     * @param string      $value
     * @param string      $namespace
     */
    private function createElementAndAppendWithNs(\DOMElement $root, $name, $value, $namespace = self::NS)
    {
        $root->appendChild(new \DOMElement($name, $value, $namespace));
    }

    /**
     * @param  Transaction $transaction
     * @param  DOMDocument $document
     * @return \DOMElement
     */
    private function createRequisicaoTransacao($transaction, DOMDocument $document)
    {
        $requisicao = $document->createElementNS(self::NS, 'requisicao-transacao');

        $requisicao->setAttribute('id', $transaction->getOrder()->getNumber());
        $requisicao->setAttribute('versao', RequestSerializer::VERSION);

        $requisicao->appendChild($this->createDadosEc($transaction, $document));
        $requisicao->appendChild($this->createDadosPortador($transaction, $document));
        $requisicao->appendChild($this->createDadosPedido($transaction, $document));
        $requisicao->appendChild($this->createFormaPagamento($transaction, $document));

        $this->createElementAndAppendWithNs($requisicao, 'url-retorno', $transaction->getReturnURL());
        $this->createElementAndAppendWithNs($requisicao, 'autorizar', $transaction->getAuthorize());
        $this->createElementAndAppendWithNs($requisicao, 'capturar', $transaction->getCapture() ? 'true' : 'false');
        $this->createElementAndAppendWithNs($requisicao, 'campo-livre', $transaction->getFreeField());

        if ($transaction->getBin() !== null) {
            $this->createElementAndAppendWithNs($requisicao, 'bin', $transaction->getBin());
        }

        $this->createElementAndAppendWithNs(
            $requisicao,
            'gerar-token',
            $transaction->getGenerateToken() ? 'true' : 'false'
        );

        $avsXML = $transaction->getAvs();

        if (! empty($avsXML)) {
            $avs = $document->createElementNS(self::NS, 'avs');

            $avs->appendChild($document->createCDATASection($avsXML));

            $requisicao->appendChild($avs);
        }

        return $requisicao;
    }

    /**
     * @param  Transaction $transaction
     * @param  DOMDocument $document
     * @return \DOMElement
     */
    private function createDadosPortador(Transaction $transaction, DOMDocument $document)
    {
        $holder = $transaction->getHolder();
        $holderName = $holder->getName();
        $expiration = $holder->getExpiration();

        $token = $holder->getToken();

        $dadosPortador = $document->createElementNS(self::NS, 'dados-portador');

        $this->createElementAndAppendWithNs($dadosPortador, 'numero', $holder->getCreditCardNumber());
        
        if (!empty($expiration)) {
            $this->createElementAndAppendWithNs($dadosPortador, 'validade', $expiration);
        }

        $this->createElementAndAppendWithNs($dadosPortador, 'indicador', $holder->getCVVIndicator());
        $this->createElementAndAppendWithNs($dadosPortador, 'codigo-seguranca', $holder->getCVV());

        if (!empty($holderName)) {
            $this->createElementAndAppendWithNs($dadosPortador, 'nome-portador', $holderName);
        }

        $this->createElementAndAppendWithNs($dadosPortador, 'token', empty($token) ? null : $token);

        return $dadosPortador;
    }

    /**
     * @param  Transaction $transaction
     * @param  DOMDocument $document
     * @return \DOMElement
     */
    private function createDadosPedido(Transaction $transaction, DOMDocument $document)
    {
        $order = $transaction->getOrder();

        $dadosPedido = $document->createElementNS(self::NS, 'dados-pedido');

        $this->createElementAndAppendWithNs($dadosPedido, 'numero', $order->getNumber());
        $this->createElementAndAppendWithNs($dadosPedido, 'valor', $order->getTotal());
        $this->createElementAndAppendWithNs($dadosPedido, 'moeda', $order->getCurrency());
        $this->createElementAndAppendWithNs($dadosPedido, 'data-hora', $order->getDateTime());
        $this->createElementAndAppendWithNs($dadosPedido, 'descricao', $order->getDescription());
        $this->createElementAndAppendWithNs($dadosPedido, 'idioma', $order->getLanguage());
        $this->createElementAndAppendWithNs($dadosPedido, 'taxa-embarque', (int) $order->getShipping());
        $this->createElementAndAppendWithNs($dadosPedido, 'soft-descriptor', $order->getSoftDescriptor());

        return $dadosPedido;
    }

    /**
     * @param  Transaction $transaction
     * @param  DOMDocument $document
     * @return \DOMElement
     */
    private function createFormaPagamento(Transaction $transaction, DOMDocument $document)
    {
        $paymentMethod = $transaction->getPaymentMethod();

        $formaPagamento = $document->createElementNS(self::NS, 'forma-pagamento');

        $this->createElementAndAppendWithNs($formaPagamento, 'bandeira', $paymentMethod->getIssuer());
        $this->createElementAndAppendWithNs($formaPagamento, 'produto', $paymentMethod->getProduct());
        $this->createElementAndAppendWithNs($formaPagamento, 'parcelas', $paymentMethod->getInstallments());

        return $formaPagamento;
    }
}
