<?php
require_once dirname(__FILE__).'/../../../SEI.php';
/**
 * Created by PhpStorm.
 * User: jhon.carvalho
 * Date: 27/12/2017
 * Time: 10:39
 */
class MdPetVincRelSerieBD extends InfraBD
{
    public function __construct(InfraIBanco $objInfraIBanco){
        parent::__construct($objInfraIBanco);
    }
    
}