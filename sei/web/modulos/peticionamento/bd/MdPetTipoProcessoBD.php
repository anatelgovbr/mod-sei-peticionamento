<?
/**
* ANATEL
*
* 15/04/2016 - criado por jaqueline.mendes - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetTipoProcessoBD extends InfraBD {

  public function __construct(InfraIBanco $objInfraIBanco){
  	 parent::__construct($objInfraIBanco);
  }

}
?>