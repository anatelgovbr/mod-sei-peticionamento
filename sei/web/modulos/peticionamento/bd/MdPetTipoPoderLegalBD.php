<?
/**
* ANATEL 
*
* 21/06/2019 - criado por renato.monteiro - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetTipoPoderLegalBD extends InfraBD {

  public function __construct(InfraIBanco $objInfraIBanco){
  	 parent::__construct($objInfraIBanco);
  }

  
}
?>