<?
/**
* ANATEL
*
* 24/06/2016 - criado por marcelo.bezerra - CAST
* 26/08/2024 - Atualização por gabrielg.colab - SPASSU 
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class MdPetTipoProcedimentoRN extends InfraRN { 
	
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
	 * @param $objTipoProcedimento
	 * @return mixed
	 */
	protected function listarConectado( TipoProcedimentoDTO $objDTO) {
	
		try {
	
			//Regras de Negocio
			//$objInfraException = new InfraException();
	
			//$objInfraException->lancarValidacoes();
			
			$objTipoProcedimentoBD = new TipoProcedimentoBD($this->getObjInfraIBanco());
			$ret = $objTipoProcedimentoBD->listar($objDTO);
				
			return $ret;
		} catch (Exception $e) {
			throw new InfraException ('Erro listando Tipo de Processo Peticionamento.', $e);
		}
	}
	
	
	
/**
	 * Short description of method consultarConectado
	 *
	 * @access protected
	 * @author Jaqueline Mendes <jaqueline.mendes@cast.com.br>
	 * @param  $objTipoProcedimentoDTO
	 * @return mixed
	 */
	protected function consultarConectado(TipoProcedimentoDTO $objDTO) {
		
		try {
			
			//SessaoSEIExterna::getInstance()->validarAuditarPermissao('tipo_procedimento_consultar',__METHOD__,$objDTO);
			
			// Valida Permissao		
		    $objBD = new TipoProcedimentoBD($this->getObjInfraIBanco());
			$ret = $objBD->consultar($objDTO);			
			return $ret;
			
		} catch ( Exception $e ) {
			throw new InfraException('Erro consultando Tipo de Processo Peticionamento.', $e);
		}
	}
		
}
?>