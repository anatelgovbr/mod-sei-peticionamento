<?
/**
* ANATEL
*
* 01/07/2016 - criado por marcelo.bezerra - CAST
* 26/08/2024 - Atualização por gabrielg.colab - SPASSU
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetRelReciboDocumentoAnexoBD extends InfraBD {

  public function __construct(InfraIBanco $objInfraIBanco){
  	 parent::__construct($objInfraIBanco);
  }

}
?>