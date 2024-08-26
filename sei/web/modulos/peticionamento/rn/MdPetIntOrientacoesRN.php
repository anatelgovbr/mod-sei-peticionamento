<?
/**
* ANATEL
*
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetIntOrientacoesRN extends InfraRN { 
	
	public static $ID_FIXO_INT_ORIENTACOES = 1;
	
	public function __construct() {
		parent::__construct ();
	}
	
	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance ();
	}
	
	
   /**
	 * Short description of method cadastrarControlado
	 *
	 * @access protected
	 * @param  $objMdPetIntOrientacoesDTO
	 * @return mixed
	 */
	protected function cadastrarControlado(MdPetIntOrientacoesDTO $objMdPetIntOrientacoesDTO) {
		try {
			// Valida Permissao
			SessaoSEI::getInstance ()->validarAuditarPermissao ('md_pet_tipo_processo_cadastrar_orientacoes', __METHOD__, $objMdPetIntOrientacoesDTO );

			// Regras de Negocio
			$objInfraException = new InfraException();
			
			$objInfraException->lancarValidacoes();
	
                        $objMdPetIntOrientacoesBD = new MdPetIntOrientacoesBD($this->getObjInfraIBanco());
			$objMdPetIntOrientacoesDTO = $objMdPetIntOrientacoesBD->cadastrar($objMdPetIntOrientacoesDTO);
			
		//	$rs = $this->getObjInfraIBanco ()->executarSql ( $sql );
	
			return $objMdPetTpProcessoOrientacoesDTO;
		} catch ( Exception $e ) {
			throw new InfraException ('Erro cadastrando Orientações do Tipo Destinatário Peticionamento.', $e );
		}
	}
	
	/**
	 * Short description of method listarConectado
	 *
	 * @access protected
	 * @param  $objMdPetIntOrientacoesDTO
	 * @return mixed
	 */
	protected function listarConectado(MdPetIntOrientacoesDTO $objMdPetIntOrientacoesDTO) {
		try {
	
			//Valida Permissao
	
			//Regras de Negocio
			//$objInfraException = new InfraException();
	
			//$objInfraException->lancarValidacoes();
	
			$objMdPetIntOrientacoesBD = new MdPetIntOrientacoesBD($this->getObjInfraIBanco());
			$ret = $objMdPetIntOrientacoesBD->listar($objMdPetIntOrientacoesDTO);
	
			//Auditoria
	
			return $ret;
	
		}catch(Exception $e){
			throw new InfraException('Erro listando Orientações do Tipo Destinatário.',$e);
		}
	}
	
	
	/**
	 * Short description of method cadastrarControlado
	 *
	 * @access protected
	 * @param  $objMdPetIntOrientacoesDTO
	 * @return mixed
	 */
	protected function alterarControlado(MdPetIntOrientacoesDTO $objMdPetIntOrientacoesDTO) {
		try {
			// Valida Permissao
			SessaoSEI::getInstance ()->validarAuditarPermissao ('md_pet_tipo_processo_cadastrar_orientacoes', __METHOD__, $objMdPetIntOrientacoesDTO );

			// Regras de Negocio
			$objInfraException = new InfraException();
				
			$objInfraException->lancarValidacoes();
	
			$objMdPetIntOrientacoesBD = new MdPetIntOrientacoesBD($this->getObjInfraIBanco());
			$ret = $objMdPetIntOrientacoesBD->alterar($objMdPetIntOrientacoesDTO);
			
			return $ret;
		} catch ( Exception $e ) {
			throw new InfraException ('Erro alterando Orientações do Tipo Destinatário Peticionamento.', $e );
		}
	}

}
?>