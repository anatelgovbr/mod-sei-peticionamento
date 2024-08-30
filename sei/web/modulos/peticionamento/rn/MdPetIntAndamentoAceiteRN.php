<?php


/**
 * ANATEL
 *
 * 28/03/2017 - criado por jaqueline.mendes - CAST
 * 26/08/2024 - Atualização por gabrielg.colab - SPASSU
 *
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntAndamentoRespostaRN extends InfraRN {

    public function __construct() {
        parent::__construct ();
    }

    protected function inicializarObjInfraIBanco() {
        return BancoSEI::getInstance ();
    }

}