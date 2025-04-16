<?
/**
* ANATEL
*
* 25/11/2016 - criado por marcelo.bezerra - CAST
* 26/08/2024 - Atualização por gabrielg.colab - SPASSU 
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntercorrenteReciboRN extends InfraRN { 
	
	public function __construct() {
		parent::__construct ();
	}
	
	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance ();
	}

}
