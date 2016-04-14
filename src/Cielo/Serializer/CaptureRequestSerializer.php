<?php

namespace Cielo\Serializer;

use Cielo\Capture;
use DOMDocument;

class CaptureRequestSerializer extends RequestSerializer
{

    private $amountToCapture = null;

    /**
     * amount to capture
     */
    public function setAmountToCapture($amountToCapture){
        $this->amountToCapture = $amountToCapture;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize($transaction)
    {

        libxml_use_internal_errors(true);

        $document = new DOMDocument('1.0', 'utf-8');

        $requisicaoTransacao = $this->createCaptureRequest($transaction, $document);

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
    private function createCaptureRequest($transaction, DOMDocument $document)
    {
        $requisicao = $document->createElementNS(self::NS, 'requisicao-captura');

        $requisicao->setAttribute('id', $transaction->getCaptureId());
        $requisicao->setAttribute('versao', RequestSerializer::VERSION);

        $this->createElementAndAppendWithNs($requisicao, 'tid', $transaction->tid);

        $requisicao->appendChild($this->createDadosEc($transaction, $document));

        if(!is_null($this->amountToCapture)){
            $this->createElementAndAppendWithNs($requisicao, 'valor', intval($this->amountToCapture));
        }

        return $requisicao;
    }
}
