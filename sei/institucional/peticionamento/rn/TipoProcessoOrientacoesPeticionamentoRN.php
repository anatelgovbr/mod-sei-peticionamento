  <?
/**
* ANATEL
*
* 11/05/2016 - criado por jaqueline.mendes - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class TipoProcessoOrientacoesPeticionamentoRN extends InfraRN { 
	
	public static $ID_FIXO_TP_PROCESSO_ORIENTACOES = 1;
	
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
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param  $objTipoProcessoOrientacoesPeticionamentoDTO
	 * @return mixed
	 */
	protected function cadastrarControlado(TipoProcessoOrientacoesPeticionamentoDTO $objTipoProcessoOrientacoesPeticionamentoDTO) {
		try {
			// Valida Permissao
			SessaoSEI::getInstance ()->validarAuditarPermissao ('tipo_processo_peticionamento_cadastrar_orientacoes', __METHOD__, $objTipoProcessoOrientacoesPeticionamentoDTO );
				
			// Regras de Negocio
			$objInfraException = new InfraException();
			
			$objInfraException->lancarValidacoes();
	
		    $objTipoProcessoOrientacoesPeticionamentoBD = new TipoProcessoOrientacoesPeticionamentoBD($this->getObjInfraIBanco());
			$objTipoProcessoOrientacoesPeticionamentoDTO = $objTipoProcessoOrientacoesPeticionamentoBD->cadastrar($objTipoProcessoOrientacoesPeticionamentoDTO);
			
		//	$rs = $this->getObjInfraIBanco ()->executarSql ( $sql );
	
			return $objTipoProcessoOrientacoesPeticionamentoDTO;
		} catch ( Exception $e ) {
			throw new InfraException ('Erro cadastrando Orientações do Tipo de Processo Peticionamento.', $e );
		}
	}
	
	/**
	 * Short description of method listarConectado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param  $objTipoProcessoOrientacoesPeticionamentoDTO
	 * @return mixed
	 */
	protected function listarConectado(TipoProcessoOrientacoesPeticionamentoDTO $objTipoProcessoOrientacoesPeticionamentoDTO) {
		try {
	
			//Valida Permissao
	
			//Regras de Negocio
			//$objInfraException = new InfraException();
	
			//$objInfraException->lancarValidacoes();
	
			$objTipoProcessoOrientacoesPeticionamentoBD = new TipoProcessoOrientacoesPeticionamentoBD($this->getObjInfraIBanco());
			$ret = $objTipoProcessoOrientacoesPeticionamentoBD->listar($objTipoProcessoOrientacoesPeticionamentoDTO);
	
			//Auditoria
	
			return $ret;
	
		}catch(Exception $e){
			throw new InfraException('Erro listando Orientações do Tipo de Processo.',$e);
		}
	}
	
	
	/**
	 * Short description of method cadastrarControlado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param  $objTipoProcessoOrientacoesPeticionamentoDTO
	 * @return mixed
	 */
	protected function alterarControlado(TipoProcessoOrientacoesPeticionamentoDTO $objTipoProcessoOrientacoesPeticionamentoDTO) {
		try {
			// Valida Permissao
			SessaoSEI::getInstance ()->validarAuditarPermissao ('tipo_processo_peticionamento_cadastrar_orientacoes', __METHOD__, $objTipoProcessoOrientacoesPeticionamentoDTO );
	
			// Regras de Negocio
			$objInfraException = new InfraException();
				
			$objInfraException->lancarValidacoes();
	
			$objTipoProcessoOrientacoesPeticionamentoBD = new TipoProcessoOrientacoesPeticionamentoBD($this->getObjInfraIBanco());
			$ret = $objTipoProcessoOrientacoesPeticionamentoBD->alterar($objTipoProcessoOrientacoesPeticionamentoDTO);
			
			return $ret;
		} catch ( Exception $e ) {
			throw new InfraException ('Erro alterando Orientações do Tipo de Processo Peticionamento.', $e );
		}
	}
	
	protected function excluirControlado($arrObjIndisponibilidadePeticionamentoAnexoDTO){
		try {
			//Valida Permissao
			//SessaoSEI::getInstance()->validarAuditarPermissao('anexo_excluir',__METHOD__,$arrObjAnexoDTO);
	
			//Regras de Negocio
			//$objInfraException = new InfraException();
	
			//$objInfraException->lancarValidacoes();
	
			$objIndisponibilidadeAnexoPeticionamentoBD = new IndisponibilidadeAnexoPeticionamentoBD($this->getObjInfraIBanco());
			for($i=0;$i<count($arrObjIndisponibilidadePeticionamentoAnexoDTO);$i++){
				$objIndisponibilidadeAnexoPeticionamentoBD->excluir($arrObjIndisponibilidadePeticionamentoAnexoDTO[$i]);
			}
	
			//Auditoria
	
		}catch(Exception $e){
			throw new InfraException('Erro excluindo Anexo.',$e);
		}
	}
	
}
?>
  
 