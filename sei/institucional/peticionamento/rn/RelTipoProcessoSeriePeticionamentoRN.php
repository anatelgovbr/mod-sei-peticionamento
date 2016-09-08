<?
/**
* ANATEL
*
* 18/05/2016 - criado por jaqueline.mendes - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class RelTipoProcessoSeriePeticionamentoRN extends InfraRN{ 

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
	 * @param  $objTipoProcessoPeticionamentoDTO
	 * @return mixed
	 */
	protected function listarConectado(RelTipoProcessoSeriePeticionamentoDTO $objTipoProcessoSeriePeticionamentoDTO) {
	
		try {
	
			//Regras de Negocio
			$objInfraException = new InfraException();
	
			$objInfraException->lancarValidacoes();
			
			$objTipoProcessoSeriePeticionamentoBD = new RelTipoProcessoSeriePeticionamentoBD($this->getObjInfraIBanco());
			$ret = $objTipoProcessoSeriePeticionamentoBD->listar($objTipoProcessoSeriePeticionamentoDTO);
				
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
	 * @param  $objTipoProcessoPeticionamentoDTO
	 * @return mixed
	 */
	protected function consultarConectado(TipoProcessoPeticionamentoDTO $objTipoProcessoPeticionamentoDTO) {
		try {
			
			// Valida Permissao
			SessaoSEI::getInstance ()->validarAuditarPermissao ('tipo_processo_peticionamento_alterar', __METHOD__, $objTipoProcessoPeticionamentoDTO );
			
		    $objTipoProcessoPeticionamentoBD = new TipoProcessoPeticionamentoBD($this->getObjInfraIBanco());
			$ret = $objTipoProcessoPeticionamentoBD->consultar($objTipoProcessoPeticionamentoDTO);
			
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
	 * @param  $arrTipoProcessoPeticionamentoDTO
	 * @return void
	 */
	protected function desativarControlado($arrTipoProcessoPeticionamentoDTO) {
	
		try {
				
			SessaoSEI::getInstance ()->validarAuditarPermissao('tipo_processo_peticionamento_desativar');
				
			$objTipoProcessoPeticionamentoBD = new TipoProcessoPeticionamentoBD($this->getObjInfraIBanco());
			for($i = 0; $i < count($arrTipoProcessoPeticionamentoDTO); $i ++) {
				$objTipoProcessoPeticionamentoBD->desativar($arrTipoProcessoPeticionamentoDTO[$i]);
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
	 * @param  $arrTipoProcessoPeticionamentoDTO
	 * @return void
	 */
	protected function reativarControlado($arrTipoProcessoPeticionamentoDTO) {
	
		try {
	
			SessaoSEI::getInstance ()->validarAuditarPermissao('tipo_processo_peticionamento_reativar');
	
			$objTipoProcessoPeticionamentoBD = new TipoProcessoPeticionamentoBD($this->getObjInfraIBanco());
			for($i = 0; $i < count($arrTipoProcessoPeticionamentoDTO); $i ++) {
				$objTipoProcessoPeticionamentoBD->reativar($arrTipoProcessoPeticionamentoDTO[$i]);
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
	 * @param  $arrRelTipoProcessoPeticionamentoDTO
	 * @return void
	 */
	protected function excluirControlado($arrRelTipoProcessoPeticionamentoDTO) {
	
		try {
	
			$objRelTipoProcessoPeticionamentoBD = new RelTipoProcessoSeriePeticionamentoBD($this->getObjInfraIBanco());
			for($i = 0; $i < count($arrRelTipoProcessoPeticionamentoDTO); $i ++) {
				$objRelTipoProcessoPeticionamentoBD->excluir($arrRelTipoProcessoPeticionamentoDTO[$i]);
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
	 * @param  $objRelTipoProcessoSeriePeticionamentoDTO
	 * @return mixed
	 */
	protected function cadastrarControlado(RelTipoProcessoSeriePeticionamentoDTO $objRelTipoProcessoSeriePeticionamentoDTO) {
		try {
			// Valida Permissao
			$objRelTipoProcessoSeriePeticionamentoBD = new RelTipoProcessoSeriePeticionamentoBD($this->getObjInfraIBanco());
			$ret = $objRelTipoProcessoSeriePeticionamentoBD->cadastrar($objRelTipoProcessoSeriePeticionamentoDTO);
	
			return $ret;
		} catch ( Exception $e ) {
			throw new InfraException ('Erro cadastrando Relacionamento do Tipo de Processo.', $e );
		}
	}
	
}
?>