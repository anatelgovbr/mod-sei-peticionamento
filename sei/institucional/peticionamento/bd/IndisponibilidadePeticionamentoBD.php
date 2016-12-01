<?
/**
* ANATEL
*
* 12/04/2016 - criado por jaqueline.mendes - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class IndisponibilidadePeticionamentoBD extends InfraBD {

  public function __construct(InfraIBanco $objInfraIBanco){
  	 parent::__construct($objInfraIBanco);
  }

}
?>