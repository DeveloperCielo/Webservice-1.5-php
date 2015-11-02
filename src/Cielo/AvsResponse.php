<?php

namespace Cielo;

class AvsResponse
{
    /**
     * @var integer
     */
    private $codigoAvsCep;

    /**
     * @var string
     */
    private $mensagemAvsCep;

    /**
     * @var integer
     */
    private $codigoAvsEnd;

    /**
     * @var string
     */
    private $mensagemAvsEnd;

    /**
     * @return string
     */
    public function getMensagemAvsCep()
    {
        return $this->mensagemAvsCep;
    }

    /**
     * @param  string $mensagemAvsCep
     * @return $this
     */
    public function setMensagemAvsCep($mensagemAvsCep)
    {
        $this->mensagemAvsCep = $mensagemAvsCep;

        return $this;
    }

    /**
     * @return int
     */
    public function getCodigoAvsCep()
    {
        return $this->codigoAvsCep;
    }

    /**
     * @param  int $codigoAvsCep
     * @return $this
     */
    public function setCodigoAvsCep($codigoAvsCep)
    {
        $this->codigoAvsCep = (int)$codigoAvsCep;

        return $this;
    }

    /**
     * @return string
     */
    public function getMensagemAvsEnd()
    {
        return $this->mensagemAvsEnd;
    }

    /**
     * @param  string $mensagemAvsEnd
     * @return $this
     */
    public function setMensagemAvsEnd($mensagemAvsEnd)
    {
        $this->mensagemAvsEnd = $mensagemAvsEnd;

        return $this;
    }

    /**
     * @return int
     */
    public function getCodigoAvsEnd()
    {
        return $this->codigoAvsEnd;
    }

    /**
     * @param  int $codigoAvsEnd
     * @return $this
     */
    public function setCodigoAvsEnd($codigoAvsEnd)
    {
        $this->codigoAvsEnd = (int)$codigoAvsEnd;

        return $this;
    }
}
