<?
/**
* ANATEL
*
* 21/06/2019 - criado por renato.monteiro - CAST
* 26/08/2024 - Atualização por gabrielg.colab - SPASSU 
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetRelVincRepTpPoderRN extends InfraRN {
	
	public function __construct() {
		parent::__construct ();
	}
	
	
	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance ();
	}
	
	
	protected function listarConectado(MdPetRelVincRepTpPoderDTO $objDTO) {
	
		try {
							
			$objInfraException = new InfraException();						
			$objBD = new MdPetRelVincRepTpPoderBD($this->getObjInfraIBanco());
			$ret = $objBD->listar($objDTO);	
			return $ret;
			
		} catch (Exception $e) {
			throw new InfraException ('Erro listando Vinculo Representante Tipo Poder .', $e);
		}
	}
	
	
	protected function consultarConectado(MdPetRelVincRepTpPoderDTO $objDTO) {
	
		try {
	
			$objBD = new MdPetRelVincRepTpPoderBD($this->getObjInfraIBanco());
			$ret = $objBD->consultar( $objDTO );
			return $ret;
				
		} catch (Exception $e) {
			throw new InfraException ('Erro listando Vinculo Representante Tipo Poder.', $e);
		}
	}
	
	protected function cadastrarControlado(MdPetRelVincRepTpPoderDTO $objDTO) {
		
		try {
			
			$objInfraException = new InfraException();
			$objBD = new MdPetRelVincRepTpPoderBD($this->getObjInfraIBanco());
			$ret = $objBD->cadastrar($objDTO);	
			return $ret;
			
		} catch ( Exception $e ) {
			throw new InfraException ('Erro cadastrando Vinculo Representante Tipo Poder.', $e );
		}
	}


	
	protected function contarConectado(MdPetRelVincRepTpPoderDTO $objDTO) {

		try {
			$objBD = new MdPetRelVincRepTpPoderBD($this->getObjInfraIBanco());
			return $objBD->contar($objDTO);

		} catch ( Exception $e ) {
			throw new InfraException ('Erro contando Vinculo Representante Tipo Poder, ', $e);
		}
	}

	protected function excluirConectado(MdPetRelVincRepTpPoderDTO $objDTO) {

		try {
			$objBD = new MdPetRelVincRepTpPoderBD($this->getObjInfraIBanco());
			return $objBD->excluir($objDTO);

		} catch ( Exception $e ) {
			throw new InfraException ('Erro excluindo Vinculo Representante Tipo Poder, ', $e);
		}
	}

	
	
}
?>