<?php

namespace Cielo\Serializer;

use Cielo\CieloException;
use Cielo\Token;
use DOMDocument;
use DOMXPath;

class TokenResponseUnserializer
{
    const NS = 'http://ecommerce.cbmp.com.br';

    /**
     * @var DOMXPath
     */
    private $xpath;

    /**
     * @param  string $xml
     * @return Token
     * @throws CieloException se houver alguma mensagem de erro envolvida no XML
     */
    public function unserialize($xml)
    {
        $document = new DOMDocument('1.0', 'utf-8');

        if (empty($xml)) {
            throw new \UnexpectedValueException('Houve um erro de comunicação com o servidor, tente novamente');
        }

        $document->loadXML($xml);

        $this->xpath = new DOMXpath($document);
        $this->xpath->registerNamespace('c', TokenResponseUnserializer::NS);

        if (($code = $this->xpath->query('/c:erro/c:codigo')->item(0)) !== null) {
            $message = $this->xpath->query('/c:erro/c:mensagem')->item(0)->nodeValue;

            throw new CieloException($message, $code->nodeValue);
        }

        return $this->readToken();
    }

    /**
     * @param  string $query
     * @return string
     */
    private function getValue($query)
    {
        $node = $this->xpath->query($query)->item(0);

        if ($node !== null) {
            return $node->nodeValue;
        }
    }

    /**
     * Read Token
     *
     * @return  Token
     */
    private function readToken()
    {
        $token = new Token();

        $token->setCode($this->getValue('//c:retorno-token/c:token/c:dados-token/c:codigo-token'));
        $token->setStatus($this->getValue('//c:retorno-token/c:token/c:dados-token/c:status'));
        $token->setNumero($this->getValue('//c:retorno-token/c:token/c:dados-token/c:numero-cartao-truncado'));

        return $token;
    }
}
