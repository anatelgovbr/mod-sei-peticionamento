<?
/**
* ANATEL
*
* 18/05/2016 - criado por jaqueline.mendes - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetRelTpProcSerieRN extends InfraRN{ 

	public static $DOC_COMPLEMENTAR = 'C';
	public static $DOC_ESSENCIAL = 'E';
	
	public function __construct() {
		parent::__construct ();
	}
	
	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance ();
	}
	
	/**
	 * Short description of method listarConectado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param  $objMdPetTipoProcessoDTO
	 * @return mixed
	 */
	protected function listarConectado(MdPetRelTpProcSerieDTO $objMdPetRelTpProcSerieDTO) {
	
		try {
			
			$objMdPetRelTpProcSerieBD = new MdPetRelTpProcSerieBD($this->getObjInfraIBanco());
			$ret = $objMdPetRelTpProcSerieBD->listar($objMdPetRelTpProcSerieDTO);
	     	return $ret;
	     	
		} catch (Exception $e) {
			throw new InfraException ('Erro listando Relacionamento de Tipo de Processo e Série Peticionamento.', $e);
		}
	}
		
/**
	 * Short description of method consultarConectado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param  $objMdPetTipoProcessoDTO
	 * @return mixed
	 */
	protected function consultarConectado(MdPetTipoProcessoDTO $objMdPetTipoProcessoDTO) {
		try {
			
			// Valida Permissao
			SessaoSEI::getInstance ()->validarAuditarPermissao ('md_pet_tipo_processo_alterar', __METHOD__, $objMdPetTipoProcessoDTO );
			
		    $objMdPetTipoProcessoBD = new MdPetTipoProcessoBD($this->getObjInfraIBanco());
			$ret = $objMdPetTipoProcessoBD->consultar($objMdPetTipoProcessoDTO);
			
			return $ret;
		} catch ( Exception $e ) {
			throw new InfraException('Erro consultando Tipo de Processo Peticionamento.', $e);
		}
	}
	
	
	
	/**
	 * Short description of method desativarControlado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param  $arrMdPetTipoProcessoDTO
	 * @return void
	 */
	protected function desativarControlado($arrMdPetTipoProcessoDTO) {
	
		try {
				
			SessaoSEI::getInstance ()->validarAuditarPermissao('md_pet_tipo_processo_desativar');
				
			$objMdPetTipoProcessoBD = new MdPetTipoProcessoBD($this->getObjInfraIBanco());
			for($i = 0; $i < count($arrMdPetTipoProcessoDTO); $i ++) {
				$objMdPetTipoProcessoBD->desativar($arrMdPetTipoProcessoDTO[$i]);
			}
				
		} catch(Exception $e) {
			throw new InfraException ('Erro desativando Tipo de Processo Peticionamento.', $e );
		}
	}
	
	/**
	 * Short description of method reativarControlado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param  $arrMdPetTipoProcessoDTO
	 * @return void
	 */
	protected function reativarControlado($arrMdPetTipoProcessoDTO) {
	
		try {
	
			SessaoSEI::getInstance ()->validarAuditarPermissao('md_pet_tipo_processo_reativar');
	
			$objMdPetTipoProcessoBD = new MdPetTipoProcessoBD($this->getObjInfraIBanco());
			for($i = 0; $i < count($arrMdPetTipoProcessoDTO); $i ++) {
				$objMdPetTipoProcessoBD->reativar($arrMdPetTipoProcessoDTO[$i]);
			}
	
		} catch(Exception $e) {
			throw new InfraException ('Erro reativando Tipo de Processo Peticionamento.', $e );
		}
	}
	
	
	/**
	 * Short description of method excluirControlado
	 *
	 * @access protected
	 * @author Alan Campos <alan.campos@castgroup.com.br>
	 * @param  $arrMdPetRelTpProcSerieDTO
	 * @return void
	 */
	protected function excluirControlado($arrMdPetRelTpProcSerieDTO) {
	
		try {
	
			$objMdPetRelTpProcSerieBD = new MdPetRelTpProcSerieBD($this->getObjInfraIBanco());
			for($i = 0; $i < count($arrMdPetRelTpProcSerieDTO); $i ++) {
				$objMdPetRelTpProcSerieBD->excluir($arrMdPetRelTpProcSerieDTO[$i]);
			}
	
		} catch(Exception $e) {
			throw new InfraException ('Erro excluindo Tipo de Processo Peticionamento.', $e );
		}
	}
	
	/**
	 * Short description of method cadastrarControlado
	 *
	 * @access protected
	 * @author Alan Campos <alan.campos@castgroup.com.br>
	 * @param  $objMdPetRelTpProcSerieDTO
	 * @return mixed
	 */
	protected function cadastrarControlado(MdPetRelTpProcSerieDTO $objMdPetRelTpProcSerieDTO) {
		try {
			// Valida Permissao
			$objMdPetRelTpProcSerieBD = new MdPetRelTpProcSerieBD($this->getObjInfraIBanco());
			$ret = $objMdPetRelTpProcSerieBD->cadastrar($objMdPetRelTpProcSerieDTO);
	
			return $ret;
		} catch ( Exception $e ) {
			throw new InfraException ('Erro cadastrando Relacionamento do Tipo de Processo.', $e );
		}
	}
	
}
?>