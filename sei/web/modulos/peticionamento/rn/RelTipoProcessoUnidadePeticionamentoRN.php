<?
/**
* ANATEL
*
* 04/08/2016 - criado por jaqueline.mendes@castgroup.com.br - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class RelTipoProcessoUnidadePeticionamentoRN extends InfraRN{ 

	
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
	 * @author Jaqueline Mendes <jaqueline.mendes@castgroup.com.br>
	 * @param  $objTipoProcessoPeticionamentoDTO
	 * @return mixed
	 */
	protected function listarConectado(RelTipoProcessoUnidadePeticionamentoDTO $objRelTipoProcessoUnidadePeticionamentoDTO) {
	
		try {
	
			//Regras de Negocio
			$objInfraException = new InfraException();
	
			$objInfraException->lancarValidacoes();
			
			$objRelTipoProcessoUnidadePeticionamentoBD = new RelTipoProcessoUnidadePeticionamentoBD($this->getObjInfraIBanco());
			$ret = $objRelTipoProcessoUnidadePeticionamentoBD->listar($objRelTipoProcessoUnidadePeticionamentoDTO);
				
			return $ret;
		} catch (Exception $e) {
			throw new InfraException ('Erro listando Relacionamento de Tipo de Processo e Unidade Peticionamento.', $e);
		}
	}
	
	
/**
	 * Short description of method consultarConectado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@castgroup.com.br>
	 * @param  $objTipoProcessoPeticionamentoDTO
	 * @return mixed
	 */
	protected function consultarConectado(TipoProcessoPeticionamentoDTO $objTipoProcessoPeticionamentoDTO) {
		try {
			
			// Valida Permissao
			//SessaoSEI::getInstance ()->validarAuditarPermissao ('tipo_processo_peticionamento_alterar', __METHOD__, $objTipoProcessoPeticionamentoDTO );
			
		    $objTipoProcessoPeticionamentoBD = new TipoProcessoPeticionamentoBD($this->getObjInfraIBanco());
			$ret = $objTipoProcessoPeticionamentoBD->consultar($objTipoProcessoPeticionamentoDTO);
			
			return $ret;
		} catch ( Exception $e ) {
			throw new InfraException('Erro consultando Tipo de Processo Peticionamento.', $e);
		}
	}
	
	
	/**
	 * Short description of method cadastrarControlado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@castgroup.com.br>
	 * @param  $objRelTipoProcessoUnidadePeticionamentoDTO
	 * @return mixed
	 */
	protected function cadastrarControlado(RelTipoProcessoUnidadePeticionamentoDTO $objRelTipoProcessoUnidadePeticionamentoDTO) {
		try {
			// Valida Permissao
			$objRelTipoProcessoUnidadePeticionamentoBD = new RelTipoProcessoUnidadePeticionamentoBD($this->getObjInfraIBanco());
			$ret = $objRelTipoProcessoUnidadePeticionamentoBD->cadastrar($objRelTipoProcessoUnidadePeticionamentoDTO);
	
			return $ret;
		} catch ( Exception $e ) {
			throw new InfraException ('Erro cadastrando Relacionamento do Tipo de Processo.', $e );
		}
	}
	
	/**
	 * Short description of method excluirControlado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@castgroup.com.br>
	 * @param  $arrRelTipoProcessoPeticionamentoDTO
	 * @return void
	 */
	protected function excluirControlado($arrRelTipoProcessoUnidadePeticionamentoDTO) {
	
		try {
	
			$objRelTipoProcessoUnidadePeticionamentoBD = new RelTipoProcessoUnidadePeticionamentoBD($this->getObjInfraIBanco());
			for($i = 0; $i < count($arrRelTipoProcessoUnidadePeticionamentoDTO); $i ++) {
				$objRelTipoProcessoUnidadePeticionamentoBD->excluir($arrRelTipoProcessoUnidadePeticionamentoDTO[$i]);
			}
	
		} catch(Exception $e) {
			throw new InfraException ('Erro excluindo Tipo de Processo Peticionamento.', $e );
		}
	}
}
?>