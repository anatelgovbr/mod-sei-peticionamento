  <?
/**
* ANATEL
*
* 11/05/2016 - criado por jaqueline.mendes - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetTpProcessoOrientacoesRN extends InfraRN { 
	
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
	 * @param  $objMdPetTpProcessoOrientacoesDTO
	 * @return mixed
	 */
	protected function cadastrarControlado(MdPetTpProcessoOrientacoesDTO $objMdPetTpProcessoOrientacoesDTO) {
		try {
			// Valida Permissao
			SessaoSEI::getInstance ()->validarAuditarPermissao ('md_pet_tipo_processo_cadastrar_orientacoes', __METHOD__, $objMdPetTpProcessoOrientacoesDTO );

			// Regras de Negocio
			$objInfraException = new InfraException();
			
			$objInfraException->lancarValidacoes();
	
		    $objMdPetTpProcessoOrientacoesBD = new MdPetTpProcessoOrientacoesBD($this->getObjInfraIBanco());
			$objMdPetTpProcessoOrientacoesDTO = $objMdPetTpProcessoOrientacoesBD->cadastrar($objMdPetTpProcessoOrientacoesDTO);
			
		//	$rs = $this->getObjInfraIBanco ()->executarSql ( $sql );
	
			return $objMdPetTpProcessoOrientacoesDTO;
		} catch ( Exception $e ) {
			throw new InfraException ('Erro cadastrando Orientações do Tipo de Processo Peticionamento.', $e );
		}
	}
	
	/**
	 * Short description of method listarConectado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param  $objMdPetTpProcessoOrientacoesDTO
	 * @return mixed
	 */
	protected function listarConectado(MdPetTpProcessoOrientacoesDTO $objMdPetTpProcessoOrientacoesDTO) {
		try {
	
			//Valida Permissao
	
			//Regras de Negocio
			//$objInfraException = new InfraException();
	
			//$objInfraException->lancarValidacoes();
	
			$objMdPetTpProcessoOrientacoesBD = new MdPetTpProcessoOrientacoesBD($this->getObjInfraIBanco());
			$ret = $objMdPetTpProcessoOrientacoesBD->listar($objMdPetTpProcessoOrientacoesDTO);
	
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
	 * @param  $objMdPetTpProcessoOrientacoesDTO
	 * @return mixed
	 */
	protected function alterarControlado(MdPetTpProcessoOrientacoesDTO $objMdPetTpProcessoOrientacoesDTO) {
		try {
			// Valida Permissao
			SessaoSEI::getInstance ()->validarAuditarPermissao ('md_pet_tipo_processo_cadastrar_orientacoes', __METHOD__, $objMdPetTpProcessoOrientacoesDTO );

			// Regras de Negocio
			$objInfraException = new InfraException();
				
			$objInfraException->lancarValidacoes();
	
			$objMdPetTpProcessoOrientacoesBD = new MdPetTpProcessoOrientacoesBD($this->getObjInfraIBanco());
			$ret = $objMdPetTpProcessoOrientacoesBD->alterar($objMdPetTpProcessoOrientacoesDTO);
			
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
	
			$objMdPetIndisponibilidadeAnexoBD = new MdPetIndisponibilidadeAnexoBD($this->getObjInfraIBanco());
			for($i=0;$i<count($arrObjIndisponibilidadePeticionamentoAnexoDTO);$i++){
				$objMdPetIndisponibilidadeAnexoBD->excluir($arrObjIndisponibilidadePeticionamentoAnexoDTO[$i]);
			}
	
			//Auditoria
	
		}catch(Exception $e){
			throw new InfraException('Erro excluindo Anexo.',$e);
		}
	}
	
}
?>
  
 