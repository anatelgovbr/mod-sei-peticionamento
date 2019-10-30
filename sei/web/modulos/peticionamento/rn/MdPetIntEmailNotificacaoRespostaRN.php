<?php

/**
 * ANATEL
 *
 * 30/03/2017 - criado por jaqueline.mendes - CAST
 *
 */

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntEmailNotificacaoRespostaRN extends InfraRN {

    public function __construct() {
        parent::__construct ();
    }

    protected function inicializarObjInfraIBanco() {
        return BancoSEI::getInstance ();
    }

}