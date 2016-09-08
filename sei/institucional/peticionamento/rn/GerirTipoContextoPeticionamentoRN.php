<?
/**
* ANATEL
*
* 29/06/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class GerirTipoContextoPeticionamentoRN extends InfraRN {
	
	public function __construct() {
		parent::__construct ();
	}
	
	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance ();
	}
	
	
	/**
	 * Short description of method excluirControlado
	 *
	 * @access protected
	 * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
	 * @param $objDTO
	 * @return void
	 */
	protected function excluirControlado($objDTO){
		
		try {
	
			//Valida Permissao
			SessaoSEI::getInstance ()->validarAuditarPermissao ('gerir_tipo_contexto_peticionamento_cadastrar', __METHOD__, $objDTO );
			
			$objExtArqPermBD = new RelTipoContextoPeticionamentoBD($this->getObjInfraIBanco());
			for($i=0;$i<count($objDTO);$i++){
				$objExtArqPermBD->excluir($objDTO[$i]);
			}
	
			//Auditoria
	
		}catch(Exception $e){
			throw new InfraException('Erro excluindo Extensão.',$e);
		}
	}
	
	/**
	 * Short description of method listarConectado
	 *
	 * @access protected
	 * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
	 * @param $objDTO
	 * @return mixed
	 */
	protected function listarConectado(RelTipoContextoPeticionamentoDTO  $objDTO) {
	
		try {
	
			//Regras de Negocio
			$objRelTipoContextoPeticionamentoBD = new RelTipoContextoPeticionamentoBD($this->getObjInfraIBanco());
			$ret = $objRelTipoContextoPeticionamentoBD->listar($objDTO);				
			return $ret;
			
		} catch (Exception $e) {
			throw new InfraException ('Erro listando Tipo de Interessado.', $e);
		}
	}
	
	
	/**
	 * Short description of method consultarConectado
	 *
	 * @access protected
	 * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
	 * @param  $objDTO
	 * @return mixed
	 */
	protected function consultarConectado(RelTipoContextoPeticionamentoDTO  $objDTO) {
		
		try {
			
			// Valida Permissao			
		    $objTamanhoArquivoBD = new RelTipoContextoPeticionamentoBD($this->getObjInfraIBanco());
			$ret = $objTamanhoArquivoBD->consultar($objTamanhoArquivoDTO);			
			return $ret;
			
		} catch ( Exception $e ) {
			throw new InfraException('Erro consultando Tipo de Interessado.', $e);
		}
	}
	
	/**
	 * Short description of method cadastrarControlado
	 *
	 * @access protected
	 * @author Marcelo Bezerra <marcelo.bezerra@castgroup.com.br>
	 * @param  $objDTO
	 * @return mixed
	 */
	protected function cadastrarControlado(RelTipoContextoPeticionamentoDTO  $objDTO) {
		
		try {
			// Valida Permissao
			SessaoSEI::getInstance ()->validarAuditarPermissao ('gerir_tipo_contexto_peticionamento_cadastrar', __METHOD__, $objDTO );	
			$objExtArqPermBD = new RelTipoContextoPeticionamentoBD($this->getObjInfraIBanco());
			$ret = $objExtArqPermBD->cadastrar($objDTO);
			return $ret;
			
		} catch ( Exception $e ) {
			throw new InfraException ('Erro cadastrando Tipo de Interessado.', $e );
		}
	}
	
}
?>