<?php

namespace Cielo\Serializer;

use Cielo\Holder;
use Cielo\Merchant;
use DOMDocument;

class TokenRequestSerializer extends RequestSerializer
{
    /**
     * @var Merchant $merchant
     */
    protected $merchant;

    /**
     * @param Merchant $merchant
     */
    public function __construct($merchant)
    {
        $this->merchant = $merchant;
    }

    /**
     * {@inheritDoc}
     */
    public function serialize($holder)
    {
        libxml_use_internal_errors(true);

        $document = new DOMDocument('1.0', 'utf-8');

        $requisicaoToken = $this->createRequisicaoToken($holder, $document);

        $document->appendChild($requisicaoToken);

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
     * @param  Holder $holder
     * @param  DOMDocument $document
     * @return \DOMElement
     */
    private function createRequisicaoToken($holder, DOMDocument $document)
    {
        $requisicao = $document->createElementNS(self::NS, 'requisicao-token');

        $requisicao->setAttribute('id', "8fc889c7-004f-42f1-963a-31aa26f75e5c");
        $requisicao->setAttribute('versao', RequestSerializer::VERSION);

        $requisicao->appendChild($this->createDadosEc($this->merchant, $document));
        $requisicao->appendChild($this->createDadosPortador($holder, $document));

        return $requisicao;
    }

    /**
     * @param  Holder $holder
     * @param  DOMDocument $document
     * @return \DOMElement
     */
    private function createDadosPortador(Holder $holder, DOMDocument $document)
    {
        $holderName = $holder->getName();

        $dadosPortador = $document->createElementNS(self::NS, 'dados-portador');

        $this->createElementAndAppendWithNs($dadosPortador, 'numero', $holder->getCreditCardNumber());
        $this->createElementAndAppendWithNs($dadosPortador, 'validade', $holder->getExpiration());
        $this->createElementAndAppendWithNs($dadosPortador, 'indicador', $holder->getCVVIndicator());
        $this->createElementAndAppendWithNs($dadosPortador, 'codigo-seguranca', $holder->getCVV());

        if (!empty($holderName)) {
            $this->createElementAndAppendWithNs($dadosPortador, 'nome-portador', $holderName);
        }

        return $dadosPortador;
    }

    /**
     * @param  Merchant $merchant
     * @param  DOMDocument $document
     * @return \DOMElement
     */
    protected function createDadosEc($merchant, DOMDocument $document)
    {
        $dadosEc = $document->createElementNS(self::NS, 'dados-ec');
        $numero = $document->createElementNS(self::NS, 'numero', $merchant->getAffiliationId());
        $chave = $document->createElementNS(self::NS, 'chave', $merchant->getAffiliationKey());

        $dadosEc->appendChild($numero);
        $dadosEc->appendChild($chave);

        return $dadosEc;
    }
}
