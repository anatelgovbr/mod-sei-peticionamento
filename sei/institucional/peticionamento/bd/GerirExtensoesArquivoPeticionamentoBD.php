<?
/**
* ANATEL
*
* 04/05/2016 - criado por alan.campos - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class GerirExtensoesArquivoPeticionamentoBD extends InfraBD {

  public function __construct(InfraIBanco $objInfraIBanco){
  	 parent::__construct($objInfraIBanco);
  }

}
?>