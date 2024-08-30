<?php
/**
 * ANATEL
 *
 * 12/04/2016 - criado por jaqueline.mendes - CAST
 * 26/08/2024 - Atualização por gabrielg.colab - SPASSU
 *
 */

require_once dirname(__FILE__).'/../../../SEI.php';

/**
 * ANATEL
 *
 * 08/12/2017 - criado por jaqueline.mendes@cast.com.br - CAST
 *
 */
class MdPetIndisponibilidadeDocBD extends InfraBD {

    public function __construct(InfraIBanco $objInfraIBanco){
        parent::__construct($objInfraIBanco);
    }

}