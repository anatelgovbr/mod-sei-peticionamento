<?
/**
* ANATEL
*
* 21/06/2019 - criado por renato.monteiro - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetRelVincRepProtocRN extends InfraRN {
	
	public function __construct() {
		parent::__construct ();
	}
	
	
	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance ();
	}
	
	
	protected function listarConectado(MdPetRelVincRepProtocDTO $objDTO) {
	
		try {
							
			$objInfraException = new InfraException();						
			$objBD = new MdPetRelVincRepProtocBD($this->getObjInfraIBanco());
			$ret = $objBD->listar($objDTO);	
			return $ret;
			
		} catch (Exception $e) {
			throw new InfraException ('Erro listando Rel Vinculo Representante Poder.', $e);
		}
	}
	
	
	protected function consultarConectado(MdPetRelVincRepProtocDTO $objDTO) {
	
		try {
	
			$objBD = new MdPetRelVincRepProtocBD($this->getObjInfraIBanco());
			$ret = $objBD->consultar( $objDTO );
			return $ret;
				
		} catch (Exception $e) {
			throw new InfraException ('Erro consultando Rel Vinculo Representante Poder.', $e);
		}
	}
	
	protected function cadastrarControlado(MdPetRelVincRepProtocDTO $objDTO) {
		
		try {
			
			$objInfraException = new InfraException();
			$objBD = new MdPetRelVincRepProtocBD($this->getObjInfraIBanco());
			$ret = $objBD->cadastrar($objDTO);	
			return $ret;
			
		} catch ( Exception $e ) {
			throw new InfraException ('Erro Cadastrando Rel Vinculo Representante Poder.', $e );
		}
	}

	

	protected function alterarControlado(MdPetRelVincRepProtocDTO $objDTO) {
		
		try {
			
			$objInfraException = new InfraException();
			$objBD = new MdPetRelVincRepProtocBD($this->getObjInfraIBanco());
			$ret = $objBD->alterar($objDTO);	
			return $ret;
			
		} catch ( Exception $e ) {
			throw new InfraException ('Erro alterando Rel Vinculo Representante Poder.', $e );
		}
	}

	
	
	

}
?>