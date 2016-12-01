<?
/**
* ANATEL
*
* 29/04/2016 - criado por jaqueline.mendes - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class IndisponibilidadeAnexoPeticionamentoBD extends InfraBD {

  public function __construct(InfraIBanco $objInfraIBanco){
  	 parent::__construct($objInfraIBanco);
  }

}
?>