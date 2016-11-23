<?
/**
* ANATEL
*
* 01/07/2016 - criado por marcelo.bezerra - CAST
*
*/

require_once dirname(__FILE__).'/../../../SEI.php';

class ReciboDocumentoAnexoPeticionamentoRN extends InfraRN { 
	
	public function __construct() {
		parent::__construct ();
	}
	
	public static $TP_PRINCIPAL = 'P';
    public static $TP_ESSENCIAL = 'E';
    public static $TP_COMPLEMENTAR = 'C';
	
	protected function inicializarObjInfraIBanco() {
		return BancoSEI::getInstance ();
	}
	
	/**
	 * Short description of method listarConectado
	 *
	 * @access protected
	 * @author Marcelo Bezerra <marcelo.bezerra@cast.com.br>
	 * @param $objDTO
	 * @return mixed
	 */
	protected function listarConectado(ReciboDocumentoAnexoPeticionamentoDTO $objDTO) {
	
		try {
							
			$objInfraException = new InfraException();						
			$objBD = new ReciboDocumentoAnexoPeticionamentoBD($this->getObjInfraIBanco());
			$ret = $objBD->listar($objDTO);	
			return $ret;
			
		} catch (Exception $e) {
			throw new InfraException ('Erro listando Recibo Anexo Peticionamento.', $e);
		}
	}
	
	/**
	 * Short description of method consultarConectado
	 *
	 * @access protected
	 * @author Marcelo Bezerra <marcelo.bezerra@cast.com.br>
	 * @param $objDTO
	 * @return mixed
	 */
	protected function consultarConectado(ReciboDocumentoAnexoPeticionamentoDTO $objDTO) {
	
		try {
	
			$objBD = new ReciboDocumentoAnexoPeticionamentoBD($this->getObjInfraIBanco());
			$ret = $objBD->consultar( $objDTO );
			return $ret;
				
		} catch (Exception $e) {
			throw new InfraException ('Erro listando Recibo Anexo Peticionamento.', $e);
		}
	}
	
	protected function cadastrarControlado(ReciboDocumentoAnexoPeticionamentoDTO $objDTO) {
		
		try {
			
			$objInfraException = new InfraException();
			$objBD = new ReciboDocumentoAnexoPeticionamentoBD($this->getObjInfraIBanco());
			$ret = $objBD->cadastrar($objDTO);	
			return $ret;
			
		} catch ( Exception $e ) {
			throw new InfraException ('Erro cadastrando Menu.', $e );
		}
	}
	
}
?>